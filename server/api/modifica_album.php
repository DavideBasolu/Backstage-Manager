<?php
include("../config/connessione.php");

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    echo "Richiesta non valida.";
    exit;
}

$id_album = isset($_POST["id_album"]) ? (int)$_POST["id_album"] : 0;
$titolo_album = isset($_POST["titolo_album"]) ? trim($_POST["titolo_album"]) : "";
$data_uscita = isset($_POST["data_uscita"]) && $_POST["data_uscita"] !== "" ? trim($_POST["data_uscita"]) : null;
$tipo_album = isset($_POST["tipo_album"]) ? trim($_POST["tipo_album"]) : "";
$genere = isset($_POST["genere"]) && $_POST["genere"] !== "" ? trim($_POST["genere"]) : null;
$stato_album = isset($_POST["stato_album"]) ? trim($_POST["stato_album"]) : "";
$note = isset($_POST["note"]) && $_POST["note"] !== "" ? trim($_POST["note"]) : null;

if ($id_album <= 0) {
    header("Location: ../../client/aggiungi_brani_album.php?errore_album=album_non_trovato");
    exit;
}

if ($titolo_album === "" || $tipo_album === "" || $stato_album === "") {
    header("Location: ../../client/aggiungi_brani_album.php?id_album=$id_album&errore_album=campi_obbligatori");
    exit;
}

$query_check = "SELECT id_album FROM album WHERE id_album = $id_album LIMIT 1";
$result_check = $connessione->query($query_check);
if (!$result_check || $result_check->num_rows === 0) {
    header("Location: ../../client/aggiungi_brani_album.php?errore_album=album_non_trovato");
    exit;
}

$titolo_album_sql = $connessione->real_escape_string($titolo_album);
$tipo_album_sql = $connessione->real_escape_string($tipo_album);
$stato_album_sql = $connessione->real_escape_string($stato_album);

$data_sql = ($data_uscita !== null) ? "'" . $connessione->real_escape_string($data_uscita) . "'" : "NULL";
$genere_sql = ($genere !== null) ? "'" . $connessione->real_escape_string($genere) . "'" : "NULL";
$note_sql = ($note !== null) ? "'" . $connessione->real_escape_string($note) . "'" : "NULL";

$query_update = "
    UPDATE album SET
        titolo_album = '$titolo_album_sql',
        data_uscita = $data_sql,
        tipo_album = '$tipo_album_sql',
        genere = $genere_sql,
        stato_album = '$stato_album_sql',
        note = $note_sql
    WHERE id_album = $id_album
";

if (!$connessione->query($query_update)) {
    header("Location: ../../client/aggiungi_brani_album.php?id_album=$id_album&errore_album=salvataggio");
    exit;
}

header("Location: ../../client/aggiungi_brani_album.php?id_album=$id_album&album_salvato=1");
exit;
?>
