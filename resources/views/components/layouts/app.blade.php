<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Título de tu aplicación</title>
    <!-- Aquí puedes incluir tus estilos CSS, scripts, etc. -->
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body>
    <!-- Aquí puedes incluir el contenido del componente -->
    {{ $slot }}
    <!-- Fin del contenido del componente -->

    <!-- Aquí puedes incluir tus scripts JavaScript, etc. -->
    <script src="{{ asset('js/app.js') }}"></script>
    <script src="{{ mix('js/alpine.js') }}"></script>
</body>
</html>
