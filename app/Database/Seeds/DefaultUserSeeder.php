<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class DefaultUserSeeder extends Seeder
{
    public function run()
    {
        $db = \Config\Database::connect();
        
        // Ensure default customer exists
        $custBuilder = $db->table('customers');
        $customer = $custBuilder->where('name', 'Default Company')->get()->getRow();
        
        if (!$customer) {
            $custBuilder->insert([
                'name'       => 'Default Company',
                'status'     => 'approved',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
            $customerId = $db->insertID();
        } else {
            $customerId = $customer->id;
        }

        $builder = $db->table('users');
        $exists = $builder->where('email', 'default@santulan.com')->countAllResults() > 0;
        
        if (!$exists) {
            $builder->insert([
                'customer_id'   => $customerId,
                'email'         => 'default@santulan.com',
                'google_id'     => null,
                'created_at'    => date('Y-m-d H:i:s'),
                'updated_at'    => date('Y-m-d H:i:s'),
            ]);
        }
    }
}
