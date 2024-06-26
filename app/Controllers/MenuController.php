<?php namespace App\Controllers;

use CodeIgniter\Controller;
use App\Models\MenuItemModel;
use App\Models\CategoryModel;
use App\Models\OrderModel;
use App\Models\OrderItemsModel;
use App\Models\TableModel;


class MenuController extends BaseController
{
    protected $menuItemModel;

    public function __construct()
    {
        helper('url'); 
        $this->menuItemModel = new MenuItemModel();
        $this->categoryModel = new CategoryModel();
        $this->orderModel = new OrderModel();
        $this->orderItemsModel = new OrderItemsModel();
        $this->tableModel = new TableModel();
    }

    /**
     * Display the landing page.
     *
     * @return \CodeIgniter\HTTP\ResponseInterface The response object.
     */
    public function index()
    {
        return view('landing_page');
    }

    /**
     * Displays the menu for a specific restaurant table.
     *
     * This function handles the logic for displaying the menu to a user, ensuring that the user is logged in,
     * and that the provided restaurant ID and table number are valid. If the user is not logged in, they are
     * redirected to the login page. If the restaurant ID or table number are missing or invalid, appropriate
     * error messages are set and the user is redirected to the landing page.
     *
     * If the inputs are valid, the restaurant ID and table number are stored in the session, and the menu items
     * for the specified restaurant are retrieved and displayed, grouped by categories.
     *
     * @return \CodeIgniter\HTTP\RedirectResponse|\CodeIgniter\HTTP\Response
     *     Returns a redirect response to the login page or landing page if there are errors,
     *     otherwise returns the menu view with the retrieved menu items and categories.
     */
    public function menu() {
        if (!auth()->loggedIn()) {
            return redirect()->to('/login');
        }

        $restaurantId = $this->request->getGet('restaurant_id');
        $tableNumber = $this->request->getGet('table');
        $category = $this->categoryModel->where('restaurant_id', $restaurantId)->findAll();

        if (!$restaurantId || !$tableNumber) {
            session()->setFlashdata('error', 'Restaurant ID and table number are required.');
            return redirect()->to('/');
        }

        if (!$this->tableModel->where('restaurant_id', $restaurantId)->where('table_number', $tableNumber)->first()) {
            session()->setFlashdata('error', 'Table number not found.');
            return redirect()->to('/');
        }

        session()->set('table_number', $tableNumber);
        session()->set('restaurant_id', $restaurantId);

        $menuItems = $this->menuItemModel->getItemsWithCategories($restaurantId);

        return view('menu', ['restaurantId' => $restaurantId, 'menuItems' => $menuItems, 'tableNumber' => $tableNumber, 'category' => $category]);
    }

    /**
     * Displays the menu management interface for a restaurant.
     *
     * This function ensures that the user is logged in and has a valid restaurant context stored in the session.
     * If the user is not logged in, they are redirected to the login page. If the restaurant context is not found
     * in the session, an error message is set and the user is redirected to the landing page.
     *
     * If the restaurant context is valid, the function retrieves all menu items and categories for the specified
     * restaurant, and prepares the data to be displayed in the menu management view.
     *
     * @return \CodeIgniter\HTTP\RedirectResponse|\CodeIgniter\HTTP\Response
     *     Returns a redirect response to the login page or landing page if there are errors,
     *     otherwise returns the menu management view with the retrieved menu items and categories.
     */
    public function menu_management()
    {
        if (!auth()->loggedIn()) {
            return redirect()->to('/login');
        }

        $restaurantId = session()->get('restaurant_id');
        if (!$restaurantId) {
            session()->setFlashdata('error', 'No restaurant context found.');
            return redirect()->to('/');
        }

        $menuItems = $this->menuItemModel->where('restaurant_id', $restaurantId)->findAll();
        $category = $this->categoryModel->where('restaurant_id', $restaurantId)->findAll();


        $data = [
            'menuItems' => $menuItems,
            'categories' => $category,
            'restaurantId' => $restaurantId
        ];

        return view('menu_management', $data);
    }

