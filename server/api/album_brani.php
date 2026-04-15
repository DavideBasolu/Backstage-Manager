<?php
include("../config/connessione.php");

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    echo "Richiesta non valida.";
    exit;
}

$id_album = isset($_POST["id_album"]) ? (int)$_POST["id_album"] : 0;
$brani_selezionati = isset($_POST["brani_selezionati"]) ? $_POST["brani_selezionati"] : [];
$numeri_traccia = isset($_POST["numero_traccia"]) ? $_POST["numero_traccia"] : [];
$note_brani = isset($_POST["note"]) ? $_POST["note"] : [];

if ($id_album <= 0) {
    echo "Album non valido.";
    exit;
}

if (empty($brani_selezionati) || !is_array($brani_selezionati)) {
    header("Location: ../../client/aggiungi_brani_album.php?id_album=$id_album&errore=nessun_brano");
    exit;
}

foreach ($brani_selezionati as $id_brano_raw) {
    $id_brano = (int)$id_brano_raw;
    $numero_traccia = isset($numeri_traccia[$id_brano]) ? (int)$numeri_traccia[$id_brano] : 0;

    if ($id_brano <= 0 || $numero_traccia <= 0) {
        header("Location: ../../client/aggiungi_brani_album.php?id_album=$id_album&errore=campi_mancanti");
        exit;
    }
}

$tracce_usate = [];

foreach ($brani_selezionati as $id_brano_raw) {
    $id_brano = (int)$id_brano_raw;
    $numero_traccia = (int)$numeri_traccia[$id_brano];

    if (in_array($numero_traccia, $tracce_usate)) {
        header("Location: ../../client/aggiungi_brani_album.php?id_album=$id_album&errore=conflitto_inserimento");
        exit;
    }

    $tracce_usate[] = $numero_traccia;
}

foreach ($brani_selezionati as $id_brano_raw) {
    $id_brano = (int)$id_brano_raw;
    $numero_traccia = (int)$numeri_traccia[$id_brano];

    $query_brano_presente = "
        SELECT id_brano
        FROM album_brani
        WHERE id_album = $id_album AND id_brano = $id_brano
    ";
    $result_brano_presente = $connessione->query($query_brano_presente);

    if ($result_brano_presente && $result_brano_presente->num_rows > 0) {
        header("Location: ../../client/aggiungi_brani_album.php?id_album=$id_album&errore=duplicato_brano");
        exit;
    }

    $query_traccia_occupata = "
        SELECT id_brano
        FROM album_brani
        WHERE id_album = $id_album AND numero_traccia = $numero_traccia
    ";
    $result_traccia_occupata = $connessione->query($query_traccia_occupata);

    if ($result_traccia_occupata && $result_traccia_occupata->num_rows > 0) {
        header("Location: ../../client/aggiungi_brani_album.php?id_album=$id_album&errore=posizione_occupata");
        exit;
    }
}

foreach ($brani_selezionati as $id_brano_raw) {
    $id_brano = (int)$id_brano_raw;
    $numero_traccia = (int)$numeri_traccia[$id_brano];
    $nota = isset($note_brani[$id_brano]) && trim($note_brani[$id_brano]) !== ""
        ? "'" . $connessione->real_escape_string(trim($note_brani[$id_brano])) . "'"
        : "NULL";

    $query_insert = "
        INSERT INTO album_brani (id_album, id_brano, numero_traccia, note)
        VALUES ($id_album, $id_brano, $numero_traccia, $nota)
    ";

    if (!$connessione->query($query_insert)) {
        header("Location: ../../client/aggiungi_brani_album.php?id_album=$id_album&errore=errore_inserimento");
        exit;
    }
}

header("Location: ../../client/aggiungi_brani_album.php?id_album=$id_album&brani_aggiunti=1");
exit;
?>