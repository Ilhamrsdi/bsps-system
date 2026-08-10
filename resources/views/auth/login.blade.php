<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />

    <!-- Instant Theme Loader (Zero Flash Fix) -->
    <script>
        (function() {
            var theme = localStorage.getItem('pupr_theme') || 'pupr';
            document.documentElement.setAttribute('data-theme', theme);
        })();
    </script>

    <title>Login - BSPS Verval (Bantuan Stimulan Perumahan Swadaya)</title>

    <!-- Favicon Logo PUPR -->
    <link rel="icon" href="{{ asset('logo.jpg') }}" type="image/jpeg" />

    <!-- Google Fonts & Font Awesome -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />

    <!-- App & Component CSS -->
    <link rel="stylesheet" href="{{ asset('assets/css/app.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/component.css') }}" />

    <style>
        body.login-page {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: radial-gradient(circle at 10% 20%, #001737 0%, #002855 45%, #070D1E 90%);
            padding: 20px;
            position: relative;
            overflow-y: auto;
        }

        body.login-page::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0; bottom: 0;
            background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.03'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
            opacity: 0.8;
            pointer-events: none;
        }

        .login-card {
            background: rgba(255, 255, 255, 0.96);
            backdrop-filter: blur(16px);
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.35);
            width: 100%;
            max-width: 440px;
            padding: 36px 32px;
            position: relative;
            z-index: 10;
            border: 1px solid rgba(255, 255, 255, 0.2);
            animation: loginCardIn 0.5s cubic-bezier(0.16, 1, 0.3, 1);
        }

        html[data-theme="dark"] .login-card {
            background: rgba(15, 23, 42, 0.95);
            border: 1px solid rgba(255, 255, 255, 0.12);
        }

        @keyframes loginCardIn {
            from { opacity: 0; transform: translateY(24px) scale(0.96); }
            to { opacity: 1; transform: translateY(0) scale(1); }
        }

        .login-brand {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 14px;
            margin-bottom: 24px;
            text-align: left;
        }

        .login-brand .brand-logo-img {
            width: 52px;
            height: 52px;
            object-fit: contain;
        }

        .login-brand .brand-text h1 {
            font-size: 20px;
            font-weight: 900;
            color: var(--primary, #002855);
            line-height: 1.1;
            letter-spacing: -0.3px;
        }

        html[data-theme="dark"] .login-brand .brand-text h1 {
            color: #ffffff;
        }

        .login-brand .brand-text span {
            font-size: 11px;
            color: #FFB800;
            font-weight: 800;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            display: block;
            margin-top: 2px;
        }

        .login-header {
            text-align: center;
            margin-bottom: 24px;
        }

        .login-header h2 {
            font-size: 20px;
            font-weight: 800;
            color: var(--text-primary, #0A192F);
            margin-bottom: 6px;
        }

        .login-header p {
            font-size: 13px;
            color: var(--text-muted, #64748B);
        }

        .alert-box {
            padding: 12px 16px;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 500;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .alert-box.danger {
            background: rgba(231, 76, 60, 0.1);
            border: 1px solid rgba(231, 76, 60, 0.2);
            color: #e74c3c;
        }

        .alert-box.info {
            background: rgba(0, 80, 157, 0.1);
            border: 1px solid rgba(0, 80, 157, 0.2);
            color: var(--primary);
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            font-size: 13px;
            font-weight: 700;
            margin-bottom: 8px;
            color: var(--text-secondary, #334155);
        }

        .input-group-custom {
            position: relative;
            display: flex;
            align-items: center;
        }

        .input-group-custom i.input-icon {
            position: absolute;
            left: 14px;
            color: var(--text-muted, #64748B);
            font-size: 15px;
        }

        .input-group-custom input {
            width: 100%;
            padding: 12px 42px 12px 40px;
            border-radius: 10px;
            border: 1px solid rgba(0, 40, 85, 0.15);
            background: var(--bg-body, #F4F6F9);
            color: var(--text-primary, #0A192F);
            font-family: inherit;
            font-size: 14px;
            font-weight: 500;
            outline: none;
            transition: all 0.2s ease;
        }

        .input-group-custom input:focus {
            border-color: var(--primary, #002855);
            background: var(--bg-card, #ffffff);
            box-shadow: 0 0 0 4px rgba(0, 40, 85, 0.08);
        }

        .input-group-custom i.toggle-pwd {
            position: absolute;
            right: 14px;
            color: var(--text-muted, #64748B);
            font-size: 15px;
            cursor: pointer;
            padding: 4px;
        }

        .input-group-custom i.toggle-pwd:hover {
            color: var(--primary, #002855);
        }

        .form-options {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 24px;
            font-size: 13px;
        }

        .checkbox-label {
            display: flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
            color: var(--text-secondary, #334155);
            font-weight: 500;
        }

        .checkbox-label input[type="checkbox"] {
            width: 16px;
            height: 16px;
            accent-color: var(--primary, #002855);
            cursor: pointer;
        }

        .btn-login-submit {
            width: 100%;
            padding: 14px;
            border-radius: 10px;
            border: none;
            background: linear-gradient(135deg, var(--primary, #002855) 0%, var(--primary-light, #003E75) 100%);
            color: #ffffff;
            font-family: inherit;
            font-size: 15px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.2s ease;
            box-shadow: 0 4px 14px rgba(0, 40, 85, 0.25);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .btn-login-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(0, 40, 85, 0.35);
        }

        /* Demo Quick Accounts Box */
        .demo-accounts-card {
            margin-top: 24px;
            padding-top: 20px;
            border-top: 1px dashed rgba(0, 40, 85, 0.12);
        }

        .demo-title {
            font-size: 11px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: var(--text-muted, #64748B);
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .demo-btn-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 8px;
        }

        .btn-demo {
            padding: 8px 6px;
            border-radius: 6px;
            border: 1px solid rgba(0, 40, 85, 0.12);
            background: var(--bg-body, #F4F6F9);
            color: var(--text-secondary, #334155);
            font-family: inherit;
            font-size: 11px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.2s ease;
            text-align: center;
        }

        .btn-demo:hover {
            border-color: var(--primary, #002855);
            background: var(--primary, #002855);
            color: #ffffff;
        }

        .login-footer-text {
            text-align: center;
            margin-top: 24px;
            font-size: 11px;
            color: rgba(255, 255, 255, 0.6);
            z-index: 10;
        }

        /* Responsive Login Page Rules */
        @media (max-width: 480px) {
            body.login-page {
                padding: 16px 12px;
                align-items: center;
            }
            .login-card {
                padding: 24px 18px;
                border-radius: 16px;
                box-sizing: border-box;
                width: 100%;
            }
            .login-brand {
                gap: 10px;
                margin-bottom: 18px;
            }
            .login-brand .brand-logo-img {
                width: 42px;
                height: 42px;
            }
            .login-brand .brand-text h1 {
                font-size: 17px;
            }
            .login-brand .brand-text span {
                font-size: 9.5px;
            }
            .login-header h2 {
                font-size: 18px;
            }
            .login-header p {
                font-size: 12px;
            }
            .btn-demo {
                padding: 9px 4px;
                font-size: 11px;
            }
        }
    </style>
</head>
<body class="login-page">

    <div style="width: 100%; max-width: 440px;">
        <div class="login-card">
            <!-- Brand Logo Header -->
            <div class="login-brand">
                <img src="{{ asset('logo.jpg') }}" alt="Logo BSPS" class="brand-logo-img" />
                <div class="brand-text">
                    <h1>BSPS Verval</h1>
                    <span>SISTEM VERIFIKASI &amp; VALIDASI PERUMAHAN SWADAYA</span>
                </div>
            </div>

            <div class="login-header">
                <h2>Selamat Datang</h2>
                <p>Masuk ke Sistem Verifikasi &amp; Validasi BSPS</p>
            </div>

            <!-- Error Notification -->
            @if ($errors->any())
                <div class="alert-box danger">
                    <i class="fas fa-exclamation-circle"></i>
                    <div>{{ $errors->first() }}</div>
                </div>
            @endif

            @if (session('info'))
                <div class="alert-box info">
                    <i class="fas fa-info-circle"></i>
                    <div>{{ session('info') }}</div>
                </div>
            @endif

            <!-- Login Form -->
            <form action="{{ url('/login') }}" method="POST">
                @csrf

                <div class="form-group">
                    <label for="email"><i class="fas fa-envelope"></i> Email / Username</label>
                    <div class="input-group-custom">
                        <i class="fas fa-user input-icon"></i>
                        <input type="email" id="email" name="email" value="{{ old('email') }}" placeholder="masukkan email anda" required autofocus />
                    </div>
                </div>

                <div class="form-group">
                    <label for="password"><i class="fas fa-lock"></i> Password</label>
                    <div class="input-group-custom">
                        <i class="fas fa-key input-icon"></i>
                        <input type="password" id="password" name="password" value="" placeholder="••••••••" required />
                        <i class="fas fa-eye toggle-pwd" id="togglePasswordBtn"></i>
                    </div>
                </div>

                <div class="form-options">
                    <label class="checkbox-label">
                        <input type="checkbox" name="remember" value="1" checked />
                        Ingat Saya di Perangkat Ini
                    </label>
                </div>

                <button type="submit" class="btn-login-submit">
                    <i class="fas fa-sign-in-alt"></i> MASUK KE SISTEM
                </button>
            </form>
        </div>

        <div class="login-footer-text">
            &copy; {{ date('Y') }} BSPS Verval - Bantuan Stimulan Perumahan Swadaya.<br />
            Developed by PT Aleena Mandiri Group
        </div>
    </div>

    <script>
        // Toggle Show/Hide Password
        const togglePwdBtn = document.getElementById('togglePasswordBtn');
        const pwdInput = document.getElementById('password');

        if (togglePwdBtn && pwdInput) {
            togglePwdBtn.addEventListener('click', function() {
                const isPassword = pwdInput.type === 'password';
                pwdInput.type = isPassword ? 'text' : 'password';
                this.classList.toggle('fa-eye', !isPassword);
                this.classList.toggle('fa-eye-slash', isPassword);
            });
        }
    </script>
</body>
</html>