    /**
     * Adds a new menu item to the restaurant's menu.
     *
     * This function handles the logic for adding a new menu item. It first checks if the restaurant context 
     * is available in the session. If not, it redirects the user to the menu management page with an error message.
     *
     * It then gathers the post data excluding the restaurant ID, adds the restaurant ID from the session to the data 
     * array, and processes the image file if provided. The image is saved to the server with a random name, and its 
     * filename is added to the data array.
     *
     * The function attempts to insert the new menu item into the database. If the insertion is successful, 
     * it redirects the user to the menu management page with a success message. If there are validation errors, 
     * it redirects the user back to the form with the input data and error messages.
     *
     * @return \CodeIgniter\HTTP\RedirectResponse
     *     Returns a redirect response to the menu management page with a success or error message.
     */
    public function addMenuItem()
    {
        $restaurantId = session()->get('restaurant_id');
        if (!$restaurantId) {
            // Redirect with error if no restaurant_id found in session
            return redirect()->to('/menu_management')->with('error', 'Restaurant context is missing.');
        }

        // Gather post data excluding restaurant_id
        $data = $this->request->getPost([
            'name', 'description', 'price', 'category'
        ]);

        // Add restaurant_id from session to the data array
        $data['restaurant_id'] = $restaurantId;
        
        $imageFile = $this->request->getFile('image');
        if ($imageFile && $imageFile->isValid() && !$imageFile->hasMoved()) {
            $newImageName = $imageFile->getRandomName();
            $imageFile->move(FCPATH . 'uploads/menu_items', $newImageName);
            $data['image'] = $newImageName;
        }

        if ($this->menuItemModel->insert($data)) {
            return redirect()->to('/admin/menu_management')->with('message', 'Menu item successfully added.');
        } else {
            return redirect()->back()->withInput()->with('errors', $this->menuItemModel->errors());
        }
    }

    /**
     * Updates an existing menu item.
     *
     * This function handles the logic for updating an existing menu item. It first retrieves the item ID and
     * the updated data from the POST request, including fields such as name, description, price, category, and
     * is_featured.
     *
     * If an image file is provided, it checks if the file is valid and not already moved. If valid, the image is
     * saved to the server with a random name, and its filename is added to the data array.
     *
     * The function then attempts to update the menu item in the database with the provided data. If the update
     * is successful, it redirects the user to the menu management page with a success message. If there are
     * validation errors, it redirects the user back to the form with the input data and error messages.
     *
     * @return \CodeIgniter\HTTP\RedirectResponse
     *     Returns a redirect response to the menu management page with a success or error message.
     */
    public function updateMenuItem()
    {
        $itemId = $this->request->getPost('item_id');
        $data = $this->request->getPost([
            'name', 'description', 'price', 'category', 'is_featured'
        ]);

        if ($imageFile = $this->request->getFile('image')) {
            if ($imageFile->isValid() && !$imageFile->hasMoved()) {
                $newImageName = $imageFile->getRandomName();
                $imageFile->move(FCPATH . 'uploads/menu_items', $newImageName);
                $data['image'] = $newImageName;
            }
        }

        if ($this->menuItemModel->update($itemId, $data)) {
            return redirect()->to('/admin/menu_management')->with('message', 'Menu item updated successfully.');
        } else {
            return redirect()->back()->withInput()->with('errors', $this->menuItemModel->errors());
        }
    }
    
    /**
     * Toggles the featured status of a menu item.
     *
     * This function handles the logic for updating the featured status of a menu item. It retrieves the item ID 
     * and the new featured status from the POST request. The function ensures that the `MenuItemModel` is correctly 
     * instantiated using the appropriate namespace.
     *
     * The function calls the `update_feature_status` method on the `MenuItemModel` instance to update the featured status 
     * of the menu item. If the update is successful, it returns a JSON response with a status of 'success'. If the update 
     * fails, it logs an error message with specific details and returns a JSON response with a status of 'error'.
     *
     * @return \CodeIgniter\HTTP\Response
     *     Returns a JSON response indicating the success or failure of the update operation.
     */
    public function toggle_feature()
    {
        $item_id = $this->request->getPost('item_id');
        $is_featured = $this->request->getPost('is_featured');
    
        // Correctly instantiate the MenuItemModel
        $menuItemModel = new \App\Models\MenuItemModel();  // Ensure you use the correct namespace
    
        // Use the $menuItemModel instance to call update_feature_status
        if ($menuItemModel->update_feature_status($item_id, $is_featured)) {
            return $this->response->setJSON(['status' => 'success']);
        } else {
            // Log the failure with more specific details if available
            log_message('error', "Failed to update feature status for item_id: $item_id");
            return $this->response->setJSON(['status' => 'error']);
        }
    }

