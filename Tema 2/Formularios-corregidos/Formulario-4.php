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
        <option value="dolar">Dolar estadounidense</option>
        <option value="euro">Euro</option>
        <option value="yen">Yen Japonés</option>
        <option value="franco">Franco Suizo</option>
        <option value="libra">Libra esterlina</option>
    </select>
    <select name="numero">
        <option value="1">1</option>
        <option value="2">2</option>
        <option value="3">3</option>
        <option value="4">4</option>
        <option value="5">5</option>
        <option value="6">6</option>
        <option value="7">7</option>
        <option value="8">8</option>
        <option value="9">9</option>
        <option value="10">10</option>
        <option value="11">11</option>
        <option value="12">12</option>
        <option value="13">13</option>
        <option value="14">14</option>
        <option value="15">15</option>
        <option value="16">16</option>
        <option value="17">17</option>
        <option value="18">18</option>
        <option value="19">19</option>
        <option value="20">20</option>
    </select>
    <input type="submit">
    </form>
    <?php        
        
        $numero = $_REQUEST['numero'];
        if (empty($numero) && isset($numero) && is_numeric($numero)) {
            echo "No se encuentra una variable definida";
        } else {
            echo "</br>";
            echo "El numero generado es: " . $numero;
        } 
        
        
        $menu_monedas = $_REQUEST['menu_monedas'];

        if (empty($menu_monedas) && isset($menu_monedas) && is_string($menu_monedas)) {
            echo "No se encuentra una variable definida";
        } else {
            echo " y la moneda seleccionada es:  " . $menu_monedas;
            echo "</br>";
            echo "</br>";
        } 


        $dolar= "./monedas/dolar.jpg";
        $dolar_cruz= "./monedas/cruz_dolar.jpg";
        $euro= "./monedas/euro.jpg";
        $euro_cruz = "./monedas/cruz_euro.jpg";
        $yen= "./monedas/yen.jpg";
        $yen_cruz ="./monedas/cruz_yen.jpg";
        $franco= "./monedas/franco.jpg";
        $franco_cruz = "./monedas/cruz_franco.jpg";
        $libra= "./monedas/libra.jpg";
        $libra_cruz= "./monedas/cruz_libra.jpg";


        for ($x=1; $x <= $numero; $x++) {

            $aleatorio = rand(1, 2);
            if ($aleatorio == 1 && $menu_monedas == "dolar") {
                $imagenMostrar = $dolar;
            } elseif ($aleatorio == 2 && $menu_monedas == "dolar") {
                $imagenMostrar = $dolar_cruz;
            } elseif ($aleatorio == 1 && $menu_monedas == "euro") {
                $imagenMostrar = $euro;
            } elseif ($aleatorio == 2 && $menu_monedas == "euro") {
                $imagenMostrar = $euro_cruz;
            }  elseif ($aleatorio == 1 && $menu_monedas == "yen") {
                $imagenMostrar = $yen;
            } elseif ($aleatorio == 2 && $menu_monedas == "yen") {
                $imagenMostrar = $yen_cruz;
            }  elseif ($aleatorio == 1 && $menu_monedas == "franco") {
                $imagenMostrar = $franco;
            } elseif ($aleatorio == 2 && $menu_monedas == "franco") {
                $imagenMostrar = $franco_cruz;
            }  elseif ($aleatorio == 1 && $menu_monedas == "libra") {
                $imagenMostrar = $libra;
            } elseif ($aleatorio == 2 && $menu_monedas == "libra") {
                $imagenMostrar = $libra_cruz;
            } else {
                echo "No has seleccionado moneda";
            }
            echo "<img src= '$imagenMostrar' width: '200px'; height: '200px'/>";
        
    }
        
    ?>




</body>
</html>