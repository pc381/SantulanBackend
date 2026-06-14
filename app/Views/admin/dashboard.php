<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Santulan Admin Dashboard</title>
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
            padding: 18px 30px;
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

        .user-email {
            font-weight: 500;
        }

        .user-provider {
            color: var(--text-muted);
            font-size: 13px;
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

        /* Verification Indicator */
        .verify-status {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 13px;
        }
        .dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            display: inline-block;
        }
        .dot-verified { background-color: var(--success); }
        .dot-unverified { background-color: var(--warning); }

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
                <a href="#">Registration Requests</a>
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
                <h1>Registration Dashboard</h1>
                <p>Welcome back, Administrator</p>
            </div>
        </div>

        <!-- Stats Overview -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-header">Pending Approvals</div>
                <div class="stat-value"><?php echo $stats['pending_count']; ?></div>
            </div>
            <div class="stat-card">
                <div class="stat-header">Active Accounts</div>
                <div class="stat-value"><?php echo $stats['approved_count']; ?></div>
            </div>
            <div class="stat-card">
                <div class="stat-header">Synced Ledgers</div>
                <div class="stat-value"><?php echo $stats['total_transactions']; ?></div>
            </div>
            <div class="stat-card">
                <div class="stat-header">Logged Events</div>
                <div class="stat-value"><?php echo $stats['total_events']; ?></div>
            </div>
        </div>

        <!-- Flash messages -->
        <?php if (session()->getFlashdata('success')): ?>
            <div class="alert-success">
                <?php echo htmlspecialchars(session()->getFlashdata('success')); ?>
            </div>
        <?php endif; ?>

        <!-- Requests Table -->
        <div class="table-card">
            <div class="table-header">
                <div class="table-title">User Approvals & Registrations</div>
            </div>
            <table class="requests-table">
                <thead>
                    <tr>
                        <th>User Email</th>
                        <th>Auth Provider</th>
                        <th>Email Verification</th>
                        <th>Registered Date</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($users)): ?>
                        <tr>
                            <td colspan="6" class="empty-state">No user registration requests found.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($users as $user): ?>
                        <tr>
                            <td class="user-email"><?php echo htmlspecialchars($user['email']); ?></td>
                            <td>
                                <span class="user-provider">
                                    <?php echo !empty($user['google_id']) ? 'Google OAuth' : 'Email/Password'; ?>
                                </span>
                            </td>
                            <td>
                                <div class="verify-status">
                                    <?php if (!empty($user['email_verified_at'])): ?>
                                        <span class="dot dot-verified"></span>
                                        <span>Verified (<?php echo date('M d, H:i', strtotime($user['email_verified_at'])); ?>)</span>
                                    <?php else: ?>
                                        <span class="dot dot-unverified"></span>
                                        <span>Unverified</span>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td><?php echo date('Y-m-d H:i:s', strtotime($user['created_at'])); ?></td>
                            <td>
                                <span class="badge badge-<?php echo $user['status']; ?>">
                                    <?php echo $user['status']; ?>
                                </span>
                            </td>
                            <td class="actions-cell">
                                <?php if ($user['status'] === 'pending' || $user['status'] === 'rejected'): ?>
                                    <form action="<?php echo base_url('admin/approve/' . $user['id']); ?>" method="post" style="display:inline;">
                                        <button type="submit" class="action-btn btn-approve">Approve</button>
                                    </form>
                                <?php endif; ?>
                                
                                <?php if ($user['status'] === 'pending' || $user['status'] === 'approved'): ?>
                                    <form action="<?php echo base_url('admin/reject/' . $user['id']); ?>" method="post" style="display:inline;">
                                        <button type="submit" class="action-btn btn-reject">Reject</button>
                                    </form>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</body>
</html>
