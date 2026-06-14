<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Support & CRM Detail - Santulan</title>
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

        .back-link {
            display: inline-flex;
            align-items: center;
            color: var(--text-muted);
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
            margin-bottom: 12px;
            gap: 6px;
            transition: color 0.2s;
        }

        .back-link:hover {
            color: var(--text-main);
        }

        .page-title h1 {
            font-size: 26px;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .page-title p {
            font-size: 14px;
            color: var(--text-muted);
            margin-top: 4px;
        }

        /* Layout Grid */
        .layout-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
            margin-bottom: 40px;
        }

        @media (max-width: 1024px) {
            .layout-grid {
                grid-template-columns: 1fr;
            }
        }

        .card {
            background: var(--card-bg);
            backdrop-filter: blur(12px);
            border: 1px solid var(--border-color);
            border-radius: 24px;
            padding: 30px;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
            margin-bottom: 30px;
        }

        .card-title {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 20px;
            border-bottom: 1px solid var(--border-color);
            padding-bottom: 12px;
        }

        /* Form styling */
        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            color: var(--text-muted);
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .form-control {
            width: 100%;
            background: rgba(15, 23, 42, 0.5);
            border: 1px solid var(--border-color);
            border-radius: 12px;
            color: var(--text-main);
            padding: 14px 18px;
            font-size: 14px;
            outline: none;
            transition: border-color 0.2s;
            resize: vertical;
        }

        .form-control:focus {
            border-color: var(--primary);
        }

        /* Buttons */
        .btn {
            padding: 12px 24px;
            border: none;
            border-radius: 12px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .btn-primary {
            background: var(--primary);
            color: #fff;
        }

        .btn-primary:hover {
            background: #1D4ED8;
        }

        .btn-secondary {
            background: rgba(255, 255, 255, 0.06);
            color: var(--text-main);
            border: 1px solid var(--border-color);
        }

        .btn-secondary:hover {
            background: rgba(255, 255, 255, 0.1);
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

        /* Status Badges */
        .badge {
            display: inline-flex;
            align-items: center;
            padding: 6px 14px;
            border-radius: 30px;
            font-size: 13px;
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

        /* Tab panels */
        .tabs {
            display: flex;
            gap: 10px;
            margin-bottom: 24px;
            border-bottom: 1px solid var(--border-color);
            padding-bottom: 12px;
        }

        .tab-btn {
            background: transparent;
            color: var(--text-muted);
            border: none;
            padding: 10px 16px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            border-radius: 8px;
            transition: all 0.2s;
        }

        .tab-btn.active {
            background: var(--primary-light);
            color: var(--text-main);
        }

        .tab-content {
            display: none;
        }

        .tab-content.active {
            display: block;
        }

        /* Scrollable table container */
        .table-responsive {
            max-height: 400px;
            overflow: auto;
            border: 1px solid var(--border-color);
            border-radius: 12px;
            background: rgba(15, 23, 42, 0.2);
        }

        .detail-table {
            width: 100%;
            border-collapse: collapse;
            text-align: left;
        }

        .detail-table th, .detail-table td {
            padding: 12px 18px;
            border-bottom: 1px solid var(--border-color);
            font-size: 13px;
        }

        .detail-table th {
            color: var(--text-muted);
            background: rgba(15, 23, 42, 0.6);
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            position: sticky;
            top: 0;
            z-index: 10;
        }

        .detail-table tbody tr:hover {
            background: rgba(255, 255, 255, 0.02);
        }

        .empty-state {
            text-align: center;
            padding: 30px;
            color: var(--text-muted);
            font-style: italic;
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
        <a href="<?php echo base_url('admin/dashboard'); ?>" class="back-link">
            ← Back to CRM Dashboard
        </a>

        <div class="page-header">
            <div class="page-title">
                <h1>
                    <?php echo htmlspecialchars($customer['name']); ?>
                    <span class="badge badge-<?php echo $customer['status']; ?>">
                        <?php echo $customer['status']; ?>
                    </span>
                </h1>
                <p>Customer ID: #<?php echo $customer['id']; ?> | Created at: <?php echo date('Y-m-d H:i', strtotime($customer['created_at'])); ?></p>
            </div>
            
            <div style="display: flex; gap: 10px;">
                <?php if ($customer['status'] === 'pending' || $customer['status'] === 'rejected'): ?>
                    <form action="<?php echo base_url('admin/approve/' . $customer['id']); ?>" method="post">
                        <button type="submit" class="btn btn-approve">Approve Customer</button>
                    </form>
                <?php endif; ?>
                
                <?php if ($customer['status'] === 'pending' || $customer['status'] === 'approved'): ?>
                    <form action="<?php echo base_url('admin/reject/' . $customer['id']); ?>" method="post">
                        <button type="submit" class="btn btn-reject">Block / Disable Customer</button>
                    </form>
                <?php endif; ?>
            </div>
        </div>

        <!-- Flash messages -->
        <?php if (session()->getFlashdata('success')): ?>
            <div class="alert-success">
                <?php echo htmlspecialchars(session()->getFlashdata('success')); ?>
            </div>
        <?php endif; ?>

        <div class="layout-grid">
            <!-- Left Column: CRM, Billing & Users -->
            <div>
                <!-- CRM & Billing Card -->
                <div class="card">
                    <div class="card-title">CRM & Billing Tracker</div>
                    <form action="<?php echo base_url('admin/customer/billing/' . $customer['id']); ?>" method="post">
                        <div class="form-group">
                            <label for="billing_details">Pricing Plan & Billing Details</label>
                            <textarea name="billing_details" id="billing_details" rows="5" class="form-control" placeholder="e.g. Plan: Pro ($49/month)&#10;Invoice #2045 due on 1st of every month&#10;Payment status: Active"><?php echo htmlspecialchars($customer['billing_details'] ?? ''); ?></textarea>
                        </div>
                        <div class="form-group">
                            <label for="admin_notes">Internal Support & Account Notes</label>
                            <textarea name="admin_notes" id="admin_notes" rows="5" class="form-control" placeholder="e.g. Contact person: Ramesh Kumar&#10;Needs support with SMS import, requested extra user seats."><?php echo htmlspecialchars($customer['admin_notes'] ?? ''); ?></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">Save Billing & Notes</button>
                    </form>
                </div>

                <!-- Associated Users Card -->
                <div class="card">
                    <div class="card-title">Associated Users (Tenant Seats)</div>
                    <div class="table-responsive">
                        <table class="detail-table">
                            <thead>
                                <tr>
                                    <th>User ID</th>
                                    <th>Email</th>
                                    <th>Verified</th>
                                    <th>Admin Role</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($users)): ?>
                                    <tr>
                                        <td colspan="4" class="empty-state">No users registered under this customer.</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($users as $u): ?>
                                        <tr>
                                            <td>#<?php echo $u['id']; ?></td>
                                            <td style="font-weight: 600;"><?php echo htmlspecialchars($u['email']); ?></td>
                                            <td>
                                                <?php if (!empty($u['email_verified_at'])): ?>
                                                    <span style="color: var(--success);">✔ Yes</span>
                                                <?php else: ?>
                                                    <span style="color: var(--warning);">⚠ No</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php if ($u['is_admin']): ?>
                                                    <span style="background: var(--primary); font-size: 10px; padding: 2px 6px; border-radius: 4px; color: white; font-weight: bold;">ADMIN</span>
                                                <?php else: ?>
                                                    <span style="color: var(--text-muted);">Standard</span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Right Column: Tenancy Diagnostics (Read-Only) -->
            <div>
                <div class="card" style="height: 100%; display: flex; flex-direction: column;">
                    <div class="card-title">Customer Database Snapshot (Diagnostics)</div>
                    
                    <div class="tabs">
                        <button class="tab-btn active" onclick="switchTab('tab-transactions', this)">Transactions (<?php echo count($transactions); ?>)</button>
                        <button class="tab-btn" onclick="switchTab('tab-parties', this)">Parties (<?php echo count($parties); ?>)</button>
                        <button class="tab-btn" onclick="switchTab('tab-contacts', this)">Contacts (<?php echo count($contacts); ?>)</button>
                    </div>

                    <!-- Transactions Tab -->
                    <div id="tab-transactions" class="tab-content active" style="flex: 1;">
                        <div class="table-responsive" style="max-height: 500px;">
                            <table class="detail-table">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Party ID</th>
                                        <th>Type</th>
                                        <th>Product</th>
                                        <th>Amount</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($transactions)): ?>
                                        <tr>
                                            <td colspan="5" class="empty-state">No transactions synced.</td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach ($transactions as $t): ?>
                                            <tr>
                                                <td><?php echo date('Y-m-d', strtotime($t['transaction_date'])); ?></td>
                                                <td>#<?php echo $t['client_party_id']; ?></td>
                                                <td>
                                                    <span style="font-weight: 700; color: <?php echo strtolower($t['type']) === 'give' || strtolower($t['type']) === 'payment' ? 'var(--danger)' : 'var(--success)'; ?>">
                                                        <?php echo strtoupper($t['type']); ?>
                                                    </span>
                                                </td>
                                                <td><?php echo htmlspecialchars($t['product'] ?? '-'); ?></td>
                                                <td style="font-weight: 600;">₹<?php echo number_format($t['amount_paise'] / 100, 2); ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Parties Tab -->
                    <div id="tab-parties" class="tab-content" style="flex: 1;">
                        <div class="table-responsive" style="max-height: 500px;">
                            <table class="detail-table">
                                <thead>
                                    <tr>
                                        <th>Local ID</th>
                                        <th>Name</th>
                                        <th>Phone</th>
                                        <th>Type</th>
                                        <th>Tax info</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($parties)): ?>
                                        <tr>
                                            <td colspan="5" class="empty-state">No parties synced.</td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach ($parties as $p): ?>
                                            <tr>
                                                <td>#<?php echo $p['client_local_id']; ?></td>
                                                <td style="font-weight: 600;"><?php echo htmlspecialchars($p['name']); ?></td>
                                                <td><?php echo htmlspecialchars($p['primary_phone']); ?></td>
                                                <td style="text-transform: uppercase; font-size: 11px; font-weight: 700;"><?php echo $p['type']; ?></td>
                                                <td>
                                                    <div style="font-size: 11px; color: var(--text-muted);">
                                                        <?php if ($p['gst_number']): ?>GST: <?php echo htmlspecialchars($p['gst_number']); ?><br><?php endif; ?>
                                                        <?php if ($p['aadhaar_number']): ?>Aadhaar: <?php echo htmlspecialchars($p['aadhaar_number']); ?><?php endif; ?>
                                                        <?php if (!$p['gst_number'] && !$p['aadhaar_number']): ?>-<?php endif; ?>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Contacts Tab -->
                    <div id="tab-contacts" class="tab-content" style="flex: 1;">
                        <div class="table-responsive" style="max-height: 500px;">
                            <table class="detail-table">
                                <thead>
                                    <tr>
                                        <th>Local ID</th>
                                        <th>Display Name</th>
                                        <th>Primary Phone</th>
                                        <th>Last Sync</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($contacts)): ?>
                                        <tr>
                                            <td colspan="4" class="empty-state">No contacts snapshot synced.</td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach ($contacts as $c): ?>
                                            <tr>
                                                <td>#<?php echo $c['client_local_id']; ?></td>
                                                <td style="font-weight: 600;"><?php echo htmlspecialchars($c['display_name']); ?></td>
                                                <td><?php echo htmlspecialchars($c['primary_phone'] ?? '-'); ?></td>
                                                <td><?php echo date('Y-m-d H:i', strtotime($c['last_seen_at'])); ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function switchTab(tabId, btn) {
            // Hide all tab contents
            const contents = document.querySelectorAll('.tab-content');
            contents.forEach(content => content.classList.remove('active'));

            // Remove active class from all buttons
            const buttons = document.querySelectorAll('.tab-btn');
            buttons.forEach(button => button.classList.remove('active'));

            // Show current tab content and set button active
            document.getElementById(tabId).classList.add('active');
            btn.classList.add('active');
        }
    </script>
</body>
</html>
