<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro</title>
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

        .register-container {
            width: 100%;
            max-width: 900px;
            margin: 0 auto;
        }

        .register-box {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            padding: 2.5rem 2rem;
            border: 1px solid rgba(255, 255, 255, 0.2);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .register-box:hover {
            transform: translateY(-5px);
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.15);
        }

        .register-header {
            text-align: center;
            margin-bottom: 2rem;
        }

        .register-icon {
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

        .register-icon i {
            font-size: 2rem;
            color: white;
        }

        .register-title {
            color: #333;
            font-weight: 700;
            font-size: 1.8rem;
            margin: 0;
        }

        .register-subtitle {
            color: #666;
            font-size: 0.95rem;
            margin-top: 0.5rem;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr;
            gap: 1.5rem;
            margin-bottom: 1.25rem;
        }

        .form-row.two-columns {
            grid-template-columns: 1fr 1fr;
        }

        .form-row.three-columns {
            grid-template-columns: 1fr 1fr 1fr;
        }

        .form-group {
            position: relative;
            margin-bottom: 0;
        }

        .section-title {
            color: #333;
            font-weight: 700;
            font-size: 1.2rem;
            margin: 2rem 0 1.5rem 0;
            padding-bottom: 0.5rem;
            border-bottom: 2px solid #198754;
            position: relative;
        }

        .section-title:first-of-type {
            margin-top: 0;
        }

        .section-title::after {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 0;
            width: 30%;
            height: 2px;
            background: linear-gradient(135deg, #198754, #20c997);
        }

        .field-error {
            color: #dc3545;
            font-size: 0.8rem;
            margin-top: 0.25rem;
            display: flex;
            align-items: center;
            gap: 0.25rem;
        }

        .field-error i {
            font-size: 0.7rem;
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
            background-color: #ffffff;
        }

        .form-control:focus {
            border-color: #198754;
            box-shadow: 0 0 0 0.2rem rgba(25, 135, 84, 0.15);
            background-color: white;
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

        .form-control:focus + .input-icon {
            color: #198754;
        }

        .btn-register {
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
        }

        .btn-register:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 30px rgba(25, 135, 84, 0.4);
            background: linear-gradient(135deg, #157347, #1aa179);
        }

        .btn-register:active {
            transform: translateY(0);
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

        .alert-success {
            background: #ffffff;
            color: #0f5132;
            border: 1px solid #badbcc;
            border-left: 4px solid #198754;
            box-shadow: 0 2px 8px rgba(25, 135, 84, 0.1);
        }

        .error-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .error-list li {
            padding: 0.25rem 0;
        }

        .back-link {
            text-align: center;
            margin-top: 1.5rem;
            padding-top: 1rem;
            border-top: 1px solid rgba(0, 0, 0, 0.1);
        }

        .back-link a {
            color: #198754;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .back-link a:hover {
            color: #157347;
            transform: translateX(-3px);
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

        .register-box {
            animation: fadeIn 0.6s ease-out;
        }

        /* Responsive Design */
        @media (min-width: 993px) {
            .register-box {
                padding: 3rem 3rem 2.5rem;
            }
        }

        @media (max-width: 992px) {
            .form-row.two-columns,
            .form-row.three-columns {
                grid-template-columns: 1fr;
            }

            .register-container {
                max-width: 520px;
            }
        }

        @media (max-width: 768px) {
            .sidebar {
                width: 100%;
            }

            .content-area {
                padding: 4rem 0.75rem 1rem;
            }

            .form-row {
                gap: 1rem;
            }
        }

        @media (max-width: 480px) {
            body {
                padding: 15px;
            }

            .register-box {
                padding: 2rem 1.5rem;
                border-radius: 15px;
            }

            .register-icon {
                width: 60px;
                height: 60px;
            }

            .register-icon i {
                font-size: 1.5rem;
            }

            .register-title {
                font-size: 1.5rem;
            }

            .form-control {
                padding: 0.65rem 0.85rem 0.65rem 2.5rem;
            }

            .input-icon {
                left: 0.85rem;
                font-size: 1rem;
            }

            .btn-register {
                padding: 0.65rem 1.5rem;
            }

            .form-row {
                gap: 0.75rem;
            }
        }

        @media (max-width: 360px) {
            .register-box {
                padding: 1.5rem 1rem;
            }

            .register-title {
                font-size: 1.3rem;
            }

            .register-subtitle {
                font-size: 0.85rem;
            }
        }

        /* Dark mode support */
        @media (prefers-color-scheme: dark) {
            .register-box {
                background: rgba(30, 30, 30, 0.95);
                border: 1px solid rgba(255, 255, 255, 0.1);
            }

            .register-title,
            .section-title {
                color: #f8f9fa;
            }

            .register-subtitle {
                color: #adb5bd;
            }

            .form-label {
                color: #dee2e6;
            }

            .form-control {
                background-color: #ffffff;
                border-color: rgba(255, 255, 255, 0.2);
                color: #333;
            }

            .form-control:focus {
                background-color: #ffffff;
                color: #333;
            }

            .input-icon {
                color: #adb5bd;
            }

            .back-link {
                border-top-color: rgba(255, 255, 255, 0.1);
            }

            .field-error {
                color: #f87171;
            }
        }
    </style>
</head>

<body>
    <div class="register-container">
        <div class="register-box">
            <div class="register-header">
                <div class="register-icon">
                    <i class="fas fa-user-plus"></i>
                </div>
                <h4 class="register-title">Crear Cuenta</h4>
                <p class="register-subtitle">Completa los datos para registrarte</p>
            </div>

            @if (session('status'))
                <div class="alert alert-success">
                    <i class="fas fa-check-circle me-2"></i>
                    {{ session('status') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <strong>Error:</strong>
                    <ul class="error-list mt-2">
                        @foreach ($errors->all() as $e)
                            <li><i class="fas fa-dot-circle me-1" style="font-size: 0.5rem;"></i>{{ $e }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('register.post') }}">
                @csrf
                
                <h3 class="section-title">Cuenta</h3>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="UsernameUsa" class="form-label">Usuario</label>
                        <input type="text" class="form-control" id="UsernameUsa" name="UsernameUsa" 
                               value="{{ old('UsernameUsa') }}" required autofocus placeholder="Ingresa tu nombre de usuario">
                        <i class="fas fa-user input-icon"></i>
                        @error('UsernameUsa')
                            <div class="field-error">
                                <i class="fas fa-exclamation-circle"></i>
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                </div>

                <div class="form-row two-columns">
                    <div class="form-group">
                        <label for="PasswordUsa" class="form-label">Contraseña</label>
                        <input type="password" class="form-control" id="PasswordUsa" name="PasswordUsa" 
                               required placeholder="Ingresa tu contraseña">
                        <i class="fas fa-lock input-icon"></i>
                    </div>

                    <div class="form-group">
                        <label for="PasswordUsa_confirmation" class="form-label">Confirmar Contraseña</label>
                        <input type="password" class="form-control" id="PasswordUsa_confirmation" name="PasswordUsa_confirmation" 
                               required placeholder="Confirma tu contraseña">
                        <i class="fas fa-shield-alt input-icon"></i>
                    </div>
                </div>
                
                @error('PasswordUsa')
                    <div class="field-error" style="margin-top: -0.75rem; margin-bottom: 1rem;">
                        <i class="fas fa-exclamation-circle"></i>
                        {{ $message }}
                    </div>
                @enderror

                <h3 class="section-title">Datos Personales</h3>

                <div class="form-row three-columns">
                    <div class="form-group">
                        <label for="NombrePer" class="form-label">Nombre</label>
                        <input type="text" class="form-control" id="NombrePer" name="NombrePer" 
                               value="{{ old('NombrePer') }}" required placeholder="Ingresa tu nombre">
                        <i class="fas fa-user-circle input-icon"></i>
                        @error('NombrePer')
                            <div class="field-error">
                                <i class="fas fa-exclamation-circle"></i>
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="ApellidoPaternoPer" class="form-label">Apellido Paterno</label>
                        <input type="text" class="form-control" id="ApellidoPaternoPer" name="ApellidoPaternoPer" 
                               value="{{ old('ApellidoPaternoPer') }}" required placeholder="Apellido paterno">
                        <i class="fas fa-user-tag input-icon"></i>
                        @error('ApellidoPaternoPer')
                            <div class="field-error">
                                <i class="fas fa-exclamation-circle"></i>
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="ApellidoMaternoPer" class="form-label">Apellido Materno</label>
                        <input type="text" class="form-control" id="ApellidoMaternoPer" name="ApellidoMaternoPer" 
                               value="{{ old('ApellidoMaternoPer') }}" required placeholder="Apellido materno">
                        <i class="fas fa-user-friends input-icon"></i>
                        @error('ApellidoMaternoPer')
                            <div class="field-error">
                                <i class="fas fa-exclamation-circle"></i>
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                </div>

                <div class="form-row two-columns">
                    <div class="form-group">
                        <label for="CorreoPer" class="form-label">Correo Electrónico</label>
                        <input type="email" class="form-control" id="CorreoPer" name="CorreoPer" 
                               value="{{ old('CorreoPer') }}" required placeholder="correo@ejemplo.com">
                        <i class="fas fa-envelope input-icon"></i>
                        @error('CorreoPer')
                            <div class="field-error">
                                <i class="fas fa-exclamation-circle"></i>
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="DniPer" class="form-label">DNI</label>
                        <input type="text" class="form-control" id="DniPer" name="DniPer" 
                               value="{{ old('DniPer') }}" required placeholder="12345678">
                        <i class="fas fa-id-card input-icon"></i>
                        @error('DniPer')
                            <div class="field-error">
                                <i class="fas fa-exclamation-circle"></i>
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                </div>

                <div class="form-row two-columns">
                    <div class="form-group">
                        <label for="TelefonoPer" class="form-label">Teléfono</label>
                        <input type="text" class="form-control" id="TelefonoPer" name="TelefonoPer" 
                               value="{{ old('TelefonoPer') }}" required placeholder="987654321">
                        <i class="fas fa-phone input-icon"></i>
                        @error('TelefonoPer')
                            <div class="field-error">
                                <i class="fas fa-exclamation-circle"></i>
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="FechaNacimientoPer" class="form-label">Fecha de Nacimiento</label>
                        <input type="date" class="form-control" id="FechaNacimientoPer" name="FechaNacimientoPer" 
                               value="{{ old('FechaNacimientoPer') }}" required>
                        <i class="fas fa-calendar-alt input-icon"></i>
                        @error('FechaNacimientoPer')
                            <div class="field-error">
                                <i class="fas fa-exclamation-circle"></i>
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                </div>

                <div class="d-grid" style="margin-top: 2rem;">
                    <button type="submit" class="btn btn-register">
                        <i class="fas fa-user-plus me-2"></i>
                        Registrarme
                    </button>
                </div>
            </form>

            <div class="back-link">
                <a href="{{ route('login') }}">
                    <i class="fas fa-arrow-left"></i>
                    Volver al login
                </a>
            </div>
        </div>
    </div>
</body>

</html>