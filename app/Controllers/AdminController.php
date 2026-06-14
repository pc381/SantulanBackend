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

        // 1. Fetch Customers and their associated Users
        $customers = $db->table('customers')
                        ->orderBy('created_at', 'DESC')
                        ->get()
                        ->getResultArray();

        foreach ($customers as &$customer) {
            $customer['users'] = $db->table('users')
                                     ->where('customer_id', $customer['id'])
                                     ->get()
                                     ->getResultArray();
        }

        // 2. Compute stats metrics
        $stats = [
            'pending_count'      => $db->table('customers')->where('status', 'pending')->countAllResults(),
            'approved_count'     => $db->table('customers')->where('status', 'approved')->countAllResults(),
            'total_transactions' => $db->table('transactions')->countAllResults(),
            'total_events'       => $db->table('observability_events')->countAllResults(),
        ];

        return view('admin/dashboard', [
            'customers' => $customers,
            'stats'     => $stats
        ]);
    }

    /**
     * POST /admin/approve/{customerId}
     */
    public function approve(int $customerId)
    {
        if (!session()->get('admin_logged_in')) {
            return redirect()->to('admin/login');
        }

        $db = \Config\Database::connect();
        
        $db->table('customers')
           ->where('id', $customerId)
           ->update([
               'status'     => 'approved',
               'updated_at' => date('Y-m-d H:i:s')
           ]);

        session()->setFlashdata('success', 'Customer registration approved successfully!');
        return redirect()->to('admin/dashboard');
    }

    /**
     * POST /admin/reject/{customerId}
     */
    public function reject(int $customerId)
    {
        if (!session()->get('admin_logged_in')) {
            return redirect()->to('admin/login');
        }

        $db = \Config\Database::connect();
        
        $db->table('customers')
           ->where('id', $customerId)
           ->update([
               'status'     => 'rejected',
               'updated_at' => date('Y-m-d H:i:s')
           ]);

        session()->setFlashdata('success', 'Customer registration rejected / disabled.');
        return redirect()->to('admin/dashboard');
    }

    /**
     * POST /admin/customer/update/{customerId}
     */
    public function updateCustomer(int $customerId)
    {
        if (!session()->get('admin_logged_in')) {
            return redirect()->to('admin/login');
        }

        $name = $this->request->getPost('name');
        if (empty($name)) {
            session()->setFlashdata('error', 'Customer name cannot be empty.');
            return redirect()->to('admin/dashboard');
        }

        $db = \Config\Database::connect();
        $db->table('customers')
           ->where('id', $customerId)
           ->update([
               'name'       => $name,
               'updated_at' => date('Y-m-d H:i:s')
           ]);

        session()->setFlashdata('success', 'Customer details updated successfully!');
        return redirect()->to('admin/dashboard');
    }

    /**
     * GET /admin/logs
     */
    public function logs()
    {
        if (!session()->get('admin_logged_in')) {
            return redirect()->to('admin/login');
        }

        $db = \Config\Database::connect();
        $logs = $db->table('observability_events')
                   ->select('observability_events.*, users.email as user_email, customers.name as customer_name')
                   ->join('users', 'users.id = observability_events.user_id', 'left')
                   ->join('customers', 'customers.id = observability_events.customer_id', 'left')
                   ->orderBy('observability_events.created_at', 'DESC')
                   ->limit(200)
                   ->get()
                   ->getResultArray();

        return view('admin/logs', [
            'logs' => $logs
        ]);
    }

    /**
     * GET /admin/logout
     */
    public function logout()
    {
        session()->destroy();
        return redirect()->to('admin/login');
    }

    /**
     * POST /admin/customer/create
     */
    public function createCustomer()
    {
        if (!session()->get('admin_logged_in')) {
            return redirect()->to('admin/login');
        }

        $name = $this->request->getPost('name');
        if (empty($name)) {
            session()->setFlashdata('error', 'Customer name cannot be empty.');
            return redirect()->to('admin/dashboard');
        }

        $db = \Config\Database::connect();
        $db->table('customers')->insert([
            'name'       => $name,
            'status'     => 'approved',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        session()->setFlashdata('success', 'Customer created successfully!');
        return redirect()->to('admin/dashboard');
    }

    /**
     * POST /admin/customer/billing/{customerId}
     */
    public function updateBilling(int $customerId)
    {
        if (!session()->get('admin_logged_in')) {
            return redirect()->to('admin/login');
        }

        $adminNotes = $this->request->getPost('admin_notes');
        $billingDetails = $this->request->getPost('billing_details');

        $db = \Config\Database::connect();
        $db->table('customers')
           ->where('id', $customerId)
           ->update([
               'admin_notes'     => $adminNotes,
               'billing_details' => $billingDetails,
               'updated_at'      => date('Y-m-d H:i:s')
           ]);

        session()->setFlashdata('success', 'Billing and internal notes updated successfully!');
        return redirect()->to('admin/customer/' . $customerId);
    }

    /**
     * GET /admin/customer/{customerId}
     */
    public function viewCustomer(int $customerId)
    {
        if (!session()->get('admin_logged_in')) {
            return redirect()->to('admin/login');
        }

        $db = \Config\Database::connect();

        $customer = $db->table('customers')
                       ->where('id', $customerId)
                       ->get()
                       ->getRowArray();

        if (!$customer) {
            session()->setFlashdata('error', 'Customer not found.');
            return redirect()->to('admin/dashboard');
        }

        // Fetch users
        $users = $db->table('users')
                    ->where('customer_id', $customerId)
                    ->get()
                    ->getResultArray();

        // Fetch contacts snapshots
        $contacts = $db->table('contact_snapshots')
                       ->where('customer_id', $customerId)
                       ->orderBy('display_name', 'ASC')
                       ->get()
                       ->getResultArray();

        // Fetch parties
        $parties = $db->table('parties')
                      ->where('customer_id', $customerId)
                      ->orderBy('name', 'ASC')
                      ->get()
                      ->getResultArray();

        // Fetch transactions
        $transactions = $db->table('transactions')
                           ->where('customer_id', $customerId)
                           ->orderBy('transaction_date', 'DESC')
                           ->get()
                           ->getResultArray();

        return view('admin/customer_detail', [
            'customer'     => $customer,
            'users'        => $users,
            'contacts'     => $contacts,
            'parties'      => $parties,
            'transactions' => $transactions
        ]);
    }

    /**
     * GET /admin/change-password
     */
    public function changePasswordView()
    {
        if (!session()->get('admin_logged_in')) {
            return redirect()->to('admin/login');
        }
        return view('admin/change_password');
    }

    /**
     * POST /admin/change-password
     */
    public function changePassword()
    {
        if (!session()->get('admin_logged_in')) {
            return redirect()->to('admin/login');
        }

        $newPassword = $this->request->getPost('new_password');
        $confirmPassword = $this->request->getPost('confirm_password');

        if (!$newPassword || strlen($newPassword) < 6) {
            session()->setFlashdata('error', 'New password must be at least 6 characters long.');
            return redirect()->to('admin/change-password');
        }

        if ($newPassword !== $confirmPassword) {
            session()->setFlashdata('error', 'Passwords do not match. Please verify your typing.');
            return redirect()->to('admin/change-password');
        }

        $db = \Config\Database::connect();
        $adminId = session()->get('admin_id');

        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

        $db->table('users')
           ->where('id', $adminId)
           ->update(['password_hash' => $hashedPassword]);

        session()->setFlashdata('success', 'Admin password changed successfully!');
        return redirect()->to('admin/change-password');
    }
}
