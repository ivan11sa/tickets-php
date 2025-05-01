<?php
    $maximo=0;
    echo "El valor inicial de <b>\$maximo</b> es: ",$maximo,"<br><br>";
    for($i=0;$i<10;$i++){
        echo "<u>Iteración ",$i,"</u><br>";
        $formacion[$i]=rand(1,30);
        echo "<b>\$formacion[",$i,"]</b>=",$formacion[$i],"<br>";
        echo "¿Es ",$formacion[$i]," mayor que ",$maximo,"?<br>";
        if($formacion[$i]>$maximo){
            echo "Sí.<br>";
            echo "El valor de <b>\$maximo</b> antes del cambio es: ",$maximo,"<br>";
            $maximo=$formacion[$i];
            echo "El valor de <b>\$maximo</b> después del cambio es: ",$maximo,"<br>";
        }
        else{
            echo "No.<br>";
            echo "El valor de <b>\$maximo</b> es: ",$maximo,"<br>";
        }
        echo "<br>";
    }
    echo "<pre>";
    print_r($formacion);
    echo "</pre>";
?>

<?php
    //Recorro todos los números del 1 al 200.
    for($i=2;$i<=200;$i++){
        //Asumo que todos los números son primos.
        $primo=1;
        /*Recorro los números internos del intervalo [2,$i)
        Por ejemplo: si $i=5 → recorro del 2 al 4.*/
        for($j=2;$j<= sqrt($i);$j++)
            //Compruebo si el número ($i) es primo.
            if($i%$j==0){
                //Si se cumple la condición significa que no es primo.
                $primo=0;
                //Igualo para salir del bucle.
                break;
            }
        //Si es primo lo muestro por pantalla.
        if($primo==1)
            echo "$i ";
    }
?>

<?php
    $maximo = 0;

    for ($i=0; $i < 10; $i++) {
        $formula[$i]= rand(1,30);
        if ($formula[$i] > $maximo) {
            $maximo = $formula[$i];
            
        } 
    }
    echo "</br>";

    foreach ($formula as $clave => $valor) {
        echo "La posición $clave contiene el numero $valor";
        echo "</br>";
    }
    echo "El valor máximo es $maximo";

?>

<?php
    for ($i = 1; $i < 100; $i++) {
        if ($i % 2 == 0) {
            echo "<table border='1'>";
            echo "<tr>";
            echo "<td>" . $i . "</td>";
            echo "</tr>";
            echo "</table>";
        }
    }

?>
