<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Punto de venta</title>
    <!-- Aquí puedes incluir tus estilos CSS, scripts, etc. -->
    @livewireStyles
    @vite(['resources/js/app.js', 'resources/css/app.css'])
</head>
<body>
    <!-- Aquí puedes incluir el contenido del componente -->
    {{ $slot }}
    <!-- Fin del contenido del componente -->
        
    @livewireScripts
    @livewireScriptConfig 
</body>
</html>
