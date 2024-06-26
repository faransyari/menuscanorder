<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateOrdersTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'order_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => TRUE,
                'auto_increment' => TRUE
            ],
            'customer_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => TRUE,
            ],
            'restaurant_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => TRUE,
            ],
            'table_number' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => TRUE,
            ],
            'status' => [
                'type' => 'ENUM',
                'constraint' => ['placed', 'in progress', 'completed', 'cancelled'],
                'default' => 'placed',  // Define a default status for new orders
            ],
            'total_price' => [
                'type' => 'DECIMAL',
                'constraint' => '10,2',
                'default' => '0.00',  // It's often useful to have a default value for monetary amounts
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => TRUE,
            ],
        ]);

        $this->forge->addKey('order_id', TRUE);
        $this->forge->addForeignKey('customer_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('restaurant_id', 'restaurants', 'restaurant_id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('orders', TRUE);
    }

    public function down()
    {
        $this->forge->dropTable('orders', TRUE);
    }
}
