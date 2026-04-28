<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>{{ $type == 'new' ? translate('New Product') : translate('Discount Applied') }}</title>
    <style type="text/css">
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; background-color: #f4f4f4; margin: 0; padding: 0; }
        .container { max-width: 600px; margin: 0 auto; background: #ffffff; padding: 20px; border-radius: 8px; margin-top: 20px; }
        .header { text-align: center; border-bottom: 1px solid #ddd; padding-bottom: 20px; margin-bottom: 20px; }
        .header h1 { color: #333; }
        .content { font-size: 16px; color: #555; line-height: 1.5; text-align: center; }
        .product-image { max-width: 100%; height: auto; border-radius: 8px; margin-bottom: 15px; }
        .product-name { font-size: 20px; font-weight: bold; color: #333; margin-bottom: 10px; }
        .btn { display: inline-block; padding: 10px 20px; background-color: #007bff; color: #ffffff; text-decoration: none; border-radius: 5px; margin-top: 20px; }
        .footer { margin-top: 30px; text-align: center; font-size: 12px; color: #aaa; border-top: 1px solid #ddd; padding-top: 20px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>{{ getWebConfig('company_name') }}</h2>
        </div>
        <div class="content">
            <p>{{ $messageStr }}</p>
            <img src="{{ getStorageImages(path: $product->thumbnail_full_url, type: 'backend-product') }}" alt="{{ $product->name }}" class="product-image" onerror="this.src='{{ asset('public/assets/back-end/img/160x160/img2.jpg') }}'">
            <div class="product-name">{{ $product->name }}</div>
            <a href="{{ route('product', $product->slug) }}" style="color: white;" class="btn">{{ translate('View Product') }}</a>
        </div>
        <div class="footer">
            <p>&copy; {{ date('Y') }} {{ getWebConfig('company_name') }}. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
