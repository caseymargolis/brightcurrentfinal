<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=dm-sans:400,500,600,700&display=swap" rel="stylesheet" />
        
        <!-- Iconify -->
        <script src="https://code.iconify.design/iconify-icon/1.0.7/iconify-icon.min.js"></script>

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        
        <style>
            * {
                margin: 0;
                padding: 0;
                box-sizing: border-box;
            }
            
            body {
                font-family: 'DM Sans', sans-serif;
                background: #0C214C;
                min-height: 100vh;
                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: center;
                padding: 20px;
            }
            
            .main-container {
                display: flex;
                flex-direction: column;
                align-items: center;
                width: 100%;
                max-width: 420px;
            }
            
            .login-container {
                background: transparent;
                width: 410px;
                text-align: center;
            }
            
            .logo {
                margin-bottom: 24px;
            }
            
            .logo img {
                height: 80px;
                width: auto;
            }
            
            .welcome-text {
                color: #A3AED0;
                font-size: 16px;
                font-weight: 400;
                margin-bottom: 24px;
                line-height: 24px;
                letter-spacing: -0.2px;
                text-align: left;
                border-bottom: 1px solid white;
                padding-bottom: 12px;
            }
            
            .form-group {
                margin-bottom: 16px;
                text-align: left;
            }
            
            .form-label {
                color: white;
                font-size: 14px;
                font-weight: 500;
                margin-bottom: 8px;
                display: block;
                letter-spacing: -0.2px;
            }
            
            .form-input {
                width: 100%;
                padding: 16px 20px;
                border: 2px solid #E0E5F2;
                border-radius: 0;
                font-size: 14px;
                color: white;
                background: transparent;
                transition: all 0.2s ease;
                box-sizing: border-box;
                font-family: 'DM Sans', sans-serif;
                height: 50px;
                font-weight: 400;
                letter-spacing: -0.2px;
            }
            
            .form-input:focus {
                outline: none;
                border-color: #4318FF;
                box-shadow: 0 0 0 1px #4318FF;
            }
            
            .form-input::placeholder {
                color: #A3AED0;
                font-size: 14px;
                font-weight: 400;
                letter-spacing: -0.2px;
            }
            
            .password-container {
                position: relative;
            }
            
            .password-toggle {
                position: absolute;
                right: 20px;
                top: 50%;
                transform: translateY(-50%);
                background: none;
                border: none;
                color: #A3AED0;
                cursor: pointer;
                padding: 0;
                display: flex;
                align-items: center;
                justify-content: center;
                transition: color 0.2s ease;
                width: 20px;
                height: 20px;
            }
            
            .password-toggle:hover {
                color: #2B3674;
            }
            
            .checkbox-container {
                display: flex;
                align-items: center;
                margin-bottom: 40px;
                margin-top: 8px;
                text-align: left;
            }
            
            .checkbox-custom {
                width: 20px;
                height: 20px;
                border: 2px solid #E0E5F2;
                border-radius: 6px;
                margin-right: 12px;
                position: relative;
                cursor: pointer;
                appearance: none;
                background: white;
                transition: all 0.2s ease;
                flex-shrink: 0;
            }
            
            .checkbox-custom:checked {
                background: #4318FF;
                border-color: #4318FF;
            }
            
            .checkbox-custom:checked::after {
                content: "✓";
                position: absolute;
                color: white;
                font-size: 12px;
                font-weight: bold;
                top: 50%;
                left: 50%;
                transform: translate(-50%, -50%);
                line-height: 1;
            }
            
            .checkbox-label {
                color: white;
                font-size: 14px;
                font-weight: 500;
                cursor: pointer;
                user-select: none;
                letter-spacing: -0.2px;
            }
            
            .login-button {
                width: 100%;
                background: linear-gradient(135deg, #FF9500 0%, #FF6B35 100%);
                border: none;
                border-radius: 0;
                padding: 16px 20px;
                color: white;
                font-size: 14px;
                font-weight: 700;
                cursor: pointer;
                transition: all 0.3s ease;
                text-transform: none;
                font-family: 'DM Sans', sans-serif;
                height: 54px;
                letter-spacing: -0.2px;
                display: flex;
                align-items: center;
                justify-content: center;
            }
            
            .login-button:hover {
                transform: translateY(-2px);
                box-shadow: 0 10px 25px rgba(255, 149, 0, 0.25);
            }
            
            .login-button:active {
                transform: translateY(0);
            }
            
            .error-message {
                color: #E53E3E;
                font-size: 12px;
                margin-top: 6px;
                font-weight: 400;
            }
            
            .login-link {
                margin-top: 24px;
                text-align: center;
            }
            
            .login-link-text {
                color: #A3AED0;
                font-size: 14px;
                font-weight: 400;
                letter-spacing: -0.2px;
            }
            
            .login-link-anchor {
                color: #4318FF;
                font-size: 14px;
                font-weight: 700;
                text-decoration: none;
                margin-left: 8px;
                letter-spacing: -0.2px;
                transition: color 0.2s ease;
            }
            
            .login-link-anchor:hover {
                color: #FF9500;
            }
            
            .footer-text {
                position: fixed;
                left: 50%;
                transform: translate(-50%, 0);
                bottom: 12px;
                color: #A3AED0;
                font-size: 14px;
                font-weight: 400;
                text-align: center;
            }
            
            /* Responsive design */
            @media (max-width: 480px) {
                body {
                    padding: 16px;
                }
                
                .main-container {
                    max-width: 360px;
                }
                
                .login-container {
                    padding: 32px 24px 24px 24px;
                    max-width: 360px;
                }
                
                .logo {
                    margin-bottom: 32px;
                }
                
                .logo img {
                    height: 60px;
                }
                
                .welcome-text {
                    font-size: 14px;
                    margin-bottom: 24px;
                }
                
                .form-group {
                    margin-bottom: 16px;
                }
                
                .form-input {
                    height: 48px;
                    padding: 14px 18px;
                }
                
                .password-toggle {
                    right: 18px;
                }
                
                .checkbox-container {
                    margin-bottom: 32px;
                }
                
                .login-button {
                    height: 50px;
                    font-size: 14px;
                }
            }
        </style>
    </head>
    <body>
        <div class="main-container">
            {{ $slot }}
            <!-- <div class="footer-text">
                © 2022 Horizon UI. All Rights Reserved. Made with love by Simmmple!
            </div> -->
        </div>
    </body>
</html>
