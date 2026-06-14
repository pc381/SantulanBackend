<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class ScopeDataToUser extends Migration
{
    private array $tables = [
        'contact_snapshots',
        'parties',
        'party_contacts',
        'transactions',
        'transaction_attachments'
    ];

    public function up()
    {
        // 1. Add temporary index on customer_id so the foreign key constraint doesn't prevent dropping the unique index.
        foreach ($this->tables as $table) {
            $this->db->query("ALTER TABLE `{$table}` ADD INDEX `temp_{$table}_customer_id` (`customer_id`)");
        }

        // 2. Drop existing unique index 'customer_id_client_local_id' on all tables.
        foreach ($this->tables as $table) {
            $this->db->query("ALTER TABLE `{$table}` DROP INDEX `customer_id_client_local_id`");
        }

        // 3. Add nullable 'user_id' column to all five tables.
        foreach ($this->tables as $table) {
            $this->forge->addColumn($table, [
                'user_id' => [
                    'type'       => 'INT',
                    'constraint' => 11,
                    'unsigned'   => true,
                    'null'       => true,
                    'after'      => 'customer_id',
                ]
            ]);
        }

        // 4. Populate existing rows with the first user ID of their customer.
        foreach ($this->tables as $table) {
            $this->db->query("
                UPDATE `{$table}` t
                SET t.user_id = (
                    SELECT MIN(u.id)
                    FROM `users` u
                    WHERE u.customer_id = t.customer_id
                )
                WHERE t.user_id IS NULL
            ");

            // Clean up any orphaned rows where customer_id doesn't have any users,
            // which prevents NOT NULL constraint failure.
            $this->db->query("DELETE FROM `{$table}` WHERE `user_id` IS NULL");
        }

        // 5. Alter 'user_id' to NOT NULL and add Foreign Key constraint.
        foreach ($this->tables as $table) {
            $this->forge->modifyColumn($table, [
                'user_id' => [
                    'type'       => 'INT',
                    'constraint' => 11,
                    'unsigned'   => true,
                    'null'       => false,
                ]
            ]);

            $this->db->query("ALTER TABLE `{$table}` ADD CONSTRAINT `fk_{$table}_user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE");
        }

        // 6. Add new unique index constraint ('customer_id', 'user_id', 'client_local_id').
        foreach ($this->tables as $table) {
            $this->db->query("ALTER TABLE `{$table}` ADD UNIQUE KEY `customer_user_client_local` (`customer_id`, `user_id`, `client_local_id`)");
        }

        // 7. Drop the temporary index since 'customer_user_client_local' starts with customer_id and satisfies the foreign key.
        foreach ($this->tables as $table) {
            $this->db->query("ALTER TABLE `{$table}` DROP INDEX `temp_{$table}_customer_id`");
        }
    }

    public function down()
    {
        foreach ($this->tables as $table) {
            // Add temporary index on customer_id
            $this->db->query("ALTER TABLE `{$table}` ADD INDEX `temp_{$table}_customer_id` (`customer_id`)");

            // Drop new unique key
            $this->db->query("ALTER TABLE `{$table}` DROP INDEX `customer_user_client_local`");

            // Drop foreign key
            $this->db->query("ALTER TABLE `{$table}` DROP FOREIGN KEY `fk_{$table}_user_id`");

            // Drop column
            $this->forge->dropColumn($table, 'user_id');

            // Restore original unique key
            $this->db->query("ALTER TABLE `{$table}` ADD UNIQUE KEY `customer_id_client_local_id` (`customer_id`, `client_local_id`)");

            // Drop temporary index
            $this->db->query("ALTER TABLE `{$table}` DROP INDEX `temp_{$table}_customer_id`");
        }
    }
}
