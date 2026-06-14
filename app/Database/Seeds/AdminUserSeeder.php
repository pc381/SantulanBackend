<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class AdminUserSeeder extends Seeder
{
    public function run()
    {
        $db = \Config\Database::connect();
        
        // Ensure admin customer exists
        $custBuilder = $db->table('customers');
        $customer = $custBuilder->where('name', 'Santulan Administration')->get()->getRow();
        
        if (!$customer) {
            $custBuilder->insert([
                'name'       => 'Santulan Administration',
                'status'     => 'approved',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
            $customerId = $db->insertID();
        } else {
            $customerId = $customer->id;
        }

        $builder = $db->table('users');
        $exists = $builder->where('email', 'admin@santulan.com')->countAllResults() > 0;

        if (!$exists) {
            $builder->insert([
                'customer_id'   => $customerId,
                'email'         => 'admin@santulan.com',
                'password_hash' => password_hash('AdminPassword123', PASSWORD_BCRYPT),
                'google_id'     => null,
                'is_admin'      => 1,
                'created_at'    => date('Y-m-d H:i:s'),
                'updated_at'    => date('Y-m-d H:i:s'),
            ]);
            echo "Admin user seeded successfully!\n";
        } else {
            echo "Admin user already exists.\n";
        }
    }
}
