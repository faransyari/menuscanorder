<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateRestaurantsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'restaurant_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => TRUE,
                'auto_increment' => TRUE
            ],
            'manager_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => TRUE,
            ],
            'name' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
            'address' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],

        ]);
        $this->forge->addKey('restaurant_id', TRUE);
        $this->forge->addForeignKey('manager_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('restaurants', TRUE);
    }

    public function down()
    {
        $this->forge->dropTable('restaurants', TRUE);
    }
}
