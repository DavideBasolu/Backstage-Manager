<?php
include("../config/connessione.php");

$id_scaletta = isset($_POST["id_scaletta"]) ? (int)$_POST["id_scaletta"] : 0;
$id_brano = isset($_POST["id_brano"]) ? (int)$_POST["id_brano"] : 0;

if ($id_scaletta <= 0 || $id_brano <= 0) {
    echo "Dati non validi.";
    exit;
}

$query = "
    DELETE FROM scaletta_brani
    WHERE id_scaletta = $id_scaletta AND id_brano = $id_brano
";

if (!$connessione->query($query)) {
    echo "Errore durante l'eliminazione: " . $connessione->error;
    exit;
}

$riordina = "
    SET @pos := 0;
";
$connessione->query($riordina);

$query_update = "
    UPDATE scaletta_brani sb
    JOIN (
        SELECT id_scaletta, id_brano, (@pos := @pos + 1) AS nuova_posizione
        FROM scaletta_brani
        CROSS JOIN (SELECT @pos := 0) AS variabili
        WHERE id_scaletta = $id_scaletta
        ORDER BY posizione ASC
    ) x
    ON sb.id_scaletta = x.id_scaletta AND sb.id_brano = x.id_brano
    SET sb.posizione = x.nuova_posizione
    WHERE sb.id_scaletta = $id_scaletta
";

$connessione->query($query_update);

header("Location: ../../client/aggiungi_brani_scaletta.php?id_scaletta=$id_scaletta&success=1");
exit;
?>