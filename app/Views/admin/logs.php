<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Santulan Client Observability Logs</title>
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

        .empty-state {
            text-align: center;
            padding: 50px;
            color: var(--text-muted);
            font-size: 15px;
        }

        /* JSON Display code */
        pre {
            background: rgba(0, 0, 0, 0.25);
            padding: 10px;
            border-radius: 8px;
            font-size: 12px;
            font-family: 'Courier New', Courier, monospace;
            max-width: 400px;
            overflow-x: auto;
            white-space: pre-wrap;
            word-wrap: break-word;
            border: 1px solid var(--border-color);
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
            <li class="menu-item active">
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
                <h1>Client Observability Logs</h1>
                <p>Monitor client-side app activities, events, and API sync parameters</p>
            </div>
        </div>

        <!-- CRM Customer Table -->
        <div class="table-card">
            <div class="table-header">
                <div class="table-title">Recent Device Logs (Limit 200)</div>
            </div>
            <table class="requests-table">
                <thead>
                    <tr>
                        <th style="width: 140px;">Timestamp</th>
                        <th style="width: 180px;">Customer / Company</th>
                        <th style="width: 180px;">User</th>
                        <th style="width: 180px;">Action / Event</th>
                        <th style="width: 130px;">Screen</th>
                        <th>Payload Data</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($logs)): ?>
                        <tr>
                            <td colspan="6" class="empty-state">No client observability logs uploaded yet.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($logs as $log): ?>
                        <tr>
                            <td>
                                <div style="font-weight: 500;"><?php echo date('Y-m-d H:i:s', strtotime($log['created_at'])); ?></div>
                                <div style="font-size: 11px; color: var(--text-muted); margin-top: 2px;">
                                    Synced: <?php echo date('H:i', strtotime($log['synced_at'])); ?>
                                </div>
                            </td>
                            <td>
                                <span style="font-weight: 600; color: #3B82F6;">
                                    <?php echo htmlspecialchars($log['customer_name'] ?? 'System/N/A'); ?>
                                </span>
                            </td>
                            <td>
                                <span style="font-size: 13px; color: #E2E8F0;">
                                    <?php echo htmlspecialchars($log['user_email'] ?? 'System'); ?>
                                </span>
                            </td>
                            <td>
                                <span style="background: rgba(37, 99, 235, 0.15); color: #60A5FA; padding: 4px 10px; border-radius: 6px; font-weight: 600; font-size: 13px;">
                                    <?php echo htmlspecialchars($log['name']); ?>
                                </span>
                            </td>
                            <td>
                                <span style="color: var(--text-muted); font-weight: 500; font-size: 13px;">
                                    <?php echo htmlspecialchars($log['screen'] ?? 'Global/Background'); ?>
                                </span>
                            </td>
                            <td>
                                <?php if (!empty($log['payload_json'])): ?>
                                    <pre><?php echo htmlspecialchars($log['payload_json']); ?></pre>
                                <?php else: ?>
                                    <span style="color: var(--text-muted); font-size: 12px; font-style: italic;">No payload</span>
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
