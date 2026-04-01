<?php
include("../config/connessione.php");

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    echo "Richiesta non valida.";
    exit;
}

$id_brano            = isset($_POST["id_brano"]) ? (int)$_POST["id_brano"] : 0;
$return              = isset($_POST["return"]) ? trim($_POST["return"]) : "discografia";
$id_artista          = isset($_POST["id_artista"]) ? (int)$_POST["id_artista"] : 0;
$titolo              = isset($_POST["titolo"]) ? trim($_POST["titolo"]) : "";
$durata              = isset($_POST["durata"]) ? trim($_POST["durata"]) : "";
$bpm                 = isset($_POST["bpm"]) && $_POST["bpm"] !== "" ? (int)$_POST["bpm"] : null;
$tonalita            = isset($_POST["tonalita"]) && $_POST["tonalita"] !== "" ? trim($_POST["tonalita"]) : null;
$anno_pubblicazione  = isset($_POST["anno_pubblicazione"]) && $_POST["anno_pubblicazione"] !== "" ? (int)$_POST["anno_pubblicazione"] : null;
$genere_brano        = isset($_POST["genere_brano"]) && $_POST["genere_brano"] !== "" ? trim($_POST["genere_brano"]) : null;
$testo               = isset($_POST["testo"]) && $_POST["testo"] !== "" ? trim($_POST["testo"]) : null;
$stato_brano         = isset($_POST["stato_brano"]) ? trim($_POST["stato_brano"]) : "";
$note                = isset($_POST["note"]) && $_POST["note"] !== "" ? trim($_POST["note"]) : null;
$feat                = isset($_POST["feat"]) ? $_POST["feat"] : [];

if ($id_brano <= 0 || $id_artista <= 0 || $titolo === "" || $durata === "" || $stato_brano === "") {
    header("Location: ../../client/modifica_brano.php?id_brano=$id_brano&return=$return&errore=1");
    exit;
}

$titolo      = $connessione->real_escape_string($titolo);
$durata      = $connessione->real_escape_string($durata);
$stato_brano = $connessione->real_escape_string($stato_brano);

$bpm_sql    = ($bpm !== null)               ? $bpm                                                      : "NULL";
$ton_sql    = ($tonalita !== null)          ? "'" . $connessione->real_escape_string($tonalita) . "'"   : "NULL";
$anno_sql   = ($anno_pubblicazione !== null)? $anno_pubblicazione                                        : "NULL";
$genere_sql = ($genere_brano !== null)      ? "'" . $connessione->real_escape_string($genere_brano) . "'" : "NULL";
$testo_sql  = ($testo !== null)             ? "'" . $connessione->real_escape_string($testo) . "'"      : "NULL";
$note_sql   = ($note !== null)              ? "'" . $connessione->real_escape_string($note) . "'"       : "NULL";

$query_update = "
    UPDATE brani SET
        id_artista          = $id_artista,
        titolo              = '$titolo',
        durata              = '$durata',
        bpm                 = $bpm_sql,
        tonalita            = $ton_sql,
        anno_pubblicazione  = $anno_sql,
        genere_brano        = $genere_sql,
        testo               = $testo_sql,
        stato_brano         = '$stato_brano',
        note                = $note_sql
    WHERE id_brano = $id_brano
";

if (!$connessione->query($query_update)) {
    header("Location: ../../client/modifica_brano.php?id_brano=$id_brano&return=$return&errore=1");
    exit;
}

// Aggiorna i feat: cancella quelli esistenti e reinserisce
$connessione->query("DELETE FROM feat_brani WHERE id_brano = $id_brano");

if (!empty($feat) && is_array($feat)) {
    foreach ($feat as $id_feat_raw) {
        $id_feat = (int)$id_feat_raw;
        if ($id_feat > 0 && $id_feat !== $id_artista) {
            $connessione->query("
                INSERT INTO feat_brani (id_brano, id_artista)
                VALUES ($id_brano, $id_feat)
            ");
        }
    }
}

header("Location: ../../client/modifica_brano.php?id_brano=$id_brano&return=$return&success=1");
exit;
?>
