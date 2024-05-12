<?php
use App\Models\Producto;
use App\Models\Iva;
// Import the Product model
// Retrieve all products


$productos = Producto::all();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Crear</title>
</head>
<body>
    <div>
        <h1>Lista de productos</h1>

        <?php foreach ($productos as $producto): ?>
            <div>
                <h2><?php echo $producto->nombre; ?></h2>
                <p>Precio: <?php echo $producto->precio; ?></p>
                <p>Descripción: <?php echo $producto->getNombreIvaAtributo()?></p>
            </div>
        <?php endforeach; ?>
</body>
</html>
