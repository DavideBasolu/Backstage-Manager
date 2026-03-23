<?php

include("../config/connessione.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $nome = isset($_POST["nome"]) ? trim($_POST["nome"]) : "";
    $cognome = isset($_POST["cognome"]) ? trim($_POST["cognome"]) : "";
    $nome_arte = isset($_POST["nome_arte"]) ? trim($_POST["nome_arte"]) : "";
    $genere_musicale = isset($_POST["genere_musicale"]) && $_POST["genere_musicale"] !== "" ? trim($_POST["genere_musicale"]) : null;
    $email = isset($_POST["email"]) && $_POST["email"] !== "" ? trim($_POST["email"]) : null;
    $telefono = isset($_POST["telefono"]) && $_POST["telefono"] !== "" ? trim($_POST["telefono"]) : null;
    $citta = isset($_POST["citta"]) && $_POST["citta"] !== "" ? trim($_POST["citta"]) : null;
    $note = isset($_POST["note"]) && $_POST["note"] !== "" ? trim($_POST["note"]) : null;
    $id_etichetta = isset($_POST["id_etichetta"]) && $_POST["id_etichetta"] !== "" ? (int)$_POST["id_etichetta"] : null;

    if ($nome == "" || $cognome == "" || $nome_arte == "") {
        die("Compila tutti i campi obbligatori.");
    }

    $nome = $connessione->real_escape_string($nome);
    $cognome = $connessione->real_escape_string($cognome);
    $nome_arte = $connessione->real_escape_string($nome_arte);

    $genere_musicale_sql = ($genere_musicale !== null) ? "'" . $connessione->real_escape_string($genere_musicale) . "'" : "NULL";
    $email_sql = ($email !== null) ? "'" . $connessione->real_escape_string($email) . "'" : "NULL";
    $telefono_sql = ($telefono !== null) ? "'" . $connessione->real_escape_string($telefono) . "'" : "NULL";
    $citta_sql = ($citta !== null) ? "'" . $connessione->real_escape_string($citta) . "'" : "NULL";
    $note_sql = ($note !== null) ? "'" . $connessione->real_escape_string($note) . "'" : "NULL";
    $id_etichetta_sql = ($id_etichetta !== null) ? $id_etichetta : "NULL";

    $query = "INSERT INTO artisti
              (nome_arte, genere_musicale, email, telefono, citta, note, nome, cognome, id_etichetta)
              VALUES
              ('$nome_arte', $genere_musicale_sql, $email_sql, $telefono_sql, $citta_sql, $note_sql, '$nome', '$cognome', $id_etichetta_sql)";

    if ($connessione->query($query) === TRUE) {
        header("Location: ../../client/inserisci_artista.php");
        exit();
    } else {
        echo "Errore nell'inserimento dell'artista: " . $connessione->error;
    }

} else {
    echo "Richiesta non valida.";
}

?>