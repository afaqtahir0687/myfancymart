<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>{{ translate('Order Status Update') }}</title>
    <style type="text/css">
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; background-color: #f4f4f4; margin: 0; padding: 0; }
        .container { max-width: 600px; margin: 0 auto; background: #ffffff; padding: 20px; border-radius: 8px; margin-top: 20px; }
        .header { text-align: center; border-bottom: 1px solid #ddd; padding-bottom: 20px; margin-bottom: 20px; }
        .header h1 { color: #333; }
        .content { font-size: 16px; color: #555; line-height: 1.5; }
        .footer { margin-top: 30px; text-align: center; font-size: 12px; color: #aaa; border-top: 1px solid #ddd; padding-top: 20px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>{{ getWebConfig('company_name') }}</h2>
        </div>
        <div class="content">
            <p>Hi {{ $userName }},</p>
            <p>We are writing to inform you that your order <b>#{{ $orderId }}</b> has been updated.</p>
            <p>The current status of your order is now: <b>{{ translate($status) }}</b></p>
            <p>Thank you for shopping with us!</p>
        </div>
        <div class="footer">
            <p>&copy; {{ date('Y') }} {{ getWebConfig('company_name') }}. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
