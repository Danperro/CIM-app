<div class="container mt-5" style="max-width: 400px;">
    <h3 class="text-center mb-4">Inicio de Sesión</h3>

    @if ($error)
        <div class="alert alert-danger">{{ $error }}</div>
    @endif

    <form wire:submit.prevent="login">
        <div class="mb-3">
            <label>Usuario</label>
            <input wire:model="username" type="text" class="form-control" required>
        </div>

        <div class="mb-3">
            <label>Contraseña</label>
            <input wire:model="password" type="password" class="form-control" required>
        </div>

        <button type="submit" class="btn btn-primary w-100">Iniciar Sesión</button>
    </form>
</div>
<script>
    Livewire.on('redirect', () => {
        window.location.href = "{{ route('control') }}";
    });
</script>

@if ($redirect)
    <script>
        Livewire.emit('redirect');
    </script>
@endif
