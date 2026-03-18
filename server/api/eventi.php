<?php

include("../config/connessione.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $id_artista = $_POST["id_artista"];
    $nome_evento = $_POST["nome_evento"];
    $data_evento = $_POST["data_evento"];
    $luogo = $_POST["luogo"];
    $cachet = $_POST["cachet"];

    $query = "INSERT INTO eventi (id_artista, nome_evento, data_evento, luogo, cachet)
              VALUES ('$id_artista', '$nome_evento', '$data_evento', '$luogo', '$cachet')";

    if ($connessione->query($query) === TRUE) {
        header("Location: ../../client/eventi.php");
        exit();
    } else {
        echo "Errore: " . $connessione->error;
    }
}
?>