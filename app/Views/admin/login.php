<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Santulan Admin Login</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg-color: #0F172A;
            --card-bg: rgba(30, 41, 59, 0.7);
            --border-color: rgba(255, 255, 255, 0.08);
            --primary: #2563EB;
            --primary-hover: #3B82F6;
            --text-main: #F8FAFC;
            --text-muted: #94A3B8;
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
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            overflow: hidden;
            position: relative;
        }

        /* Abstract background glows */
        .glow {
            position: absolute;
            width: 300px;
            height: 300px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(37, 99, 235, 0.15) 0%, rgba(0,0,0,0) 70%);
            z-index: 0;
            pointer-events: none;
        }
        .glow-1 { top: 10%; left: 15%; }
        .glow-2 { bottom: 10%; right: 15%; }

        .login-container {
            width: 100%;
            max-width: 440px;
            padding: 20px;
            z-index: 10;
        }

        .login-card {
            background: var(--card-bg);
            backdrop-filter: blur(16px);
            border: 1px solid var(--border-color);
            border-radius: 24px;
            padding: 40px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
            width: 100%;
        }

        .brand-section {
            text-align: center;
            margin-bottom: 30px;
        }

        .brand-logo {
            font-size: 28px;
            font-weight: 700;
            background: linear-gradient(135deg, #3B82F6 0%, #2563EB 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            letter-spacing: -1px;
            margin-bottom: 8px;
        }

        .brand-subtitle {
            font-size: 14px;
            color: var(--text-muted);
        }

        .form-group {
            margin-bottom: 20px;
            position: relative;
        }

        .form-label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            color: var(--text-muted);
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .form-input {
            width: 100%;
            background: rgba(15, 23, 42, 0.6);
            border: 1px solid var(--border-color);
            border-radius: 12px;
            padding: 14px 16px;
            color: var(--text-main);
            font-size: 15px;
            outline: none;
            transition: all 0.2s ease;
        }

        .form-input:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.15);
            background: rgba(15, 23, 42, 0.8);
        }

        .alert-error {
            background: rgba(239, 68, 68, 0.1);
            border: 1px solid rgba(239, 68, 68, 0.2);
            color: #F87171;
            padding: 12px 16px;
            border-radius: 12px;
            font-size: 14px;
            margin-bottom: 20px;
            line-height: 1.4;
        }

        .login-btn {
            width: 100%;
            background: var(--primary);
            color: #fff;
            border: none;
            border-radius: 12px;
            padding: 14px;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            transition: background-color 0.2s ease, transform 0.1s ease;
            margin-top: 10px;
        }

        .login-btn:hover {
            background: var(--primary-hover);
        }

        .login-btn:active {
            transform: scale(0.98);
        }
    </style>
</head>
<body>
    <div class="glow glow-1"></div>
    <div class="glow glow-2"></div>

    <div class="login-container">
        <div class="login-card">
            <div class="brand-section">
                <div class="brand-logo">SANTULAN</div>
                <div class="brand-subtitle">Control & Registration Panel Login</div>
            </div>

            <?php if (session()->getFlashdata('error')): ?>
                <div class="alert-error">
                    <?php echo htmlspecialchars(session()->getFlashdata('error')); ?>
                </div>
            <?php endif; ?>

            <form action="<?php echo base_url('admin/login'); ?>" method="post">
                <div class="form-group">
                    <label class="form-label" for="email">Admin Email</label>
                    <input class="form-input" type="email" id="email" name="email" placeholder="admin@santulan.com" required autocomplete="email">
                </div>

                <div class="form-group">
                    <label class="form-label" for="password">Password</label>
                    <input class="form-input" type="password" id="password" name="password" placeholder="••••••••" required autocomplete="current-password">
                </div>

                <button class="login-btn" type="submit">Sign In to Dashboard</button>
            </form>
        </div>
    </div>
</body>
</html>
