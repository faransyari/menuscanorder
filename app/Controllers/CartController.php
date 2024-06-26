<?php namespace App\Controllers;

use CodeIgniter\Controller;
use App\Models\OrderModel;
use App\Models\MenuItemModel;

class CartController extends Controller
{
    public function __construct()
    {
        helper(['url', 'form']);
        $this->orderModel = new OrderModel();
        $this->menuItemModel = new MenuItemModel();

    }

    /**
     * Processes an order from the JSON data received in the request.
     * Validates items, calculates the total price, creates an order, and adds items to the order.
     *
     * @return \CodeIgniter\HTTP\ResponseInterface The JSON response indicating the success or failure of the order.
     */
    public function order()
    {
        $json = $this->request->getJSON();
        $items = $json->items ?? null; // Fetch the items object
    
        if (!$items) {
            return $this->response->setJSON(['success' => false, 'message' => 'No order data received.']);
        }
    
        $total_price = 0;
        $order_items = [];
        $valid_items = true;
    
        foreach ($items as $menuItemId => $quantity) {
            if (!$menuItemId || !$quantity) {
                log_message('error', 'Invalid menu item ID or quantity.');
                $valid_items = false;
                continue; // Skip this item or handle it according to your error policy
            }
            
            $menuItem = $this->menuItemModel->find($menuItemId);
            if (!$menuItem) {
                log_message('error', 'Menu item not found: ' . $menuItemId);
                $valid_items = false;
                continue; // Skip this item or handle it accordingly
            }
    
            $total_price += $menuItem['price'] * $quantity;
    
            // Store each valid item to be added to the order later
            $order_items[] = ['menuItem' => $menuItem, 'quantity' => $quantity];
        }
    
        // If any items were invalid, you might choose to fail the whole order
        if (!$valid_items) {
            return $this->response->setJSON(['success' => false, 'message' => 'Some items in the order were invalid.']);
        }
    
        // Assuming all validations passed, create the order
        $order_id = $this->orderModel->createOrder($total_price);
    
        // Add all validated items to the order
        foreach ($order_items as $item) {
            $this->orderModel->addOrderItem($order_id, $item['menuItem'], $item['quantity']);
        }
    
        // Process the order data
        return $this->response->setJSON(['success' => true, 'message' => 'Order received successfully.']);
    }
    



}
