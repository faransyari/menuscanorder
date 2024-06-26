<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateOrderItemsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'order_item_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => TRUE,
                'auto_increment' => TRUE
            ],
            'order_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => TRUE,
            ],
            'item_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => TRUE,
            ],
            'item_name' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
            'quantity' => [
                'type' => 'INT',
                'constraint' => 11,
            ],
            'price' => [
                'type' => 'DECIMAL',
                'constraint' => '10,2',
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => TRUE, // Allowing NULL for the creation timestamp until it's set explicitly
            ],
        ]);

        $this->forge->addKey('order_item_id', TRUE);

        // Adding foreign keys with CASCADE on delete and update to maintain referential integrity
        $this->forge->addForeignKey('order_id', 'orders', 'order_id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('item_id', 'menu_items', 'item_id', 'CASCADE', 'CASCADE');

        $this->forge->createTable('order_items', TRUE);
    }

    public function down()
    {
        // Drop table with foreign keys in a safe manner
        $this->forge->dropTable('order_items', TRUE);
    }
}
