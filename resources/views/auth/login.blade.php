    <!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Iniciar Sesión</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f3f3f3;
        }
        .login-box {
            max-width: 400px;
            margin: 100px auto;
            padding: 30px;
            background-color: #ffffff;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
        }
        .form-label {
            font-weight: bold;
        }
        .btn-primary {
            background-color: #198754;
            border: none;
        }
    </style>
</head>
<body>

<div class="login-box">
    <h4 class="text-center mb-4">Iniciar Sesión</h4>

    @if ($errors->any())
        <div class="alert alert-danger">
            <strong>Error:</strong> {{ $errors->first('UsernameUsa') }}
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}">
        @csrf
        <div class="mb-3">
            <label for="UsernameUsa" class="form-label">Usuario</label>
            <input type="text" class="form-control" id="UsernameUsa" name="UsernameUsa" required autofocus>
        </div>

        <div class="mb-3">
            <label for="PasswordUsa" class="form-label">Contraseña</label>
            <input type="password" class="form-control" id="PasswordUsa" name="PasswordUsa" required>
        </div>

        <div class="d-grid">
            <button type="submit" class="btn btn-primary">Ingresar</button>
        </div>
    </form>
</div>

</body>
</html>
