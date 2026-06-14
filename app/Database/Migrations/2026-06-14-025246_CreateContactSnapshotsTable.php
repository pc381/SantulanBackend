<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateContactSnapshotsTable extends Migration
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
            'device_contact_id' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
            ],
            'display_name' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
            ],
            'primary_phone' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
            ],
            'phones_json' => [
                'type' => 'TEXT',
            ],
            'last_seen_at' => [
                'type' => 'DATETIME',
            ],
            'phone_deleted_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'restored_at' => [
                'type' => 'DATETIME',
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
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('customer_id', 'customers', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addUniqueKey(['customer_id', 'client_local_id']);
        $this->forge->createTable('contact_snapshots');
    }

    public function down()
    {
        $this->forge->dropTable('contact_snapshots');
    }
}
