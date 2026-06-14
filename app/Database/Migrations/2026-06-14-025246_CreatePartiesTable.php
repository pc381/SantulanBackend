<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePartiesTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'user_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'client_local_id' => [
                'type'       => 'INT',
                'constraint' => 11,
            ],
            'client_contact_snapshot_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => true,
            ],
            'name' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
            ],
            'primary_phone' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
            ],
            'type' => [
                'type'       => 'VARCHAR',
                'constraint' => 50, // buyer, seller, both
            ],
            'notes' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'aadhaar_number' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
            ],
            'gst_number' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
            ],
            'aadhaar_card_path' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],
            'address_proof_path' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],
            'is_favorite' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 0,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'deleted_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addUniqueKey(['user_id', 'client_local_id']);
        $this->forge->createTable('parties');
    }

    public function down()
    {
        $this->forge->dropTable('parties');
    }
}
