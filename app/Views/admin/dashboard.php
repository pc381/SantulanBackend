<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Santulan CRM & Admin Dashboard</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg-color: #0F172A;
            --sidebar-bg: #1E293B;
            --card-bg: rgba(30, 41, 59, 0.6);
            --border-color: rgba(255, 255, 255, 0.08);
            --primary: #2563EB;
            --primary-light: rgba(37, 99, 235, 0.1);
            --text-main: #F8FAFC;
            --text-muted: #94A3B8;
            --success: #10B981;
            --warning: #F59E0B;
            --danger: #EF4444;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif;
        }

        body {
            background-color: var(--bg-color);
            color: var(--text-main);
            min-height: 100vh;
            display: flex;
            overflow-x: hidden;
        }

        /* Sidebar styling */
        .sidebar {
            width: 280px;
            background-color: var(--sidebar-bg);
            border-right: 1px solid var(--border-color);
            padding: 30px 20px;
            display: flex;
            flex-direction: column;
            position: fixed;
            height: 100vh;
            z-index: 100;
        }

        .sidebar-brand {
            font-size: 24px;
            font-weight: 700;
            background: linear-gradient(135deg, #3B82F6 0%, #2563EB 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            letter-spacing: -0.5px;
            margin-bottom: 40px;
            padding-left: 10px;
        }

        .sidebar-menu {
            list-style: none;
            display: flex;
            flex-direction: column;
            gap: 8px;
            flex: 1;
        }

        .menu-item a {
            display: flex;
            align-items: center;
            padding: 14px 16px;
            color: var(--text-muted);
            text-decoration: none;
            border-radius: 12px;
            font-weight: 500;
            font-size: 15px;
            transition: all 0.2s ease;
        }

        .menu-item.active a, .menu-item a:hover {
            color: var(--text-main);
            background-color: var(--primary-light);
        }

        .menu-item.active a {
            border-left: 4px solid var(--primary);
            border-radius: 0 12px 12px 0;
            background-color: rgba(37, 99, 235, 0.15);
        }

        .sidebar-footer {
            border-top: 1px solid var(--border-color);
            padding-top: 20px;
        }

        .logout-btn {
            display: flex;
            align-items: center;
            padding: 14px 16px;
            color: var(--danger);
            text-decoration: none;
            border-radius: 12px;
            font-weight: 600;
            font-size: 15px;
            transition: background 0.2s;
            width: 100%;
            border: none;
            background: transparent;
            cursor: pointer;
        }

        .logout-btn:hover {
            background: rgba(239, 68, 68, 0.1);
        }

        /* Main Content Styling */
        .main-content {
            margin-left: 280px;
            flex: 1;
            padding: 40px;
            max-width: 1300px;
        }

        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }

        .page-title h1 {
            font-size: 26px;
            font-weight: 700;
        }

        .page-title p {
            font-size: 14px;
            color: var(--text-muted);
            margin-top: 4px;
        }

        /* Stats Cards */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 20px;
            margin-bottom: 40px;
        }

        .stat-card {
            background: var(--card-bg);
            backdrop-filter: blur(12px);
            border: 1px solid var(--border-color);
            border-radius: 20px;
            padding: 24px;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        }

        .stat-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            color: var(--text-muted);
            font-size: 13px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 12px;
        }

        .stat-value {
            font-size: 32px;
            font-weight: 700;
            color: var(--text-main);
        }

        /* Flash Message Alerts */
        .alert-success {
            background: rgba(16, 185, 129, 0.1);
            border: 1px solid rgba(16, 185, 129, 0.2);
            color: #A7F3D0;
            padding: 16px;
            border-radius: 12px;
            margin-bottom: 30px;
            font-size: 15px;
        }
        .alert-error {
            background: rgba(239, 68, 68, 0.1);
            border: 1px solid rgba(239, 68, 68, 0.2);
            color: #FECACA;
            padding: 16px;
            border-radius: 12px;
            margin-bottom: 30px;
            font-size: 15px;
        }

        /* Table Card */
        .table-card {
            background: var(--card-bg);
            backdrop-filter: blur(12px);
            border: 1px solid var(--border-color);
            border-radius: 24px;
            overflow: hidden;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
        }

        .table-header {
            padding: 24px 30px;
            border-bottom: 1px solid var(--border-color);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .table-title {
            font-size: 18px;
            font-weight: 600;
        }

        .requests-table {
            width: 100%;
            border-collapse: collapse;
            text-align: left;
        }

        .requests-table th, .requests-table td {
            padding: 14px 20px;
            border-bottom: 1px solid var(--border-color);
        }

        .requests-table th {
            color: var(--text-muted);
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            background: rgba(15, 23, 42, 0.4);
        }

        .requests-table tbody tr {
            transition: background 0.2s;
        }

        .requests-table tbody tr:hover {
            background: rgba(255, 255, 255, 0.02);
        }

        .requests-table td {
            font-size: 14px;
            color: var(--text-main);
            vertical-align: middle;
        }

        /* Status Badges */
        .badge {
            display: inline-flex;
            align-items: center;
            padding: 6px 12px;
            border-radius: 30px;
            font-size: 12px;
            font-weight: 600;
            text-transform: capitalize;
        }

        .badge-pending {
            background-color: rgba(245, 158, 11, 0.1);
            color: var(--warning);
            border: 1px solid rgba(245, 158, 11, 0.2);
        }

        .badge-approved {
            background-color: rgba(16, 185, 129, 0.1);
            color: var(--success);
            border: 1px solid rgba(16, 185, 129, 0.2);
        }

        .badge-rejected {
            background-color: rgba(239, 68, 68, 0.1);
            color: var(--danger);
            border: 1px solid rgba(239, 68, 68, 0.2);
        }

        /* Action Buttons */
        .actions-cell {
            display: flex;
            gap: 10px;
        }

        .action-btn {
            padding: 8px 16px;
            border: none;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
            white-space: nowrap; /* Prevent button text wrapping */
        }

        .btn-approve {
            background: rgba(16, 185, 129, 0.15);
            color: var(--success);
            border: 1px solid rgba(16, 185, 129, 0.3);
        }

        .btn-approve:hover {
            background: var(--success);
            color: #fff;
        }

        .btn-reject {
            background: rgba(239, 68, 68, 0.15);
            color: var(--danger);
            border: 1px solid rgba(239, 68, 68, 0.3);
        }

        .btn-reject:hover {
            background: var(--danger);
            color: #fff;
        }

        .empty-state {
            text-align: center;
            padding: 50px;
            color: var(--text-muted);
            font-size: 15px;
        }
    </style>
</head>
<body>

    <!-- Sidebar -->
    <div class="sidebar">
        <div class="sidebar-brand">SANTULAN</div>
        <ul class="sidebar-menu">
            <li class="menu-item active">
                <a href="<?php echo base_url('admin/dashboard'); ?>">Customers (CRM)</a>
            </li>
            <li class="menu-item">
                <a href="<?php echo base_url('admin/logs'); ?>">Observability Logs</a>
            </li>
            <li class="menu-item">
                <a href="<?php echo base_url('admin/change-password'); ?>">Change Password</a>
            </li>
        </ul>
        <div class="sidebar-footer">
            <a href="<?php echo base_url('admin/logout'); ?>" class="logout-btn">
                <span>Sign Out</span>
            </a>
        </div>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <div class="page-header">
            <div class="page-title">
                <h1>Customer CRM Dashboard</h1>
                <p>Welcome back, Administrator</p>
            </div>
        </div>

        <!-- Stats Overview -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-header">Pending Customers</div>
                <div class="stat-value"><?php echo $stats['pending_count']; ?></div>
            </div>
            <div class="stat-card">
                <div class="stat-header">Approved Customers</div>
                <div class="stat-value"><?php echo $stats['approved_count']; ?></div>
            </div>
            <div class="stat-card">
                <div class="stat-header">Total Ledger Records</div>
                <div class="stat-value"><?php echo $stats['total_transactions']; ?></div>
            </div>
            <div class="stat-card">
                <div class="stat-header">Client Logs Logged</div>
                <div class="stat-value"><?php echo $stats['total_events']; ?></div>
            </div>
        </div>

        <!-- Flash messages -->
        <?php if (session()->getFlashdata('success')): ?>
            <div class="alert-success">
                <?php echo htmlspecialchars(session()->getFlashdata('success')); ?>
            </div>
        <?php endif; ?>
        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert-error">
                <?php echo htmlspecialchars(session()->getFlashdata('error')); ?>
            </div>
        <?php endif; ?>

        <!-- Create Customer Card -->
        <div style="background: var(--card-bg); backdrop-filter: blur(12px); border: 1px solid var(--border-color); border-radius: 20px; padding: 24px; margin-bottom: 30px; box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);">
            <h2 style="font-size: 16px; font-weight: 600; margin-bottom: 16px; color: var(--text-main);">Create New Customer (CRM)</h2>
            <form action="<?php echo base_url('admin/customer/create'); ?>" method="post" style="display: flex; gap: 12px; align-items: center; max-width: 500px;">
                <input type="text" name="name" placeholder="Enter Customer / Company Name" style="flex: 1; background: rgba(255,255,255,0.04); border: 1px solid var(--border-color); color: var(--text-main); padding: 10px 16px; border-radius: 10px; font-size: 14px; font-weight: 500;" required />
                <button type="submit" class="action-btn btn-approve" style="padding: 10px 20px; border-radius: 10px; font-size: 14px;">Create & Approve</button>
            </form>
        </div>

        <!-- CRM Customer Table -->
        <div class="table-card">
            <div class="table-header">
                <div class="table-title">Customer Accounts & Associated Users</div>
            </div>
            <div style="overflow-x: auto; -webkit-overflow-scrolling: touch;">
                <table class="requests-table">
                <thead>
                    <tr>
                        <th style="width: 80px;">ID</th>
                        <th style="width: 320px;">Customer / Company Name</th>
                        <th>Registered Users</th>
                        <th style="width: 140px;">Registered</th>
                        <th style="width: 120px;">Status</th>
                        <th style="width: 300px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($customers)): ?>
                        <tr>
                            <td colspan="6" class="empty-state">No customer registrations found.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($customers as $cust): ?>
                        <tr>
                            <td>#<?php echo $cust['id']; ?></td>
                            <td>
                                <!-- Customer Inline Rename Form -->
                                <form action="<?php echo base_url('admin/customer/update/' . $cust['id']); ?>" method="post" style="display: flex; gap: 8px; align-items: center;">
                                    <input type="text" name="name" value="<?php echo htmlspecialchars($cust['name']); ?>" style="background: rgba(255,255,255,0.04); border: 1px solid var(--border-color); color: var(--text-main); padding: 8px 12px; border-radius: 8px; font-size: 14px; font-weight: 600; width: 220px;" required />
                                    <button type="submit" class="action-btn btn-approve" style="padding: 6px 12px; font-size: 11px; border-radius: 6px;">Rename</button>
                                </form>
                            </td>
                            <td>
                                <div style="display: flex; flex-direction: column; gap: 6px;">
                                    <?php if (empty($cust['users'])): ?>
                                        <span style="color: var(--text-muted); font-size: 13px; font-style: italic;">No users registered</span>
                                    <?php else: ?>
                                        <?php foreach ($cust['users'] as $u): ?>
                                            <div style="font-size: 13px; display: flex; gap: 8px; align-items: center;">
                                                <span style="font-weight: 500; color: #E2E8F0;"><?php echo htmlspecialchars($u['email']); ?></span>
                                                <?php if (!empty($u['email_verified_at'])): ?>
                                                    <span style="color: var(--success); font-size: 11px;">✔ Verified</span>
                                                <?php else: ?>
                                                    <span style="color: var(--warning); font-size: 11px;">⚠ Unverified</span>
                                                <?php endif; ?>
                                                <?php if ($u['is_admin']): ?>
                                                    <span style="background: var(--primary); font-size: 10px; padding: 2px 6px; border-radius: 4px; color: white; font-weight: bold;">ADMIN</span>
                                                <?php endif; ?>
                                            </div>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td><?php echo date('Y-m-d H:i', strtotime($cust['created_at'])); ?></td>
                            <td>
                                <span class="badge badge-<?php echo $cust['status']; ?>">
                                    <?php echo $cust['status']; ?>
                                </span>
                            </td>
                            <td class="actions-cell">
                                <div style="display: flex; gap: 6px; align-items: center;">
                                    <a href="<?php echo base_url('admin/customer/' . $cust['id']); ?>" class="action-btn" style="background: rgba(37, 99, 235, 0.15); color: #60A5FA; border: 1px solid rgba(37, 99, 235, 0.3); text-decoration: none; display: inline-flex; align-items: center; justify-content: center; height: 34px;">Manage / Support</a>
                                    <?php if ($cust['status'] === 'pending' || $cust['status'] === 'rejected'): ?>
                                        <form action="<?php echo base_url('admin/approve/' . $cust['id']); ?>" method="post" style="display:inline; margin: 0;">
                                            <button type="submit" class="action-btn btn-approve" style="height: 34px;">Approve</button>
                                        </form>
                                    <?php endif; ?>
                                    
                                    <?php if ($cust['status'] === 'pending' || $cust['status'] === 'approved'): ?>
                                        <form action="<?php echo base_url('admin/reject/' . $cust['id']); ?>" method="post" style="display:inline; margin: 0;">
                                            <button type="submit" class="action-btn btn-reject" style="height: 34px;">Block / Disable</button>
                                        </form>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
            </div>
        </div>
    </div>

</body>
</html>
