<?php
include("../config/connessione.php");

$id_album = isset($_POST["id_album"]) ? (int)$_POST["id_album"] : 0;
$ordine_brani = isset($_POST["ordine_brani"]) ? trim($_POST["ordine_brani"]) : "";

if ($id_album <= 0 || $ordine_brani === "") {
    echo "Dati non validi.";
    exit;
}

// ordine_brani è una stringa "id_brano:disco:traccia,id_brano:disco:traccia,..."
// il frontend invia i dati in questo formato per preservare disco e traccia
$voci = explode(",", $ordine_brani);

// STEP 1: reset temporaneo dei numeri traccia per evitare conflitti UNIQUE
$connessione->query("
    UPDATE album_brani
    SET numero_traccia = numero_traccia + 10000
    WHERE id_album = $id_album
");

// STEP 2: assegna le nuove posizioni mantenendo il numero disco originale
// ma ricalcolando numero_traccia in base all'ordine per disco
$per_disco = [];

foreach ($voci as $voce) {
    $parti = explode(":", $voce);
    if (count($parti) < 2) continue;
    $id_brano = (int)$parti[0];
    $disco    = (int)$parti[1];
    if ($id_brano <= 0 || $disco <= 0) continue;
    $per_disco[$disco][] = $id_brano;
}

foreach ($per_disco as $disco => $brani) {
    $traccia = 1;
    foreach ($brani as $id_brano) {
        $query = "
            UPDATE album_brani
            SET numero_traccia = $traccia
            WHERE id_album = $id_album AND id_brano = $id_brano AND disco = $disco
        ";
        if (!$connessione->query($query)) {
            echo "Errore durante il riordino: " . $connessione->error;
            exit;
        }
        $traccia++;
    }
}

header("Location: ../../client/aggiungi_brani_album.php?id_album=$id_album&success=1");
exit;
?>
