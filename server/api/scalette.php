<?php

include("../config/connessione.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $id_artista = isset($_POST["id_artista"]) ? (int)$_POST["id_artista"] : 0;
    $nome_scaletta = isset($_POST["nome_scaletta"]) ? trim($_POST["nome_scaletta"]) : "";
    $descrizione = isset($_POST["descrizione"]) && $_POST["descrizione"] !== "" ? trim($_POST["descrizione"]) : null;
    $durata_totale = isset($_POST["durata_totale"]) && $_POST["durata_totale"] !== "" ? $_POST["durata_totale"] : null;
    $note = isset($_POST["note"]) && $_POST["note"] !== "" ? trim($_POST["note"]) : null;

    if ($id_artista <= 0 || $nome_scaletta == "") {
        die("Compila i campi obbligatori.");
    }

    $nome_scaletta = $connessione->real_escape_string($nome_scaletta);

    $descrizione_sql = ($descrizione !== null) ? "'" . $connessione->real_escape_string($descrizione) . "'" : "NULL";
    $durata_sql = ($durata_totale !== null) ? "'" . $connessione->real_escape_string($durata_totale) . "'" : "NULL";
    $note_sql = ($note !== null) ? "'" . $connessione->real_escape_string($note) . "'" : "NULL";

    $query = "INSERT INTO scalette
              (id_artista, nome_scaletta, descrizione, durata_totale, note)
              VALUES
              ($id_artista, '$nome_scaletta', $descrizione_sql, $durata_sql, $note_sql)";

    if ($connessione->query($query) === TRUE) {
        header("Location: ../../client/inserisci_scaletta.php");
        exit();
    } else {
        echo "Errore nell'inserimento della scaletta: " . $connessione->error;
    }

} else {
    echo "Richiesta non valida.";
}

?>