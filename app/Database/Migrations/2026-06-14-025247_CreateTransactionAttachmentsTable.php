<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTransactionAttachmentsTable extends Migration
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
            'client_transaction_id' => [
                'type'       => 'INT',
                'constraint' => 11,
            ],
            'type' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
            ],
            'local_path' => [
                'type' => 'TEXT',
            ],
            'file_name' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
            ],
            'server_path' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],
            'created_at' => [
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
        $this->forge->createTable('transaction_attachments');
    }

    public function down()
    {
        $this->forge->dropTable('transaction_attachments');
    }
}
