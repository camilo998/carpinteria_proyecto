<?php
function connection(){
    $host = "localhost";
    $user = "root";
    $pass = "";
    $bd = "clientes";

    $connect = mysqli_connect($host, $user, $pass, $bd);

    if (!$connect) {
        die("Error de conexión: " . mysqli_connect_error());
    }

    mysqli_select_db($connect, $bd);

    return $connect;
}

?>
