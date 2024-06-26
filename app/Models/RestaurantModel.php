<?php

namespace App\Models;

use CodeIgniter\Model;

class RestaurantModel extends Model
{
    protected $table = 'restaurants';
    protected $primaryKey = 'restaurant_id';
    protected $allowedFields = ['manager_id', 'name', 'address', 'created_at'];

    // Disable automatic timestamps
    protected $useTimestamps = false;

    // Define validation rules if needed
    protected $validationRules = [
        'name' => 'required|min_length[3]|max_length[255]',
        'address' => 'required|min_length[3]|max_length[255]',
    ];
    protected $validationMessages = [
        'name' => [
            'required' => 'Restaurant name is required',
            'min_length' => 'Restaurant name must be at least 3 characters long',
            'max_length' => 'Restaurant name must not exceed 255 characters',
        ],
        'address' => [
            'required' => 'Restaurant address is required',
            'min_length' => 'Restaurant address must be at least 3 characters long',
            'max_length' => 'Restaurant address must not exceed 255 characters',
        ],
    ];
}
