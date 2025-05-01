<?php
    $productos = [
        "plancha" => 10.50,
        "alfombra" => 20.30,
        "ordenador" => 15.20,
        "raton" => 16.80
    ];

    function listarProductos ($productos) {
        if (count($productos) > 0) {
            echo "<table border='1'>";
            echo "<tr><th> Nombre </th><th>Precio</th></tr>";  
            foreach ($productos as $nombre => $valor) {
                echo "<tr>";
                echo "<td>$nombre</td>";
                echo "<td>$valor</td>";
                echo "</tr>";
            }    
            echo "</table>";
        }
    }

    function añadirProducto (&$productos, $nombre, $valor) {
        foreach ($productos as $producto => $precio) {
            if($producto == $nombre){
                echo "El producto $nombre ya existe";
            }
            
        }
        $productos[$nombre] =$valor;
        
        
    }

    function eliminarProducto (&$productos, $nombre){
        if ($productos[$nombre]){
            unset($productos[$nombre]);
            echo "El producto $nombre ha sido eliminado";
        } else {
            echo "El producto $nombre no se ha encontrado";
        }
    }

    function actualizarPrecio (&$productos, $nombre, $valor) {
        foreach ($productos as $producto => $valor) {
            if ($producto == $nombre) {
                $productos[$nombre] = $valor;
                echo "El precio de $nombre ha sido actualizado a $valor";
            
            } else {
                echo"No ha sido encontrado el producto";
            }
        }
    }

    function ordenarAlfabeticamente (&$productos, $orden) {
        
        if($orden == "asc"){
            ksort($productos);
        } else {
            krsort($productos);
        }

        
    }

    echo "<h1>Empezamos listando los productos</h1>";

    listarProductos($productos);

    echo "Añadimos productos";
    añadirProducto ($productos, "jamon", 30.50);
    añadirProducto ($productos, "queso", 10.50);
    añadirProducto ($productos, "miel", 7.50);

    echo "<h1>Volvemos a listar productos de nuevo</h1>";

    listarProductos($productos);

    eliminarProducto($productos, "jamon");

    echo "<h1>Volvemos a listar productos de nuevo</h1>";

    listarProductos($productos);

    echo "<h1> Listamos productos alfabeticamente</h1>";
    ordenarAlfabeticamente($productos, "asc");
    listarProductos($productos);

?>