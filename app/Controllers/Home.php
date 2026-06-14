<?php

namespace App\Controllers;

class Home extends BaseController
{
    public function index(): string
    {
        return '
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Santulan API Portal</title>
            <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
            <style>
                body {
                    background-color: #0F172A;
                    color: #F8FAFC;
                    font-family: "Plus Jakarta Sans", sans-serif;
                    display: flex;
                    justify-content: center;
                    align-items: center;
                    height: 100vh;
                    margin: 0;
                }
                .container {
                    text-align: center;
                    padding: 40px;
                    background: rgba(30, 41, 59, 0.4);
                    backdrop-filter: blur(12px);
                    border: 1px solid rgba(255, 255, 255, 0.08);
                    border-radius: 24px;
                    box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
                    max-width: 400px;
                }
                .logo {
                    font-size: 28px;
                    font-weight: 700;
                    letter-spacing: -0.5px;
                    background: linear-gradient(135deg, #3B82F6 0%, #2563EB 100%);
                    -webkit-background-clip: text;
                    -webkit-text-fill-color: transparent;
                    margin-bottom: 20px;
                }
                .status-badge {
                    display: inline-flex;
                    align-items: center;
                    gap: 8px;
                    background: rgba(16, 185, 129, 0.1);
                    border: 1px solid rgba(16, 185, 129, 0.2);
                    color: #A7F3D0;
                    padding: 8px 16px;
                    border-radius: 30px;
                    font-size: 14px;
                    font-weight: 600;
                    margin-bottom: 24px;
                }
                .dot {
                    width: 8px;
                    height: 8px;
                    background-color: #10B981;
                    border-radius: 50%;
                    animation: pulse 1.5s infinite;
                }
                p {
                    color: #94A3B8;
                    font-size: 14px;
                    line-height: 1.6;
                    margin: 0 0 30px 0;
                }
                .footer-text {
                    font-size: 11px;
                    color: #475569;
                    text-transform: uppercase;
                    letter-spacing: 1px;
                }
                @keyframes pulse {
                    0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7); }
                    70% { transform: scale(1); box-shadow: 0 0 0 8px rgba(16, 185, 129, 0); }
                    100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(16, 185, 129, 0); }
                }
            </style>
        </head>
        <body>
            <div class="container">
                <div class="logo">SANTULAN API</div>
                <div class="status-badge">
                    <span class="dot"></span>
                    API Services Operational
                </div>
                <p>This subdomain serves synchronization, ledger transactions, and admin portal controls for the Santulan application suite.</p>
                <div class="footer-text">Production Environment v1.0</div>
            </div>
        </body>
        </html>
        ';
    }
}
