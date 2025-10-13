<!DOCTYPE html>
<html>
<head>
    <title>Recuperación de Contraseña</title>
</head>
<body>
    <h2>Recuperación de Contraseña</h2>
    <p>Has solicitado restablecer tu contraseña.</p>
    <p>Utiliza el siguiente token para completar el proceso:</p>
    <h3>{{ $token }}</h3>
    <p>O si tu frontend ya está listo, podrías usar un enlace como:</p>
    <a href="http://localhost:4200/reset-password?token={{ $token }}">Restablecer contraseña</a>
</body>
</html>
