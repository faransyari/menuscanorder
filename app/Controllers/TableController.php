<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use App\Models\TableModel;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\LabelAlignment;
use Endroid\QrCode\Color\Color;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\RoundBlockSizeMode\RoundBlockSizeModeMargin;
use Endroid\QrCode\Builder\BuilderInterface;
use Endroid\QrCode\Builder\Builder;

class TableController extends Controller
{
    protected $TableModel;

    public function __construct()
    {
        helper('url'); 
        $this->TableModel = new TableModel();

    }

    /**
     * Displays the QR code generation interface for a restaurant.
     *
     * This function handles the logic for displaying the QR code generation interface. It first checks if the user is logged in.
     * If the user is not logged in, they are redirected to the login page. The function then checks if the restaurant 
     * context is available in the session. If not, it sets an error message and redirects to the landing page.
     *
     * If the restaurant context is valid, the function retrieves all tables for the specified restaurant. The data 
     * for the tables and the restaurant ID are prepared and passed to the view.
     *
     * @return \CodeIgniter\HTTP\RedirectResponse|\CodeIgniter\HTTP\Response
     *     Returns a redirect response to the login page or landing page if there are errors,
     *     otherwise returns the QR code generation view with the retrieved tables and restaurant ID.
     */
    public function view_generate_qr()
    {
        if (!auth()->loggedIn()) {
            return redirect()->to('/login');
        }

        $restaurantId = session()->get('restaurant_id');

        if (!$restaurantId) {
            session()->setFlashdata('error', 'No restaurant context found.');
            return redirect()->to('/');
        }

        $tables = $this->TableModel->where('restaurant_id', $restaurantId)->findAll();

        $data = [
            'tables' => $tables,
            'restaurantId' => $restaurantId
        ];

        // Assuming method handles orders based on restaurantId
        return view('generate_qr', $data);
    }

    /**
     * Generates a QR code for a specific table in a restaurant.
     *
     * This function handles the logic for generating a QR code for a specified table in a restaurant. It retrieves the
     * table number from the POST request and the restaurant ID from the session. If either the table number or restaurant 
     * ID is missing, it sets an error message and redirects back to the previous page.
     *
     * The function checks for an existing table with the same number in the same restaurant. If such a table exists, 
     * it sets an error message and redirects back to the previous page.
     *
     * If no existing table is found, the function generates a QR code containing a URL that includes the restaurant ID 
     * and table number. The QR code is saved to a specified file path. If the file save operation fails, it sets an error 
     * message and redirects back to the previous page.
     *
     * The function then inserts the new table data, including the table number, restaurant ID, and QR code path, into 
     * the database. If the insertion is successful, it sets a success message and redirects back to the previous page.
     *
     * @return \CodeIgniter\HTTP\RedirectResponse
     *     Returns a redirect response to the previous page with a success or error message.
     */
    public function generateQr()
    {
        $tableNumber = $this->request->getPost('tableNumber');
        $restaurantId = session()->get('restaurant_id');

        if (!$tableNumber || !$restaurantId) {
            session()->setFlashdata('error', 'Table number and restaurant context are required.');
            return redirect()->back();
        }

        // Check for existing table with the same number in the same restaurant
        $existingTable = $this->TableModel->where([
            'table_number' => $tableNumber,
            'restaurant_id' => $restaurantId
        ])->first();

        if ($existingTable) {
            session()->setFlashdata('error', 'A table with this number already exists for the restaurant.');
            return redirect()->back();
        }

        $qrCode = Builder::create()
            ->writer(new PngWriter())
            ->data('https://infs3202-14206650.uqcloud.net/menuscanorder/menu?restaurant_id=' . $restaurantId . '&table=' . $tableNumber)
            ->encoding(new Encoding('UTF-8'))
            ->size(300)
            ->margin(10)
            ->backgroundColor(new Color(255, 255, 255))
            ->foregroundColor(new Color(0, 0, 0))
            ->build();

        $outputPath = FCPATH . 'uploads/qr_codes/' . $restaurantId . '_table_' . $tableNumber . '.png';
        $qrCode->saveToFile($outputPath);

        if (!file_exists($outputPath)) {
            session()->setFlashdata('error', 'Failed to save QR code.');
            return redirect()->back();
        }

        $data = [
            'table_number' => $tableNumber,
            'restaurant_id' => $restaurantId,
            'qr_code' => $outputPath,
            'completed' => false
        ];

        $this->TableModel->insert($data);

        session()->setFlashdata('success', 'QR code generated successfully for table ' . $tableNumber . '.');
        return redirect()->back();
    }

    /**
     * Deletes a table and its associated QR code from the restaurant's system.
     *
     * This function handles the logic for deleting a table entry and its associated QR code file. It first checks 
     * if the table ID is provided. If not, it sets an error message and redirects back to the previous page.
     *
     * The function then loads the `TableModel` and retrieves the table entry using the provided table ID. If the table 
     * is not found, it sets an error message and redirects back to the previous page.
     *
     * If the table is found, the function constructs the file path for the QR code associated with the table. It attempts 
     * to delete the QR code file. If the file exists but cannot be deleted, it sets an error message and proceeds, optionally 
     * stopping the process based on your preference.
     *
     * The function then attempts to delete the table entry from the database. If the deletion fails, it sets an error message 
     * and redirects back to the previous page.
     *
     * If the table and QR code are successfully deleted, the function sets a success message and redirects back to the 
     * previous page.
     *
     * @param int|null $table_id
     *     The ID of the table to be deleted.
     *
     * @return \CodeIgniter\HTTP\RedirectResponse
     *     Returns a redirect response to the previous page with a success or error message.
     */
    public function delete($table_id = null)
    {
        if (!$table_id) {
            session()->setFlashdata('error', 'No table ID provided.');
            return redirect()->back();
        }
    
        // Load the TableModel
        $tableModel = new \App\Models\TableModel();
    
        // Retrieve the table entry
        $table = $tableModel->find($table_id);
        if (!$table) {
            session()->setFlashdata('error', 'Table not found.');
            return redirect()->back();
        }
    
        // Construct the file path
        $filePath = FCPATH . 'uploads/qr_codes/' . $table['restaurant_id'] . '_table_' . $table['table_number'] . '.png';
    
        // Attempt to delete the QR code file
        if (file_exists($filePath) && !unlink($filePath)) {
            session()->setFlashdata('error', 'Failed to delete QR code file.');
            // Optionally, decide if you want to stop the process if the file can't be deleted
            // return redirect()->back();
        }
    
        // Delete the database entry
        if (!$tableModel->delete($table_id)) {
            session()->setFlashdata('error', 'Failed to delete table record.');
            return redirect()->back();
        }
    
        // Set a success message
        session()->setFlashdata('success', 'Table and associated QR code successfully removed.');
        return redirect()->back();
    }

}
