<?php namespace App\Models;

use CodeIgniter\Model;

class OrderModel extends Model
{
    protected $table = 'orders';
    protected $primaryKey = 'order_id';
    protected $allowedFields = ['customer_id', 'restaurant_id', 'status', 'total_price', 'created_at','table_number'];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = '';

    /**
     * Creates a new order and returns the ID of the created order.
     *
     * @param float $total_price The total price of the order.
     * @return int The ID of the newly created order.
     */
    public function createOrder($total_price)
    {
        $data = [
            'customer_id'   => session()->get('logged_in_user_id'), // Fetching customer ID from session directly
            'restaurant_id' => session()->get('restaurant_id'), // Fetching restaurant ID from session directly
            'status' => 'placed',
            'total_price' => $total_price,
            'table_number' => session()->get('table_number'),
        ];
    
        $this->insert($data);
        return $this->getInsertID();
    }
    
    /**
     * Adds an order item to the order.
     *
     * @param int $order_id The ID of the order to add the item to.
     * @param array $menuItem The menu item details.
     * @param int $quantity The quantity of the menu item.
     * @return bool True if the order item was added successfully, false otherwise.
     */
    public function addOrderItem($order_id, $menuItem, $quantity)
    {  
        $data = [
            'order_id' => $order_id,
            'item_id' => $menuItem['item_id'],
            'item_name' => $menuItem['name'],
            'quantity' => $quantity,
            'price' => $menuItem['price']
        ];
    
        if (!$this->db->table('order_items')->insert($data)) {
            log_message('error', 'Failed to insert order item: ' . json_encode($data));
            return false; // Indicate failure
        }
        return true; // Indicate success
    }

    /**
     * Updates the total price of an order.
     *
     * @param int $order_id The ID of the order to update.
     * @param float $total_price The new total price of the order.
     * @return bool True if the update was successful, false otherwise.
     */
    public function updateTotalPrice($order_id, $total_price)
    {        
        $data = ['total_price' => $total_price];
        return $this->update($order_id, $data);
    }

    /**
     * Updates the status of an order.
     *
     * @param int $order_id The ID of the order to update.
     * @param string $status The new status of the order.
     * @return bool True if the update was successful, false otherwise.
     */
    public function updateStatus($order_id, $status)
    {
        $data = ['status' => $status];
        return $this->update($order_id, $data);
    }
    
    
}


class OrderItemsModel extends Model
{
    protected $table = 'order_items';
    protected $primaryKey = 'order_item_id';
    protected $allowedFields = ['order_id', 'item_id', 'quantity', 'price'];
    protected $useTimestamps = false;
    protected $createdField = '';
}
