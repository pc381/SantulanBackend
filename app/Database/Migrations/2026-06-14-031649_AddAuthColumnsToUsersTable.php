<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddAuthColumnsToUsersTable extends Migration
{
    public function up()
    {
        // 1. Add columns to users table
        $this->forge->addColumn('users', [
            'password_hash' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
                'after'      => 'google_id',
            ],
            'verification_token' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
                'after'      => 'password_hash',
            ],
            'email_verified_at' => [
                'type'  => 'DATETIME',
                'null'  => true,
                'after' => 'verification_token',
            ],
            'reset_token' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
                'after'      => 'email_verified_at',
            ],
            'reset_token_expires_at' => [
                'type'  => 'DATETIME',
                'null'  => true,
                'after' => 'reset_token',
            ],
            'is_admin' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 0,
                'after'      => 'reset_token_expires_at',
            ],
        ]);

        // 2. Create user_tokens table
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
            'token' => [
                'type'       => 'VARCHAR',
                'constraint' => 191,
                'unique'     => true,
            ],
            'expires_at' => [
                'type' => 'DATETIME',
            ],
            'created_at' => [
                'type' => 'DATETIME',
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('user_tokens');
    }

    public function down()
    {
        // Drop user_tokens table first
        $this->forge->dropTable('user_tokens', true);

        // Drop added columns from users table
        $this->forge->dropColumn('users', [
            'password_hash',
            'verification_token',
            'email_verified_at',
            'reset_token',
            'reset_token_expires_at',
            'is_admin',
        ]);
    }
}
