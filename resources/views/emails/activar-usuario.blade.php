<!doctype html>
<html lang="es">

<body>
    <h2>Nuevo registro pendiente</h2>
    <p>Se registró un usuario y requiere activación.</p>

    <ul>
        <li><strong>Usuario:</strong> {{ $user->UsernameUsa }}</li>
        <li><strong>ID:</strong> {{ $user->IdUsa }}</li>
    </ul>

    <p>Para activar esta cuenta (el enlace caduca):</p>
    <p>
        <a href="{{ $url }}"
            style="display:inline-block;padding:10px 16px;background:#198754;color:#fff;text-decoration:none;border-radius:6px;">
            Activar usuario
        </a>
    </p>

    <p>Si el botón no funciona, copia esta URL:</p>
    <p style="word-break:break-all;">{{ $url }}</p>
</body>

</html>
