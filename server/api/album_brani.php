<?php
include("../config/connessione.php");

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    echo "Richiesta non valida.";
    exit;
}

$id_album = isset($_POST["id_album"]) ? (int)$_POST["id_album"] : 0;
$brani_selezionati = isset($_POST["brani_selezionati"]) ? $_POST["brani_selezionati"] : [];
$numeri_traccia = isset($_POST["numero_traccia"]) ? $_POST["numero_traccia"] : [];
$dischi = isset($_POST["disco"]) ? $_POST["disco"] : [];
$note_brani = isset($_POST["note"]) ? $_POST["note"] : [];

if ($id_album <= 0) {
    header("Location: ../../client/aggiungi_brani_album.php?errore=generico");
    exit;
}

if (empty($brani_selezionati)) {
    header("Location: ../../client/aggiungi_brani_album.php?id_album=$id_album&errore=nessun_brano");
    exit;
}

$combinazioni_nuove = [];

foreach ($brani_selezionati as $id_brano_raw) {
    $id_brano = (int)$id_brano_raw;

    $numero_traccia = isset($numeri_traccia[$id_brano]) ? (int)$numeri_traccia[$id_brano] : 0;
    $disco = isset($dischi[$id_brano]) ? (int)$dischi[$id_brano] : 1;

    if ($numero_traccia <= 0 || $disco <= 0) {
        header("Location: ../../client/aggiungi_brani_album.php?id_album=$id_album&errore=campi_mancanti");
        exit;
    }

    $chiave = $disco . "-" . $numero_traccia;

    if (isset($combinazioni_nuove[$chiave])) {
        header("Location: ../../client/aggiungi_brani_album.php?id_album=$id_album&errore=conflitto_inserimento");
        exit;
    }

    $combinazioni_nuove[$chiave] = true;
}

foreach ($brani_selezionati as $id_brano_raw) {
    $id_brano = (int)$id_brano_raw;

    $query_controllo_brano = "
        SELECT 1
        FROM album_brani
        WHERE id_album = $id_album AND id_brano = $id_brano
        LIMIT 1
    ";
    $result_controllo_brano = $connessione->query($query_controllo_brano);

    if ($result_controllo_brano && $result_controllo_brano->num_rows > 0) {
        header("Location: ../../client/aggiungi_brani_album.php?id_album=$id_album&errore=duplicato_brano");
        exit;
    }
}

foreach ($brani_selezionati as $id_brano_raw) {
    $id_brano = (int)$id_brano_raw;
    $numero_traccia = (int)$numeri_traccia[$id_brano];
    $disco = (int)$dischi[$id_brano];

    $query_controllo_posizione = "
        SELECT 1
        FROM album_brani
        WHERE id_album = $id_album
          AND disco = $disco
          AND numero_traccia = $numero_traccia
        LIMIT 1
    ";
    $result_controllo_posizione = $connessione->query($query_controllo_posizione);

    if ($result_controllo_posizione && $result_controllo_posizione->num_rows > 0) {
        header("Location: ../../client/aggiungi_brani_album.php?id_album=$id_album&errore=posizione_occupata");
        exit;
    }
}

foreach ($brani_selezionati as $id_brano_raw) {
    $id_brano = (int)$id_brano_raw;
    $numero_traccia = (int)$numeri_traccia[$id_brano];
    $disco = (int)$dischi[$id_brano];
    $note = isset($note_brani[$id_brano]) ? trim($note_brani[$id_brano]) : "";

    $note = mysqli_real_escape_string($connessione, $note);
    $note_sql = ($note !== "") ? "'$note'" : "NULL";

    $query_insert = "
        INSERT INTO album_brani (id_album, id_brano, numero_traccia, disco, note)
        VALUES ($id_album, $id_brano, $numero_traccia, $disco, $note_sql)
    ";

    if (!$connessione->query($query_insert)) {
        echo "Errore durante l'inserimento: " . $connessione->error;
        exit;
    }
}

header("Location: ../../client/aggiungi_brani_album.php?id_album=$id_album&success=1");
exit;
?>