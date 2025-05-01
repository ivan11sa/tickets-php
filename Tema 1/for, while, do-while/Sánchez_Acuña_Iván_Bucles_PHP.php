<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
<style>
table, tr, td {
  border: 1px solid black;
}
</style>
    
<h1>Ejercicio 1</h1>
    <table>
        
        <?php
            for ($i=1; $i <= 10; $i++ ) {
                echo "<td>$i</td>"; 
             }    
        ?>
        
    </table> 

<h1>Ejercicio 2</h1>

    <table>
            <?php
                for ($i=1; $i <= 10; $i++ ) {
                    echo  "<td>$i</td></tr>";           }   
                /* otra opción que se puede hacer es metiendo una condicion dentro del rango para ver si un numero es par haciendo uso del modulo (%)
                  si $x % 2 == 0*/    
            ?>
    </table> 



<h1>Ejercicio 3</h1>

    <table>
            <?php
                for ($i=2; $i <= 100; $i+=2 ) {
                    echo  "<td>$i</td></tr>";           }    
            ?>
    </table> 

<h1>Ejercicio 4</h1>

    <table>
            <?php
                $e = rand (1, 10);
                for ($i=1; $i <= 10; $i++) {

                    $resultado= $i*$e;
                    echo "<tr><td>$i x $e = " . $resultado . "</td></tr>";
                }    
            ?>
    </table> 

<h1>Ejercicio 5</h1>


        <?php
            for ($i=1; $i <= 10; $i++) {
                echo "<h3>Tabla del $i</h3>";
                echo "<table>";
                for ($j = 1; $j <= 10; $j++) {
                    echo "<tr><td>$i x $j = " . ($i * $j) . "</td></tr>";
                }
                echo "</table>";
            }    
        ?>

<h1>Ejercicio 6</h1>

    <table>
        <tr>
        <?php
            $i=1;
            while ($i <= 10) {
                
                echo "<td>$i</td>"; 
                $i++;
             }    
        ?>
        </tr>
    </table> 

<h1>Ejercicio 7</h1>

    <table>
        <tr>
        <?php
            $i=1;
            do {
                echo "<td>$i</td>"; 
                $i++;  
            } while ($i <= 10);
        ?>
        </tr>
    </table> 

 

 

</body>
</html>