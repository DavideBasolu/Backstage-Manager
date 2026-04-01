<?php
include("../config/connessione.php");

$id_scaletta = isset($_POST["id_scaletta"]) ? (int)$_POST["id_scaletta"] : 0;
$ordine_brani = isset($_POST["ordine_brani"]) ? trim($_POST["ordine_brani"]) : "";

if ($id_scaletta <= 0 || $ordine_brani === "") {
    echo "Dati non validi.";
    exit;
}

$ids = explode(",", $ordine_brani);

// 🔴 STEP 1: reset temporaneo (evita conflitti UNIQUE)
$connessione->query("
    UPDATE scaletta_brani
    SET posizione = posizione + 1000
    WHERE id_scaletta = $id_scaletta
");

// 🟢 STEP 2: assegna le nuove posizioni
$posizione = 1;

foreach ($ids as $id_brano_raw) {
    $id_brano = (int)$id_brano_raw;

    $query = "
        UPDATE scaletta_brani
        SET posizione = $posizione
        WHERE id_scaletta = $id_scaletta AND id_brano = $id_brano
    ";

    if (!$connessione->query($query)) {
        echo "Errore durante il riordino: " . $connessione->error;
        exit;
    }

    $posizione++;
}

header("Location: ../../client/aggiungi_brani_scaletta.php?id_scaletta=$id_scaletta&success=1");
exit;
?>