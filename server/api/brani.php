<?php

include("../config/connessione.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $id_artista = isset($_POST["id_artista"]) ? (int)$_POST["id_artista"] : 0;
    $titolo = isset($_POST["titolo"]) ? trim($_POST["titolo"]) : "";
    $durata = isset($_POST["durata"]) ? trim($_POST["durata"]) : "";
    $bpm = isset($_POST["bpm"]) && $_POST["bpm"] !== "" ? (int)$_POST["bpm"] : null;
    $tonalita = isset($_POST["tonalita"]) && $_POST["tonalita"] !== "" ? trim($_POST["tonalita"]) : null;
    $anno_pubblicazione = isset($_POST["anno_pubblicazione"]) && $_POST["anno_pubblicazione"] !== "" ? (int)$_POST["anno_pubblicazione"] : null;
    $genere_brano = isset($_POST["genere_brano"]) && $_POST["genere_brano"] !== "" ? trim($_POST["genere_brano"]) : null;
    $testo = isset($_POST["testo"]) && $_POST["testo"] !== "" ? trim($_POST["testo"]) : null;
    $stato_brano = isset($_POST["stato_brano"]) ? trim($_POST["stato_brano"]) : "";
    $note = isset($_POST["note"]) && $_POST["note"] !== "" ? trim($_POST["note"]) : null;
    $feat = isset($_POST["feat"]) ? $_POST["feat"] : [];

    if ($id_artista <= 0 || $titolo == "" || $durata == "" || $stato_brano == "") {
        die("Compila tutti i campi obbligatori.");
    }

    $titolo = $connessione->real_escape_string($titolo);
    $durata = $connessione->real_escape_string($durata);
    $stato_brano = $connessione->real_escape_string($stato_brano);

    $tonalita_sql = ($tonalita !== null) ? "'" . $connessione->real_escape_string($tonalita) . "'" : "NULL";
    $anno_sql = ($anno_pubblicazione !== null) ? $anno_pubblicazione : "NULL";
    $genere_sql = ($genere_brano !== null) ? "'" . $connessione->real_escape_string($genere_brano) . "'" : "NULL";
    $testo_sql = ($testo !== null) ? "'" . $connessione->real_escape_string($testo) . "'" : "NULL";
    $note_sql = ($note !== null) ? "'" . $connessione->real_escape_string($note) . "'" : "NULL";
    $bpm_sql = ($bpm !== null) ? $bpm : "NULL";

    $query = "INSERT INTO brani
              (id_artista, titolo, durata, bpm, tonalita, anno_pubblicazione, genere_brano, testo, stato_brano, note)
              VALUES
              ($id_artista, '$titolo', '$durata', $bpm_sql, $tonalita_sql, $anno_sql, $genere_sql, $testo_sql, '$stato_brano', $note_sql)";

    if ($connessione->query($query) === TRUE) {

        $id_brano = $connessione->insert_id;

        if (!empty($feat) && is_array($feat)) {
            foreach ($feat as $id_feat) {
                $id_feat = (int)$id_feat;

                if ($id_feat > 0 && $id_feat != $id_artista) {
                    $query_feat = "INSERT INTO feat_brani (id_brano, id_artista)
                                   VALUES ($id_brano, $id_feat)";
                    $connessione->query($query_feat);
                }
            }
        }

        header("Location: ../../client/inserisci_brano.php");
        exit();

    } else {
        echo "Errore nell'inserimento del brano: " . $connessione->error;
    }
} else {
    echo "Richiesta non valida.";
}

?>