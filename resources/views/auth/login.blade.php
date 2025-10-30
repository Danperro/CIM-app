<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        * {
            box-sizing: border-box;
        }

        body {
            min-height: 100vh;
            background: linear-gradient(135deg, #198754 0%, #20c997 100%);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            margin: 0;
        }

        .login-container {
            width: 100%;
            max-width: 420px;
            margin: 0 auto;
        }

        .login-box {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            padding: 2.5rem 2rem;
            border: 1px solid rgba(255, 255, 255, 0.2);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .login-box:hover {
            transform: translateY(-5px);
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.15);
        }

        .login-header {
            text-align: center;
            margin-bottom: 2rem;
        }

        .login-icon {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, #198754, #20c997);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem;
            box-shadow: 0 10px 30px rgba(25, 135, 84, 0.3);
        }

        .login-icon i {
            font-size: 2rem;
            color: white;
        }

        .login-title {
            color: #333;
            font-weight: 700;
            font-size: 1.8rem;
            margin: 0;
        }

        .login-subtitle {
            color: #666;
            font-size: 0.95rem;
            margin-top: 0.5rem;
        }

        .form-group {
            position: relative;
            margin-bottom: 1.5rem;
        }

        .form-label {
            font-weight: 600;
            color: #555;
            font-size: 0.9rem;
            margin-bottom: 0.5rem;
        }

        .form-control {
            border: 2px solid #e1e5e9;
            border-radius: 12px;
            padding: 0.75rem 1rem 0.75rem 3rem;
            font-size: 1rem;
            transition: all 0.3s ease;
            background-color: #f8f9fa;
        }

        .form-control:focus {
            border-color: #198754;
            box-shadow: 0 0 0 0.2rem rgba(25, 135, 84, 0.15);
            background-color: white !important;
        }

        /* Nuevo: Fondo blanco cuando hay contenido en el input */
        .form-control:not(:placeholder-shown) {
            background-color: white !important;
        }

        .input-icon {
            position: absolute;
            left: 1rem;
            top: calc(50% + 12px);
            transform: translateY(-50%);
            color: #999;
            font-size: 1.1rem;
            z-index: 5;
        }

        .form-control:focus+.input-icon {
            color: #198754;
        }

        .btn-login {
            background: linear-gradient(135deg, #198754, #20c997);
            border: none;
            border-radius: 12px;
            padding: 0.75rem 2rem;
            font-weight: 600;
            font-size: 1rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            transition: all 0.3s ease;
            width: 100%;
            color: white;
            position: relative;
            overflow: hidden;
            margin-bottom: 1rem;
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 30px rgba(25, 135, 84, 0.4);
            background: linear-gradient(135deg, #157347, #1aa179);
        }

        .btn-login:active {
            transform: translateY(0);
        }

        /* Nuevo: Botón de registro */
        .btn-register {
            background: linear-gradient(135deg, #6c757d, #868e96);
            border: none;
            border-radius: 12px;
            padding: 0.75rem 2rem;
            font-weight: 600;
            font-size: 1rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            transition: all 0.3s ease;
            width: 100%;
            color: white;
            position: relative;
            overflow: hidden;
            text-decoration: none;
            display: inline-block;
            text-align: center;
        }

        .btn-register:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 30px rgba(108, 117, 125, 0.4);
            background: linear-gradient(135deg, #5a6268, #6c757d);
            color: white;
            text-decoration: none;
        }

        .btn-register:active {
            transform: translateY(0);
        }

        .divider {
            position: relative;
            text-align: center;
            margin: 1.5rem 0;
        }

        .divider::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 0;
            right: 0;
            height: 1px;
            background: linear-gradient(90deg, transparent, #ddd, transparent);
        }

        .divider span {
            background: rgba(255, 255, 255, 0.95);
            padding: 0 1rem;
            color: #666;
            font-size: 0.9rem;
        }

        .alert {
            border-radius: 12px;
            border: none;
            padding: 1rem;
            margin-bottom: 1.5rem;
            font-size: 0.9rem;
        }

        .alert-danger {
            background: #ffffff;
            color: #721c24;
            border: 1px solid #f5c6cb;
            border-left: 4px solid #dc3545;
            box-shadow: 0 2px 8px rgba(220, 53, 69, 0.1);
        }

        /* Animaciones */
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(30px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .login-box {
            animation: fadeIn 0.6s ease-out;
        }

        /* Responsive Design */
        @media (max-width: 480px) {
            body {
                padding: 15px;
            }

            .login-box {
                padding: 2rem 1.5rem;
                border-radius: 15px;
            }

            .login-icon {
                width: 60px;
                height: 60px;
            }

            .login-icon i {
                font-size: 1.5rem;
            }

            .login-title {
                font-size: 1.5rem;
            }

            .form-control {
                padding: 0.65rem 0.85rem 0.65rem 2.5rem;
            }

            .input-icon {
                left: 0.85rem;
                font-size: 1rem;
            }

            .btn-login,
            .btn-register {
                padding: 0.65rem 1.5rem;
            }
        }

        @media (max-width: 360px) {
            .login-box {
                padding: 1.5rem 1rem;
            }

            .login-title {
                font-size: 1.3rem;
            }

            .login-subtitle {
                font-size: 0.85rem;
            }
        }

        /* Dark mode support */
        @media (prefers-color-scheme: dark) {
            .login-box {
                background: rgba(30, 30, 30, 0.95);
                border: 1px solid rgba(255, 255, 255, 0.1);
            }

            .login-title {
                color: #f8f9fa;
            }

            .login-subtitle {
                color: #adb5bd;
            }

            .form-label {
                color: #dee2e6;
            }

            .form-control {
                background-color: rgba(255, 255, 255, 0.05);
                border-color: rgba(255, 255, 255, 0.1);
                color: #f8f9fa;
            }

            .form-control:focus,
            .form-control:not(:placeholder-shown) {
                background-color: white !important;
                color: #333 !important;
            }

            .input-icon {
                color: #adb5bd;
            }

            .divider span {
                background: rgba(30, 30, 30, 0.95);
                color: #adb5bd;
            }
        }
    </style>
</head>

<body>
    <div class="login-container">
        <div class="login-box">
            <div class="login-header">
                <div class="login-icon">
                    <i class="fas fa-user"></i>
                </div>
                <h4 class="login-title">Bienvenido</h4>
                <p class="login-subtitle">Ingresa tus credenciales para continuar</p>
            </div>

            @if ($errors->any())
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <strong>Error:</strong> {{ $errors->first('UsernameUsa') }}
                </div>
            @endif

            <form method="POST" action="{{ route('login.post') }}">
                @csrf
                <div class="form-group">
                    <label for="UsernameUsa" class="form-label">Usuario</label>
                    <input type="text" class="form-control" id="UsernameUsa" name="UsernameUsa" required autofocus
                        placeholder="Ingresa tu usuario">
                    <i class="fas fa-user input-icon"></i>
                </div>

                <div class="form-group">
                    <label for="PasswordUsa" class="form-label">Contraseña</label>
                    <input type="password" class="form-control" id="PasswordUsa" name="PasswordUsa" required
                        placeholder="Ingresa tu contraseña">
                    <i class="fas fa-lock input-icon"></i>
                </div>

                <div class="d-grid">
                    <button type="submit" class="btn btn-login">
                        <i class="fas fa-sign-in-alt me-2"></i>
                        Ingresar
                    </button>
                </div>
            </form>

            <div class="divider">
                <span>o</span>
            </div>

            <div class="d-grid">
                <a href="{{ route('register') }}" class="btn btn-register">
                    <i class="fas fa-user-plus me-2"></i>
                    Registrarse
                </a>
            </div>
        </div>
    </div>
</body>

</html>
