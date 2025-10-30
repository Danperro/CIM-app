<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Iniciar Sesión</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    @livewireStyles
    <style>
        body { background-color:#f3f3f3; }
        .login-box { max-width:400px; margin:100px auto; padding:30px; background:#fff; box-shadow:0 0 15px rgba(0,0,0,.1); border-radius:10px; }
        .btn-primary { background-color:#198754; border:none; }
    </style>
</head>
<body>
<div class="login-box">
    <h4 class="text-center mb-4">Iniciar Sesión</h4>

    @if ($error)
        <div class="alert alert-danger">{{ $error }}</div>
    @endif

    {{-- Validación de Livewire --}}
    @error('username') <div class="alert alert-danger py-1 mb-2">{{ $message }}</div> @enderror
    @error('password') <div class="alert alert-danger py-1 mb-2">{{ $message }}</div> @enderror

    {{-- AQUÍ sí usas Livewire --}}
    <form wire:submit.prevent="login" class="needs-validation" novalidate>
        <div class="mb-3">
            <label for="UsernameUsa" class="form-label">Usuario</label>
            <input type="text" id="UsernameUsa" class="form-control" wire:model.live="username" required autofocus>
        </div>

        <div class="mb-3">
            <label for="PasswordUsa" class="form-label">Contraseña</label>
            <input type="password" id="PasswordUsa" class="form-control" wire:model.live="password" required>
        </div>

        <div class="d-grid">
            <button type="submit" class="btn btn-primary">Ingresar</button>
        </div>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
@livewireScripts
</body>
</html>
