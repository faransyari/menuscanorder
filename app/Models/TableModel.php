<?php

namespace App\Models;

use CodeIgniter\Model;

class TableModel extends Model
{
    protected $table = 'tables'; 
    protected $primaryKey = 'table_id'; 

    protected $allowedFields = ['table_number','restaurant_id', 'qr_code', 'completed', 'created_at', 'updated_at'];

    protected $useTimestamps = true; 
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    /**
     * Fetch a table by its number.
     * @param int $tableNumber The number of the table.
     * @return array|null The table data or null if not found.
     */
    public function getTableByNumber($tableNumber)
    {
        return $this->where('table_number', $tableNumber)->first();
    }

    /**
     * Mark a table as completed.
     * @param int $tableId The ID of the table.
     * @return bool Whether the operation was successful.
     */
    public function markTableCompleted($tableId)
    {
        return $this->update($tableId, ['completed' => true]);
    }
    
}
