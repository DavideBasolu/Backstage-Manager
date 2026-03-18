<?php

$host = "localhost";
$user = "root";
$password = "";
$database = "backstage_manager";

$connessione = new mysqli($host, $user, $password, $database);

if ($connessione->connect_error) {
    die("Connessione fallita: " . $connessione->connect_error);
}

?>