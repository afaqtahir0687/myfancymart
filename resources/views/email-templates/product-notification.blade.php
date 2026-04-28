<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>{{ $type == 'new' ? translate('New Product') : translate('Discount Applied') }}</title>
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
            text-align: center;
        }
        .content h2 {
            margin-top: 0;
            color: #1a1a1a;
            font-size: 22px;
        }
        .product-card {
            background-color: #fdfdfd;
            border: 1px solid #eeeeee;
            padding: 20px;
            border-radius: 10px;
            margin: 25px 0;
        }
        .product-image {
            max-width: 100%;
            height: auto;
            border-radius: 8px;
            margin-bottom: 20px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        .product-name {
            font-size: 20px;
            font-weight: bold;
            color: #1a1a1a;
            margin-bottom: 10px;
        }
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
                    <h2>{{ $type == 'new' ? translate('Exciting New Arrival!') : translate('Price Drop Alert!') }}</h2>
                    <p style="font-size: 16px; line-height: 1.6;">{{ $messageStr }}</p>
                    
                    <div class="product-card">
                        <img src="{{ getStorageImages(path: $product->thumbnail_full_url, type: 'backend-product') }}" 
                             alt="{{ $product->name }}" 
                             class="product-image" 
                             onerror="this.src='{{ asset('public/assets/back-end/img/160x160/img2.jpg') }}'">
                        
                        <div class="product-name">{{ $product->name }}</div>
                        
                        <div class="btn-container">
                            <a href="{{ route('product', $product->slug) }}" class="btn">{{ translate('Shop Now') }}</a>
                        </div>
                    </div>
                </td>
            </tr>
            <tr>
                <td class="footer">
                    <p>Stay tuned for more updates and exclusive offers!</p>
                    <p>&copy; {{ date('Y') }} {{ getWebConfig('company_name') }}. All rights reserved.</p>
                </td>
            </tr>
        </table>
    </div>
</body>
</html>
