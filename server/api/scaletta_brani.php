<?php
include("../config/connessione.php");

$id_scaletta = isset($_POST["id_scaletta"]) ? (int)$_POST["id_scaletta"] : 0;
$brani = $_POST["brani"] ?? [];
$note = $_POST["note"] ?? [];

if ($id_scaletta <= 0) {
    echo "Scaletta non valida.";
    exit;
}

if (empty($brani)) {
    header("Location: ../../client/aggiungi_brani_scaletta.php?id_scaletta=$id_scaletta&errore=nessun_brano");
    exit;
}

// Recupera la posizione massima attuale nella scaletta
$result_max = $connessione->query("
    SELECT COALESCE(MAX(posizione), 0) AS max_pos
    FROM scaletta_brani
    WHERE id_scaletta = $id_scaletta
");
$max_pos = (int)$result_max->fetch_assoc()["max_pos"];

foreach ($brani as $id_brano_raw) {
    $id_brano = (int)$id_brano_raw;

    // Salta se il brano è già presente nella scaletta
    $check_brano = $connessione->query("
        SELECT 1
        FROM scaletta_brani
        WHERE id_scaletta = $id_scaletta AND id_brano = $id_brano
        LIMIT 1
    ");

    if ($check_brano && $check_brano->num_rows > 0) {
        continue;
    }

    $max_pos++;

    $nota = isset($note[$id_brano]) ? trim($note[$id_brano]) : "";
    $nota = mysqli_real_escape_string($connessione, $nota);
    $nota_sql = ($nota !== "") ? "'$nota'" : "NULL";

    $query_insert = "
        INSERT INTO scaletta_brani (id_scaletta, id_brano, posizione, note)
        VALUES ($id_scaletta, $id_brano, $max_pos, $nota_sql)
    ";

    if (!$connessione->query($query_insert)) {
        echo "Errore durante l'inserimento: " . $connessione->error;
        exit;
    }
}

header("Location: ../../client/aggiungi_brani_scaletta.php?id_scaletta=$id_scaletta&success=1");
exit;
?>