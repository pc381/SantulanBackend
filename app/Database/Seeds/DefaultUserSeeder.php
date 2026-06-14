<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class DefaultUserSeeder extends Seeder
{
    public function run()
    {
        $db = \Config\Database::connect();
        $builder = $db->table('users');
        
        $exists = $builder->where('id', 1)->countAllResults() > 0;
        if (!$exists) {
            $builder->insert([
                'id'         => 1,
                'email'      => 'default@santulan.com',
                'google_id'  => null,
                'status'     => 'approved',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
        }
    }
}
