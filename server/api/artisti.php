<?php

include("../config/connessione.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $nome = $_POST["nome"];
    $cognome = $_POST["cognome"];
    $nome_arte = $_POST["nome_arte"];
    $genere = $_POST["genere"];
    $email = $_POST["email"];
    $telefono = $_POST["telefono"];
    $citta = $_POST["citta"];

    $query = "INSERT INTO artisti (nome, cognome, nome_arte, genere_musicale, email, telefono, citta)
              VALUES ('$nome', '$cognome', '$nome_arte', '$genere', '$email', '$telefono', '$citta')";

    if ($connessione->query($query) === TRUE) {
        header("Location: ../../client/artisti.php");
        exit();
    } else {
        echo "Errore: " . $connessione->error;
    }
}
?>