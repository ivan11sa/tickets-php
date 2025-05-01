<?php

$n = rand (0, 10);

if ( $n < 5 ) {
    echo "La nota es $n , por lo tanto el resultado es insuficiente";
} elseif ( $n < 6 ) {
    echo "La nota es $n , por lo tanto el resultado es suficiente";
    
} elseif ( $n < 7 ) {
    echo "La nota es $n , por lo tanto el resultado es bien";
    
} elseif ( $n < 9 ) {
    echo "La nota es $n , por lo tanto el resultado es notable";
    
} else {
    echo "El resultado es sobresaliente";
}


?>