    /**
     * Handles the image upload process for a menu item.
     *
     * This private function checks if the provided image file is valid and has not already been moved. 
     * If the file is valid, it generates a random name for the image, moves the file to the specified 
     * upload directory, and returns the new image name. If the file is not valid or has already been moved, 
     * the function returns null.
     *
     * @param \CodeIgniter\Files\File $imageFile
     *     The uploaded image file to be handled.
     *
     * @return string|null
     *     Returns the new image name if the upload is successful, or null if the upload fails.
     */
    private function handleImageUpload($imageFile)
    {
        if ($imageFile && $imageFile->isValid() && !$imageFile->hasMoved()) {
            $newImageName = $imageFile->getRandomName();
            $imageFile->move(FCPATH . 'uploads/menu_items', $newImageName);
            return $newImageName;
        }
        return null;
    }

    /**
     * Deletes a menu item and its associated image.
     *
     * This function handles the deletion of a menu item identified by the given item ID. It first checks if the 
     * item ID is provided. If not, it redirects to the menu management page with an error message. The function 
     * then retrieves the item to get the image path. If the item is not found, it redirects with an error message.
     *
     * If the item exists and has an associated image file, the function checks if the image file exists and 
     * deletes it. It then attempts to delete the item using the model. If the deletion is successful, it redirects 
     * to the menu management page with a success message. If the deletion fails, it redirects with an error message.
     *
     * @param int|null $item_id
     *     The ID of the menu item to be deleted.
     *
     * @return \CodeIgniter\HTTP\RedirectResponse
     *     Returns a redirect response to the menu management page with a success or error message.
     */
    public function delete($item_id = null)
    {
        if (!$item_id) {
            return redirect()->to('/menu_management')->with('error', 'Invalid request to delete item.');
        }
    
        // Retrieve the item to get the image path
        $item = $this->menuItemModel->find($item_id);
        if (!$item) {
            return redirect()->to('admin/menu_management')->with('error', 'Item not found.');
        }
    
        // Delete the image file if it exists
        if ($item['image'] && file_exists(FCPATH . 'uploads/menu_items/' . $item['image'])) {
            unlink(FCPATH . 'uploads/menu_items/' . $item['image']);
        }
    
        // Attempt to delete the item using the model
        if ($this->menuItemModel->delete($item_id)) {
            // If the deletion is successful
            return redirect()->to('admin/menu_management')->with('message', 'Item and associated image successfully deleted.');
        } else {
            // If the deletion fails
            return redirect()->to('admin/menu_management')->with('error', 'Could not delete the item.');
        }
    }

    /**
     * Displays the orders for a specific restaurant.
     *
     * This function handles the logic for displaying orders for a restaurant. It first checks if the user is logged in.
     * If the user is not logged in, they are redirected to the login page. The function then checks if the restaurant 
     * context is available in the session. If not, it sets an error message and redirects to the landing page.
     *
     * If the restaurant context is valid, the function retrieves all menu items and orders for the specified restaurant.
     * It then iterates over the orders to retrieve the corresponding order items and organizes them into an order list.
     * The function logs the order list for debugging purposes.
     *
     * The function prepares the data to be displayed in the orders view, including menu items, order items, customer orders,
     * and the restaurant ID.
     *
     * @return \CodeIgniter\HTTP\RedirectResponse|\CodeIgniter\HTTP\Response
     *     Returns a redirect response to the login page or landing page if there are errors,
     *     otherwise returns the orders view with the retrieved data.
     */
    public function orders()
    {
        if (!auth()->loggedIn()) {
            return redirect()->to('/login');
        }

        $restaurantId = session()->get('restaurant_id');
        if (!$restaurantId) {
            session()->setFlashdata('error', 'No restaurant context found.');
            return redirect()->to('/');
        }

        $menuItems = $this->menuItemModel->where('restaurant_id', $restaurantId)->findAll();
        $order= $this->orderModel->where('restaurant_id', $restaurantId)->findAll();
        $orderList = [];
        foreach ($order as $items) {
            $orderItems = $this->orderItemsModel->where('order_id', $items['order_id'])->findAll();
            $orderList[$items['order_id']] = $orderItems;
        }
        
        log_message('info', 'Orders: ' . json_encode($orderList));

        $data = [
            'menuItems' => $menuItems,
            'orderItems' => $orderList,
            'customerOrders' => $order,
            'restaurantId' => $restaurantId
        ];

        return view('orders', $data);
    }

