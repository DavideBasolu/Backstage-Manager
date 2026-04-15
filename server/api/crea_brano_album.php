<?php
include("../config/connessione.php");

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    echo "Richiesta non valida.";
    exit;
}

$id_album             = isset($_POST["id_album"]) ? (int)$_POST["id_album"] : 0;
$titolo               = isset($_POST["titolo"]) ? trim($_POST["titolo"]) : "";
$durata               = isset($_POST["durata"]) ? trim($_POST["durata"]) : "";
$bpm                  = isset($_POST["bpm"]) && $_POST["bpm"] !== "" ? (int)$_POST["bpm"] : null;
$tonalita             = isset($_POST["tonalita"]) && $_POST["tonalita"] !== "" ? trim($_POST["tonalita"]) : null;
$anno_pubblicazione   = isset($_POST["anno_pubblicazione"]) && $_POST["anno_pubblicazione"] !== "" ? (int)$_POST["anno_pubblicazione"] : null;
$genere_brano         = isset($_POST["genere_brano"]) && $_POST["genere_brano"] !== "" ? trim($_POST["genere_brano"]) : null;
$stato_brano          = isset($_POST["stato_brano"]) ? trim($_POST["stato_brano"]) : "";
$testo                = isset($_POST["testo"]) && $_POST["testo"] !== "" ? trim($_POST["testo"]) : null;
$note                 = isset($_POST["note"]) && $_POST["note"] !== "" ? trim($_POST["note"]) : null;
$numero_traccia       = isset($_POST["numero_traccia"]) ? (int)$_POST["numero_traccia"] : 0;
$note_album_brano     = isset($_POST["note_album_brano"]) && $_POST["note_album_brano"] !== "" ? trim($_POST["note_album_brano"]) : null;
$feat                 = isset($_POST["feat"]) && is_array($_POST["feat"]) ? $_POST["feat"] : [];

if ($id_album <= 0 || $titolo === "" || $durata === "" || $stato_brano === "" || $numero_traccia <= 0) {
    header("Location: ../../client/aggiungi_brani_album.php?id_album=$id_album&errore=campi_nuovo_brano");
    exit;
}

// recupero artista dell'album
$query_album = "
    SELECT id_artista
    FROM album
    WHERE id_album = $id_album
";
$result_album = $connessione->query($query_album);

if (!$result_album || $result_album->num_rows === 0) {
    header("Location: ../../client/aggiungi_brani_album.php?id_album=$id_album&errore=album_non_trovato");
    exit;
}

$dati_album = $result_album->fetch_assoc();
$id_artista = (int)$dati_album["id_artista"];

// controllo traccia già occupata
$query_controllo = "
    SELECT id_brano
    FROM album_brani
    WHERE id_album = $id_album AND numero_traccia = $numero_traccia
";
$result_controllo = $connessione->query($query_controllo);

if ($result_controllo && $result_controllo->num_rows > 0) {
    header("Location: ../../client/aggiungi_brani_album.php?id_album=$id_album&errore=traccia_occupata");
    exit;
}

$titolo_sql = $connessione->real_escape_string($titolo);
$durata_sql = $connessione->real_escape_string($durata);
$stato_sql = $connessione->real_escape_string($stato_brano);

$bpm_sql = ($bpm !== null) ? $bpm : "NULL";
$tonalita_sql = ($tonalita !== null) ? "'" . $connessione->real_escape_string($tonalita) . "'" : "NULL";
$anno_sql = ($anno_pubblicazione !== null) ? $anno_pubblicazione : "NULL";
$genere_sql = ($genere_brano !== null) ? "'" . $connessione->real_escape_string($genere_brano) . "'" : "NULL";
$testo_sql = ($testo !== null) ? "'" . $connessione->real_escape_string($testo) . "'" : "NULL";
$note_sql = ($note !== null) ? "'" . $connessione->real_escape_string($note) . "'" : "NULL";
$note_album_brano_sql = ($note_album_brano !== null) ? "'" . $connessione->real_escape_string($note_album_brano) . "'" : "NULL";

// inserimento nuovo brano
$query_insert_brano = "
    INSERT INTO brani (
        id_artista,
        titolo,
        durata,
        bpm,
        tonalita,
        anno_pubblicazione,
        genere_brano,
        testo,
        stato_brano,
        note
    ) VALUES (
        $id_artista,
        '$titolo_sql',
        '$durata_sql',
        $bpm_sql,
        $tonalita_sql,
        $anno_sql,
        $genere_sql,
        $testo_sql,
        '$stato_sql',
        $note_sql
    )
";

if (!$connessione->query($query_insert_brano)) {
    header("Location: ../../client/aggiungi_brani_album.php?id_album=$id_album&errore=errore_creazione_brano");
    exit;
}

$id_brano = $connessione->insert_id;

// inserimento feat
if (!empty($feat)) {
    foreach ($feat as $id_feat_raw) {
        $id_feat = (int)$id_feat_raw;

        if ($id_feat > 0 && $id_feat !== $id_artista) {
            $query_feat = "
                INSERT INTO feat_brani (id_brano, id_artista)
                VALUES ($id_brano, $id_feat)
            ";
            $connessione->query($query_feat);
        }
    }
}

// collegamento all'album
$query_album_brano = "
    INSERT INTO album_brani (id_album, id_brano, numero_traccia, note)
    VALUES ($id_album, $id_brano, $numero_traccia, $note_album_brano_sql)
";

if (!$connessione->query($query_album_brano)) {
    header("Location: ../../client/aggiungi_brani_album.php?id_album=$id_album&errore=errore_collegamento_album");
    exit;
}

header("Location: ../../client/aggiungi_brani_album.php?id_album=$id_album&brano_creato=1");
exit;
?>