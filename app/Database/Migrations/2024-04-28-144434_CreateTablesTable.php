<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTablesTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'table_id' => [
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
            'table_number' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => TRUE,
                'null' => FALSE
            ],
            'qr_code' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => TRUE,  // Assuming the QR code might be set after table creation
                'comment' => 'Path to the QR code image'
            ],
            'completed' => [
                'type' => 'BOOLEAN',
                'default' => FALSE,
                'comment' => 'Whether the dining session is completed'
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => TRUE
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => TRUE
            ]
        ]);

        $this->forge->addKey('table_id', TRUE);
        $this->forge->addForeignKey('restaurant_id', 'restaurants', 'restaurant_id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('tables');

    }

    public function down()
    {
        $this->forge->dropTable('tables');
    }
}
