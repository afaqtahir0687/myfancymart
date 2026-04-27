<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to FancyMart.pk</title>
    <style>
        /* FancyMart.pk Theme Colors */
        :root {
            --primary: #fe696a;
            --secondary: #f3f5f9;
            --success: #42d697;
            --info: #69b3fe;
            --warning: #fea569;
            --danger: #f34770;
            --light: #fff;
            --dark: #373f50;
            --accent: #4e54c8;
            --font-family-sans-serif: "Open Sans", sans-serif;
        }
        
        /* Reset and base styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: var(--font-family-sans-serif);
            line-height: 1.6;
            color: #262d34;
            background-color: #f4f4f4;
            padding: 20px;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }
        
        .email-container {
            max-width: 600px;
            margin: 0 auto;
            background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0px 15px 35px rgba(145, 158, 171, 0.15),
                        0px 5px 15px rgba(145, 158, 171, 0.1),
                        inset 0px 1px 0px rgba(255, 255, 255, 0.9);
            border: 1px solid rgba(254, 105, 106, 0.1);
            position: relative;
        }
        
        .email-container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(45deg, 
                rgba(254, 105, 106, 0.05) 0%, 
                rgba(66, 214, 151, 0.05) 50%, 
                rgba(105, 179, 254, 0.05) 100%);
            pointer-events: none;
        }
        
        .header {
            background-color: var(--primary);
            padding: 40px;
            text-align: center;
        }
        
        .logo {
            font-size: 32px;
            font-weight: bold;
            color: #ffffff;
            text-decoration: none;
            letter-spacing: 1px;
        }
        
        .content {
            padding: 40px;
        }
        
        .greeting {
            font-size: 24px;
            font-weight: 600;
            color: #212629;
            margin-bottom: 20px;
            letter-spacing: 0.01em;
        }
        
        .main-message {
            font-size: 16px;
            color: #262d34;
            margin-bottom: 30px;
            line-height: 1.8;
        }
        
        .cta-button {
            display: inline-block;
            background: linear-gradient(135deg, var(--primary) 0%, #fe4344 100%);
            color: #ffffff;
            text-decoration: none;
            padding: 18px 35px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 16px;
            text-align: center;
            margin: 30px 0;
            transition: all 300ms ease-in-out;
            box-shadow: 0px 8px 25px rgba(254, 105, 106, 0.25),
                        0px 4px 10px rgba(254, 105, 106, 0.15),
                        inset 0px 1px 0px rgba(255, 255, 255, 0.2);
            border: 1px solid rgba(255, 255, 255, 0.1);
            position: relative;
            overflow: hidden;
        }
        
        .cta-button::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, 
                transparent 0%, 
                rgba(255, 255, 255, 0.2) 50%, 
                transparent 100%);
            transition: left 0.6s ease;
        }
        
        .cta-button:hover {
            background: linear-gradient(135deg, #fe4344 0%, #fe3638 100%);
            transform: translateY(-3px);
            box-shadow: 0px 12px 35px rgba(254, 105, 106, 0.35),
                        0px 6px 15px rgba(254, 105, 106, 0.25),
                        inset 0px 1px 0px rgba(255, 255, 255, 0.3);
        }
        
        .cta-button:hover::before {
            left: 100%;
        }
        
        .benefits {
            background: linear-gradient(135deg, var(--secondary) 0%, #ffffff 100%);
            padding: 30px;
            margin: 30px 0;
            border-radius: 12px;
            box-shadow: 0px 8px 25px rgba(145, 158, 171, 0.08),
                        inset 0px 1px 0px rgba(255, 255, 255, 0.8);
            border: 1px solid rgba(254, 105, 106, 0.08);
            position: relative;
        }
        
        .benefits::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(45deg, 
                rgba(254, 105, 106, 0.03) 0%, 
                rgba(66, 214, 151, 0.03) 50%, 
                rgba(105, 179, 254, 0.03) 100%);
            border-radius: 12px;
            pointer-events: none;
        }
        
        .benefits h3 {
            color: #212629;
            margin-bottom: 20px;
            font-size: 18px;
            font-weight: 600;
            letter-spacing: 0.01em;
        }
        
        .benefits ul {
            list-style: none;
            padding: 0;
        }
        
        .benefits li {
            padding: 8px 0;
            color: #262d34;
            position: relative;
            padding-left: 25px;
        }
        
        .benefits li:before {
            content: "✓";
            position: absolute;
            left: 0;
            color: var(--success);
            font-weight: bold;
        }
        
        .footer {
            background-color: var(--secondary);
            padding: 30px;
            text-align: center;
            border-top: 1px solid #e9ecef;
        }
        
        .footer p {
            color: #7d879c;
            font-size: 14px;
            margin-bottom: 10px;
        }
        
        .footer a {
            color: var(--primary);
            text-decoration: none;
            font-weight: 500;
        }
        
        .footer a:hover {
            text-decoration: underline;
        }
        
        /* Mobile responsive */
        @media only screen and (max-width: 600px) {
            .email-container {
                margin: 10px;
                border-radius: 0;
            }
            
            .header, .content, .footer {
                padding: 25px;
            }
            
            .greeting {
                font-size: 20px;
            }
            
            .cta-button {
                display: block;
                width: 100%;
                text-align: center;
            }
        }
    </style>
</head>
<body>
    <div class="email-container">
        <!-- Header -->
        <div class="header">
            <a href="{{ $appUrl }}" class="logo">FancyMart.pk</a>
        </div>
        
        <!-- Main Content -->
        <div class="content">
            <h1 class="greeting">Hi {{ $user->name ?? 'Customer' }},</h1>
            
            <div class="main-message">
                <p>Welcome to FancyMart.pk! 🎉</p>
                <p>Your account has been created successfully and you're now part of Pakistan's premier online shopping destination.</p>
                <p>Discover thousands of products across multiple categories with the convenience of shopping from anywhere, anytime.</p>
            </div>
            
            <!-- CTA Button -->
            <div style="text-align: center;">
                <a href="{{ $appUrl }}" class="cta-button">Start Shopping</a>
            </div>
            
            <!-- Benefits Section -->
            <div class="benefits">
                <h3>Why Shop with FancyMart.pk?</h3>
                <ul>
                    <li>Cash on Delivery available nationwide</li>
                    <li>Fast and reliable shipping across Pakistan</li>
                    <li>Exclusive deals and discounts</li>
                    <li>Secure payment options</li>
                    <li>24/7 customer support</li>
                    <li>Easy returns and refunds</li>
                </ul>
            </div>
            
            <div class="main-message">
                <p>Ready to start your shopping journey? Browse our extensive collection and find everything you need in one place.</p>
                <p>If you have any questions, our support team is always here to help!</p>
            </div>
        </div>
        
        <!-- Footer -->
        <div class="footer">
            <p>Need help? Contact us at <a href="mailto:support@fancymart.pk">support@fancymart.pk</a></p>
            <p>Visit us at <a href="{{ $appUrl }}">{{ $appUrl }}</a></p>
            <p style="margin-top: 20px; font-size: 12px;">&copy; {{ date('Y') }} FancyMart.pk. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
