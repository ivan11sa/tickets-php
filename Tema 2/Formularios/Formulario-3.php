<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
  
<form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
Introduce un número para crear una tabla de multiplicar <br/>
<input type="text" name="numero"><br/>
<input type="submit">
<?php
    if (isset($_POST['numero']) && !empty($_POST['numero'])) {
        $numero = $_POST['numero'];
        if (is_numeric($numero)) {
            echo "La variable 'numero' está definida y su valor es: " . $numero;
        } else {
            echo "Por favor, ingresa un valor numérico.";
        }
    } else {
        echo "No se encuentra una variable definida.";
    }
?>
</form>

<?php
    if (isset($numero) && is_numeric($numero)) {
        echo "<table border='1'>";
        for ($i = 1; $i <= 10; $i++) {
            $resultado = $i * $numero;
            echo "<tr><td>$i x $numero = $resultado</td></tr>";
        }
        echo "</table>";
    }
?>

</body>


</html>