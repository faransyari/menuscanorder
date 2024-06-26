<?php namespace App\Models;

use CodeIgniter\Model;

class MenuItemModel extends Model
{
    protected $returnType = 'array'; 
    protected $table = 'menu_items';
    protected $primaryKey = 'item_id';
    protected $allowedFields = ['restaurant_id', 'name', 'description','category', 'price', 'is_featured', 'image'];

    protected $useTimestamps = false;    
    protected $createdField  = 'created_at';
    protected $updatedField  = false;

    /**
     * Retrieves menu items with their categories for a specific restaurant.
     *
     * @param int $restaurantId The ID of the restaurant to retrieve items for.
     * @return array The menu items along with their category names.
     */
    public function getItemsWithCategories($restaurantId)
    {
        return $this->select('menu_items.*, category.name as category_name')
                    ->join('category', 'menu_items.category = category.name')
                    ->where('menu_items.restaurant_id', $restaurantId)
                    ->findAll();
    }

    /**
     * Updates the feature status of a menu item.
     *
     * @param int $item_id The ID of the menu item to update.
     * @param bool $is_featured The feature status to set.
     * @return bool True if the update was successful, false otherwise.
     */
    public function update_feature_status($item_id, $is_featured)
    {
        $data = ['is_featured' => $is_featured];
        return $this->update($item_id, $data);
    }

    /**
     * Retrieves the price of a menu item.
     *
     * @param int $item_id The ID of the menu item to retrieve the price for.
     * @return array The price of the menu item.
     */
    public function getItemPrice($item_id)
    {
        return $this->select('price')
                    ->where('item_id', $item_id)
                    ->get()
                    ->getRowArray();
    }

}
