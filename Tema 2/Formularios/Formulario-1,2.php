<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>

<form method="request" action="<?php echo $_SERVER['PHP_SELF']; ?>">
Introduce el texto <br/>
<input type="text" name="texto"><br/>
<input type="submit">
<?php
    if (empty($_REQUEST['texto'])) {
        echo "No se encuentra una variable definida";
    } else {
        echo "La variable 'texto' está definida y su valor es: " . htmlspecialchars($_REQUEST['texto']);
    }

?>
</form>
<br>
El texto es: <?php 
    if (!empty($_REQUEST['texto'])) {
        echo htmlspecialchars($_REQUEST['texto']); 
    }
?><br>
</body>
</html>