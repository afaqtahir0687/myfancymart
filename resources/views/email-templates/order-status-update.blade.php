<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>{{ translate('Order Status Update') }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style type="text/css">
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8f9fa;
            margin: 0;
            padding: 0;
            -webkit-font-smoothing: antialiased;
        }
        .wrapper {
            width: 100%;
            table-layout: fixed;
            background-color: #f8f9fa;
            padding-bottom: 40px;
        }
        .main {
            background-color: #ffffff;
            margin: 0 auto;
            width: 100%;
            max-width: 600px;
            border-spacing: 0;
            color: #4a4a4a;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 10px rgba(0,0,0,0.05);
            margin-top: 40px;
        }
        .header {
            background-color: #1a1a1a;
            padding: 30px;
            text-align: center;
        }
        .header h1 {
            color: #ffffff;
            margin: 0;
            font-size: 28px;
            letter-spacing: 2px;
            text-transform: uppercase;
        }
        .content {
            padding: 40px 30px;
        }
        .content h2 {
            margin-top: 0;
            color: #1a1a1a;
            font-size: 22px;
        }
        .order-info {
            background-color: #fdfdfd;
            border: 1px solid #eeeeee;
            padding: 20px;
            border-radius: 6px;
            margin: 25px 0;
        }
        .order-id {
            font-weight: bold;
            color: #1a1a1a;
            font-size: 18px;
        }
        .status-badge {
            display: inline-block;
            padding: 6px 15px;
            border-radius: 50px;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 14px;
            margin-top: 10px;
        }
        .status-delivered { background-color: #d4edda; color: #155724; }
        .status-shipped { background-color: #cce5ff; color: #004085; }
        .status-pending { background-color: #fff3cd; color: #856404; }
        .status-canceled { background-color: #f8d7da; color: #721c24; }
        .status-default { background-color: #e2e3e5; color: #383d41; }

        .btn-container {
            text-align: center;
            margin-top: 30px;
        }
        .btn {
            background-color: #1a1a1a;
            color: #ffffff !important;
            padding: 15px 30px;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            display: inline-block;
            transition: background-color 0.3s;
        }
        .footer {
            padding: 30px;
            text-align: center;
            font-size: 13px;
            color: #888888;
            background-color: #fdfdfd;
            border-top: 1px solid #eeeeee;
        }
        .footer p {
            margin: 5px 0;
        }
        @media screen and (max-width: 600px) {
            .main {
                margin-top: 0 !important;
                border-radius: 0 !important;
            }
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <table class="main" width="100%">
            <tr>
                <td class="header">
                    <h1>{{ getWebConfig('company_name') }}</h1>
                </td>
            </tr>
            <tr>
                <td class="content">
                    <h2>Hi {{ $userName }},</h2>
                    <p>Good news! Your order has been updated. We're working hard to get your items to you as quickly as possible.</p>
                    
                    <div class="order-info">
                        <div class="order-id">Order #{{ $orderId }}</div>
                        @php
                            $statusClass = 'status-default';
                            $currentStatus = strtolower($status);
                            if ($currentStatus == 'delivered') $statusClass = 'status-delivered';
                            elseif ($currentStatus == 'shipped' || $currentStatus == 'out_for_delivery') $statusClass = 'status-shipped';
                            elseif ($currentStatus == 'pending' || $currentStatus == 'confirmed') $statusClass = 'status-pending';
                            elseif ($currentStatus == 'canceled' || $currentStatus == 'returned' || $currentStatus == 'failed') $statusClass = 'status-canceled';
                        @endphp
                        <div class="status-badge {{ $statusClass }}">
                            {{ translate($status) }}
                        </div>
                    </div>

                    <p>You can view your order details and track its progress anytime on our website.</p>
                    
                    <div class="btn-container">
                        <a href="{{ route('account-order-details', ['order_id' => $orderId]) }}" class="btn">{{ translate('View Order Details') }}</a>
                    </div>
                </td>
            </tr>
            <tr>
                <td class="footer">
                    <p>Thank you for choosing {{ getWebConfig('company_name') }}!</p>
                    <p>&copy; {{ date('Y') }} {{ getWebConfig('company_name') }}. All rights reserved.</p>
                    <p>If you have any questions, feel free to contact our support team.</p>
                </td>
            </tr>
        </table>
    </div>
</body>
</html>
