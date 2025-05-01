<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <h1>Multiplicador de monedas</h1>


    <p>Selecciona una moneda y dale a generar</p>
    <form method="request" action="<?php echo $_SERVER['PHP_SELF']; ?>">
    <select name ="menu_monedas">
        <option value="dolar.jpg">Dolar estadounidense</option>
        <option value="euro.jpg">Euro</option>
        <option value="yen.jpg">Yen</option>
        <option value="franco.jpg">Franco Suizo</option>
        <option value="libra.jpg">Libra esterlina</option>
    </select>
    </br>
    </br>
    <button type="hidden" name="<?php $numero = rand(1, 20)?>">Generar</button>
    <?php
        if (empty($numero)) {
            echo "No se encuentra una variable definida";
        } else {
            echo "El numero generado es $numero";
        } 
    ?>

    </br>
    
    </br>
    <?php
        
        $resultado= $_REQUEST["menu_monedas"];
        $menu_monedas = $_REQUEST["menu_monedas"];

        $dolar= "dolar.jpg";
        $euro= "euro.jpg";
        $yen= "yen.jpg";
        $franco= "franco.jpg";
        $libra= "libra.jpg";

        if ($menu_monedas == "dolar.jpg") {
            $imagenMostrar = $dolar;
            $resultado = "Dolar";
        } elseif ($menu_monedas == "euro.jpg") {
            $imagenMostrar = $euro;
            $resultado = "Euro";
        } elseif ($menu_monedas == "yen.jpg") {
            $imagenMostrar = $yen;
            $resultado = "Yen";
        } elseif ($menu_monedas == "franco.jpg") {
            $imagenMostrar = $franco;
            $resultado = "Franco";
        } elseif ($menu_monedas == "libra.jpg") {
            $imagenMostrar = $libra;
            $resultado = "Libra";
        } else {
            echo "No se ha seleccionado moneda";
        }

        for ($x=1; $x <= $numero; $x++) {
            echo "<img src= '$imagenMostrar' width: '200px'; height: '200px'/>";
        }
        
    ?>


    </form>

</body>
</html>