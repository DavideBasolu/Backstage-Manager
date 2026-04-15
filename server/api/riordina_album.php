<?php
include("../config/connessione.php");

$id_album = isset($_POST["id_album"]) ? (int)$_POST["id_album"] : 0;
$ordine_brani = isset($_POST["ordine_brani"]) ? trim($_POST["ordine_brani"]) : "";

if ($id_album <= 0 || $ordine_brani === "") {
    echo "Dati non validi.";
    exit;
}

$voci = explode(",", $ordine_brani);

$connessione->query("
    UPDATE album_brani
    SET numero_traccia = numero_traccia + 10000
    WHERE id_album = $id_album
");

foreach ($voci as $voce) {
    $parti = explode(":", $voce);

    if (count($parti) < 2) {
        continue;
    }

    $id_brano = (int)$parti[0];
    $traccia = (int)$parti[1];

    if ($id_brano <= 0 || $traccia <= 0) {
        continue;
    }

    $query = "
        UPDATE album_brani
        SET numero_traccia = $traccia
        WHERE id_album = $id_album AND id_brano = $id_brano
    ";

    if (!$connessione->query($query)) {
        echo "Errore durante il riordino: " . $connessione->error;
        exit;
    }
}

header("Location: ../../client/aggiungi_brani_album.php?id_album=$id_album&ordine_salvato=1");
exit;
?>