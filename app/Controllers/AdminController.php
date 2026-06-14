<?php

namespace App\Controllers;

use App\Controllers\BaseController;

class AdminController extends BaseController
{
    /**
     * GET /admin or /admin/login
     */
    public function login()
    {
        if (session()->get('admin_logged_in')) {
            return redirect()->to('admin/dashboard');
        }
        return view('admin/login');
    }

    /**
     * POST /admin/login
     */
    public function processLogin()
    {
        $email = $this->request->getPost('email');
        $password = $this->request->getPost('password');

        if (!$email || !$password) {
            session()->setFlashdata('error', 'Please fill in all fields.');
            return redirect()->to('admin/login');
        }

        $db = \Config\Database::connect();
        $builder = $db->table('users');

        // Check if user exists and is an admin
        $admin = $builder->where('email', $email)
                         ->where('is_admin', 1)
                         ->get()
                         ->getRow();

        if (!$admin || !password_verify($password, $admin->password_hash)) {
            session()->setFlashdata('error', 'Invalid admin email or password.');
            return redirect()->to('admin/login');
        }

        // Establish admin web session
        session()->set([
            'admin_logged_in' => true,
            'admin_id'        => $admin->id,
            'admin_email'     => $admin->email
        ]);

        return redirect()->to('admin/dashboard');
    }

    /**
     * GET /admin/dashboard
     */
    public function dashboard()
    {
        if (!session()->get('admin_logged_in')) {
            return redirect()->to('admin/login');
        }

        $db = \Config\Database::connect();

        // 1. Fetch normal users (non-admins)
        $users = $db->table('users')
                    ->where('is_admin', 0)
                    ->orderBy('created_at', 'DESC')
                    ->get()
                    ->getResultArray();

        // 2. Compute stats metrics
        $stats = [
            'pending_count'      => $db->table('users')->where('is_admin', 0)->where('status', 'pending')->countAllResults(),
            'approved_count'     => $db->table('users')->where('is_admin', 0)->where('status', 'approved')->countAllResults(),
            'total_transactions' => $db->table('transactions')->countAllResults(),
            'total_events'       => $db->table('observability_events')->countAllResults(),
        ];

        return view('admin/dashboard', [
            'users' => $users,
            'stats' => $stats
        ]);
    }

    /**
     * POST /admin/approve/{userId}
     */
    public function approve(int $userId)
    {
        if (!session()->get('admin_logged_in')) {
            return redirect()->to('admin/login');
        }

        $db = \Config\Database::connect();
        
        $db->table('users')
           ->where('id', $userId)
           ->update([
               'status'     => 'approved',
               'updated_at' => date('Y-m-d H:i:s')
           ]);

        session()->setFlashdata('success', 'User registration approved successfully!');
        return redirect()->to('admin/dashboard');
    }

    /**
     * POST /admin/reject/{userId}
     */
    public function reject(int $userId)
    {
        if (!session()->get('admin_logged_in')) {
            return redirect()->to('admin/login');
        }

        $db = \Config\Database::connect();
        
        $db->table('users')
           ->where('id', $userId)
           ->update([
               'status'     => 'rejected',
               'updated_at' => date('Y-m-d H:i:s')
           ]);

        session()->setFlashdata('success', 'User registration rejected / account disabled.');
        return redirect()->to('admin/dashboard');
    }

    /**
     * GET /admin/logout
     */
    public function logout()
    {
        session()->destroy();
        return redirect()->to('admin/login');
    }
}
