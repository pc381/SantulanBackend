<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePartyContactsTable extends Migration
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
            'customer_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'client_local_id' => [
                'type'       => 'INT',
                'constraint' => 11,
            ],
            'client_party_id' => [
                'type'       => 'INT',
                'constraint' => 11,
            ],
            'client_contact_snapshot_id' => [
                'type'       => 'INT',
                'constraint' => 11,
            ],
            'designation' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],
            'notes' => [
                'type' => 'TEXT',
                'null' => true,
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
        $this->forge->addForeignKey('customer_id', 'customers', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addUniqueKey(['customer_id', 'client_local_id']);
        $this->forge->createTable('party_contacts');
    }

    public function down()
    {
        $this->forge->dropTable('party_contacts');
    }
}
