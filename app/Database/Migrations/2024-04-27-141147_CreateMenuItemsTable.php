<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateMenuItemsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'item_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => TRUE,
                'auto_increment' => TRUE
            ],
            'restaurant_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => TRUE,
            ],
            'name' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
            ],
            'description' => [
                'type' => 'TEXT',
            ],
            'category' => [
                'type' => 'TEXT',
            ],
            'price' => [
                'type' => 'DECIMAL',
                'constraint' => '10,2',
            ],
            'is_featured' => [
                'type' => 'BOOLEAN',
            ],
            'image' => [
                'type' => 'VARCHAR',
                'constraint' => '255', // Assuming the image path will be stored here
            ],
            'created_at' => [
                'type' => 'DATETIME',
            ],
        ]);
        $this->forge->addKey('item_id', TRUE);
        $this->forge->addForeignKey('restaurant_id', 'restaurants', 'restaurant_id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('menu_items', TRUE);
    }

    public function down()
    {
        $this->forge->dropTable('menu_items', TRUE);
    }
}
