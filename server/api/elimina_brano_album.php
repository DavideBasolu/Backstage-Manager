<?php
include("../config/connessione.php");

$id_album = isset($_POST["id_album"]) ? (int)$_POST["id_album"] : 0;
$id_brano = isset($_POST["id_brano"]) ? (int)$_POST["id_brano"] : 0;

if ($id_album <= 0 || $id_brano <= 0) {
    echo "Dati non validi.";
    exit;
}

$query = "
    DELETE FROM album_brani
    WHERE id_album = $id_album AND id_brano = $id_brano
";

if (!$connessione->query($query)) {
    echo "Errore durante l'eliminazione: " . $connessione->error;
    exit;
}

header("Location: ../../client/aggiungi_brani_album.php?id_album=$id_album&brano_rimosso=1");
exit;
?>