<?php
    $productos = [
        "plancha" => 20,
        "tostadora" => 30,
        "boli" => 50,
        "aspiradora" => 12,
    ];

    function mostrarArray ($productos) {
        foreach ($productos as $producto => $valor) {
            echo "Del producto $producto hay $valor unidades";
            echo "</br>";
        }
    }

    function agregarUnidad (&$productos, $producto, $cantidad) {
        foreach ($productos as $nombre => $valor) {
            if ($nombre == $producto) {
                echo "El producto '$producto' ya existe en el inventario.<br>";
            return; // Salimos de la función si el producto ya existe
        }
    }

    $productos[$producto] = $cantidad;
    echo "Producto '$producto' agregado con cantidad $cantidad.<br>";
    }

    function actualizarCantidad (&$productos, $producto, $cantidad) {
        foreach ($productos as $nombre => $valor) {
            if ($nombre == $producto) {
                $productos[$producto] =$cantidad;
                echo "Cantidad de $producto ha sido actualizada a $cantidad </br>";
                return;
            }
        }

        echo "El producto $producto no existe en el inventario </br>";
    }

    function eliminarProducto (&$productos, $producto){
        foreach ($productos as $nombre => $valor) {
            if ($nombre == $producto){
                unset ($productos[$producto]);
                echo "Producto $producto eliminado de la tabla </br>";
                return;
            }
        }
        echo "El producto $producto no existe en la tabla </br>";
    }

    function mostrarBajos ($productos, $limite = 20) {
        foreach ($productos as $producto => $cantidad){
            if ($cantidad < $limite){
                echo "Producto: $producto | Cantidad: $cantidad </br>";
            }
        }
    }

echo "<h1>Lista principal del inventario</h1>";
mostrarArray($productos);
echo "</br>";

echo "<h1>Añadimos mas productos</h1>";

// Agregar un nuevo producto
agregarUnidad($productos, "Queso", 12);

// Intentar agregar un producto existente
agregarUnidad($productos, "Manzanas", 10);

echo "<h1>Mostramos los productos una vez añadidos</h1>";
mostrarArray ($productos);

echo "<h1>Modificamos la cantidad de los productos</h1>";
actualizarCantidad($productos, "plancha", 25);
echo "<h1>Mostramos el listado con la cantidad modificada</h1>";
mostrarArray ($productos);

eliminarProducto ($productos, "plancha");

echo "<h1>Mostrar elementos con cantidades menor a 20</h1>";
mostrarBajos($productos);



?>