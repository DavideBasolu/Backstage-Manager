<?php

include("../config/connessione.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $nome_etichetta = isset($_POST["nome_etichetta"]) ? trim($_POST["nome_etichetta"]) : "";
    $referente = isset($_POST["referente"]) && $_POST["referente"] !== "" ? trim($_POST["referente"]) : null;
    $telefono = isset($_POST["telefono"]) && $_POST["telefono"] !== "" ? trim($_POST["telefono"]) : null;
    $email = isset($_POST["email"]) && $_POST["email"] !== "" ? trim($_POST["email"]) : null;
    $sede = isset($_POST["sede"]) && $_POST["sede"] !== "" ? trim($_POST["sede"]) : null;
    $note = isset($_POST["note"]) && $_POST["note"] !== "" ? trim($_POST["note"]) : null;

    if ($nome_etichetta == "") {
        die("Il nome dell'etichetta è obbligatorio.");
    }

    $nome_etichetta = $connessione->real_escape_string($nome_etichetta);
    $referente_sql = ($referente !== null) ? "'" . $connessione->real_escape_string($referente) . "'" : "NULL";
    $telefono_sql = ($telefono !== null) ? "'" . $connessione->real_escape_string($telefono) . "'" : "NULL";
    $email_sql = ($email !== null) ? "'" . $connessione->real_escape_string($email) . "'" : "NULL";
    $sede_sql = ($sede !== null) ? "'" . $connessione->real_escape_string($sede) . "'" : "NULL";
    $note_sql = ($note !== null) ? "'" . $connessione->real_escape_string($note) . "'" : "NULL";

    $query = "INSERT INTO etichette
              (nome_etichetta, referente, telefono, email, sede, note)
              VALUES
              ('$nome_etichetta', $referente_sql, $telefono_sql, $email_sql, $sede_sql, $note_sql)";

    if ($connessione->query($query) === TRUE) {
        header("Location: ../../client/inserisci_etichetta.php");
        exit();
    } else {
        echo "Errore nell'inserimento dell'etichetta: " . $connessione->error;
    }

} else {
    echo "Richiesta non valida.";
}

?>