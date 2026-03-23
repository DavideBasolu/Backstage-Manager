<?php

include("../config/connessione.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $id_scaletta = isset($_POST["id_scaletta"]) ? (int)$_POST["id_scaletta"] : 0;
    $id_brano = isset($_POST["id_brano"]) ? (int)$_POST["id_brano"] : 0;
    $posizione = isset($_POST["posizione"]) ? (int)$_POST["posizione"] : 0;
    $note = isset($_POST["note"]) && $_POST["note"] !== "" ? trim($_POST["note"]) : null;

    if ($id_scaletta <= 0 || $id_brano <= 0 || $posizione <= 0) {
        die("Compila tutti i campi obbligatori.");
    }

    $note_sql = ($note !== null) ? "'" . $connessione->real_escape_string($note) . "'" : "NULL";

    $query_controllo = "SELECT * 
                        FROM scaletta_brani
                        WHERE id_scaletta = $id_scaletta
                        AND id_brano = $id_brano";

    $result_controllo = $connessione->query($query_controllo);

    if ($result_controllo && $result_controllo->num_rows > 0) {
        die("Questo brano è già presente nella scaletta.");
    }

    $query_posizione = "SELECT * 
                        FROM scaletta_brani
                        WHERE id_scaletta = $id_scaletta
                        AND posizione = $posizione";

    $result_posizione = $connessione->query($query_posizione);

    if ($result_posizione && $result_posizione->num_rows > 0) {
        die("Questa posizione è già occupata nella scaletta.");
    }

    $query = "INSERT INTO scaletta_brani
              (id_scaletta, id_brano, posizione, note)
              VALUES
              ($id_scaletta, $id_brano, $posizione, $note_sql)";

    if ($connessione->query($query) === TRUE) {
        header("Location: ../../client/aggiungi_brani_scaletta.php");
        exit();
    } else {
        echo "Errore nell'inserimento del brano nella scaletta: " . $connessione->error;
    }

} else {
    echo "Richiesta non valida.";
}

?>