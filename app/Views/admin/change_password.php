<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Change Admin Password - Santulan</title>
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
            max-width: 800px;
        }

        .page-header {
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

        /* Card container */
        .card {
            background: var(--card-bg);
            backdrop-filter: blur(12px);
            border: 1px solid var(--border-color);
            border-radius: 24px;
            padding: 30px;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
        }

        /* Form elements */
        .form-group {
            margin-bottom: 24px;
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        label {
            font-size: 13px;
            font-weight: 600;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .form-control {
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid var(--border-color);
            color: var(--text-main);
            padding: 14px 18px;
            border-radius: 12px;
            font-size: 14px;
            font-weight: 500;
            width: 100%;
            transition: border-color 0.2s, background-color 0.2s;
        }

        .form-control:focus {
            outline: none;
            border-color: var(--primary);
            background: rgba(255, 255, 255, 0.05);
        }

        .btn {
            padding: 14px 28px;
            border: none;
            border-radius: 12px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
            width: 100%;
        }

        .btn-primary {
            background: var(--primary);
            color: #fff;
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.2);
        }

        .btn-primary:hover {
            background: #1d4ed8;
            box-shadow: 0 6px 16px rgba(37, 99, 235, 0.35);
        }

        /* Alerts */
        .alert-success, .alert-error {
            padding: 16px 20px;
            border-radius: 12px;
            margin-bottom: 24px;
            font-size: 14px;
            font-weight: 500;
            display: flex;
            align-items: center;
        }

        .alert-success {
            background: rgba(16, 185, 129, 0.1);
            border: 1px solid rgba(16, 185, 129, 0.2);
            color: #A7F3D0;
        }

        .alert-error {
            background: rgba(239, 68, 68, 0.1);
            border: 1px solid rgba(239, 68, 68, 0.2);
            color: #FCA5A5;
        }
    </style>
</head>
<body>

    <!-- Sidebar -->
    <div class="sidebar">
        <div class="sidebar-brand">SANTULAN</div>
        <ul class="sidebar-menu">
            <li class="menu-item">
                <a href="<?php echo base_url('admin/dashboard'); ?>">Customers (CRM)</a>
            </li>
            <li class="menu-item">
                <a href="<?php echo base_url('admin/logs'); ?>">Observability Logs</a>
            </li>
            <li class="menu-item active">
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
                <h1>Security Configuration</h1>
                <p>Change the secret access key for the primary administrator account</p>
            </div>
        </div>

        <!-- Flash messages -->
        <?php if (session()->getFlashdata('success')): ?>
            <div class="alert-success">
                ✔ <?php echo htmlspecialchars(session()->getFlashdata('success')); ?>
            </div>
        <?php endif; ?>
        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert-error">
                ⚠ <?php echo htmlspecialchars(session()->getFlashdata('error')); ?>
            </div>
        <?php endif; ?>

        <!-- Change Password Card -->
        <div class="card">
            <form action="<?php echo base_url('admin/change-password'); ?>" method="post">
                <div class="form-group">
                    <label for="new_password">New Password</label>
                    <input type="password" name="new_password" id="new_password" class="form-control" placeholder="Enter new password (min 6 characters)" required minlength="6" />
                </div>
                
                <div class="form-group">
                    <label for="confirm_password">Confirm New Password</label>
                    <input type="password" name="confirm_password" id="confirm_password" class="form-control" placeholder="Type new password again to confirm" required minlength="6" />
                </div>

                <div style="margin-top: 10px;">
                    <button type="submit" class="btn btn-primary">Save New Password</button>
                </div>
            </form>
        </div>
    </div>

</body>
</html>
