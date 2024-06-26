<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateCategoryTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'category_id' => [
                'type'           => 'INT',
                'constraint'     => 5,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'restaurant_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => TRUE,
            ],
            'name' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
            ],
        ]);
        $this->forge->addKey('category_id', TRUE);
        $this->forge->addForeignKey('restaurant_id', 'restaurants', 'restaurant_id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('category');
    }

    public function down()
    {
        $this->forge->dropTable('category');
    }
}