    /**
     * Displays the admin index page for a specific restaurant.
     *
     * This function handles the logic for displaying the admin index page. It first checks if the user is logged in.
     * If the user is not logged in, they are redirected to the login page. The function then checks if the restaurant 
     * context is available in the session. If not, it sets an error message and redirects to the landing page.
     *
     * If the restaurant context is valid, the function prepares the data to be displayed in the admin index view, 
     * including the restaurant ID.
     *
     * @return \CodeIgniter\HTTP\RedirectResponse|\CodeIgniter\HTTP\Response
     *     Returns a redirect response to the login page or landing page if there are errors,
     *     otherwise returns the admin index view with the restaurant ID.
     */
    public function admin_index()
    {
        if (!auth()->loggedIn()) {
            return redirect()->to('/login');
        }

        $restaurantId = session()->get('restaurant_id');
        if (!$restaurantId) {
            session()->setFlashdata('error', 'No restaurant context found.');
            return redirect()->to('/');
        }

        // Assuming method handles orders based on restaurantId
        return view('admin_index', ['restaurantId' => $restaurantId]);
    }

    /**
     * Adds a new category to the restaurant's menu.
     *
     * This function handles the logic for adding a new category to the restaurant's menu. It retrieves the category name
     * from the POST request and the restaurant ID from the session. If the category name is not provided, it redirects
     * back to the previous page with an error message.
     *
     * If the category name is valid, the function prepares the data for insertion, including the restaurant ID and
     * category name. It then attempts to insert the new category into the database. If the insertion is successful,
     * it redirects to the menu management page with a success message. If the insertion fails, it redirects to the
     * menu management page with an error message.
     *
     * @return \CodeIgniter\HTTP\RedirectResponse
     *     Returns a redirect response to the previous page or the menu management page with a success or error message.
     */
    public function add_category()
    {
        $categoryName = $this->request->getPost('categoryName');
        $restaurantId = session()->get('restaurant_id'); // Assume restaurant ID is stored in session

        if (!$categoryName) {
            return redirect()->back()->with('error', 'Category name is required.');
        }

        // Perform insertion
        $data = [
            'restaurant_id' => $restaurantId,
            'name' => $categoryName,
        ];

        if ($this->categoryModel->insert($data)) {
            return redirect()->to('admin/menu_management')->with('message', 'Category successfully added.');
        } else {
            return redirect()->to('admin/menu_management')->with('error', 'Failed to add category.');
        }
    }

    /**
     * Deletes a category from the restaurant's menu.
     *
     * This function handles the logic for deleting a category identified by the given category ID. It first checks if 
     * the category ID is provided. If not, it redirects to the menu management page with an error message.
     *
     * The function then attempts to delete the category using the model. If the deletion is successful, it redirects 
     * to the menu management page with a success message. If the deletion fails, it redirects with an error message.
     *
     * @param int|null $category_id
     *     The ID of the category to be deleted.
     *
     * @return \CodeIgniter\HTTP\RedirectResponse
     *     Returns a redirect response to the menu management page with a success or error message.
     */
    public function delete_category($category_id = null)
    {
        if (!$category_id) {
            return redirect()->to('admin/menu_management')->with('error', 'Invalid request to delete category.');
        }

        if ($this->categoryModel->delete($category_id)) {
            return redirect()->to('admin/menu_management')->with('message', 'Category successfully deleted.');
        } else {
            return redirect()->to('admin/menu_management')->with('error', 'Could not delete the category.');
        }
    }

    /**
     * Updates the status of an order.
     *
     * This function handles the logic for updating the status of an order. It retrieves the order ID and the new 
     * status from the POST request. If either the order ID or the status is missing, it returns a JSON response 
     * indicating an invalid request.
     *
     * The function then attempts to update the order status using the model. If the update is successful, it returns 
     * a JSON response indicating success. If the update fails, it returns a JSON response indicating failure.
     *
     * @return \CodeIgniter\HTTP\Response
     *     Returns a JSON response indicating the success or failure of the update operation.
     */
    public function update_order_status()
    {
        $orderId = $this->request->getPost('order_id');
        $status = $this->request->getPost('status');

        if (!$orderId || !$status) {
            return $this->response->setJSON(['success' => false, 'message' => 'Invalid request.']);
        }

        if ($this->orderModel->updateStatus($orderId, ['status' => $status])) {
            return $this->response->setJSON(['success' => true, 'message' => 'Order status updated.']);
        } else {
            return $this->response->setJSON(['success' => false, 'message' => 'Failed to update order status.']);
        }
    }

}