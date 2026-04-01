<?php
include("../config/connessione.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $id_artista = $_POST["id_artista"];
    $titolo_album = $_POST["titolo_album"];
    $data_uscita = !empty($_POST["data_uscita"]) ? $_POST["data_uscita"] : null;
    $tipo_album = $_POST["tipo_album"];
    $genere = !empty($_POST["genere"]) ? $_POST["genere"] : null;
    $stato_album = $_POST["stato_album"];
    $note = !empty($_POST["note"]) ? $_POST["note"] : null;

    $titolo_album = mysqli_real_escape_string($connessione, $titolo_album);
    $tipo_album = mysqli_real_escape_string($connessione, $tipo_album);
    $stato_album = mysqli_real_escape_string($connessione, $stato_album);

    if ($genere !== null) {
        $genere = mysqli_real_escape_string($connessione, $genere);
    }

    if ($note !== null) {
        $note = mysqli_real_escape_string($connessione, $note);
    }

    $data_sql = ($data_uscita !== null) ? "'$data_uscita'" : "NULL";
    $genere_sql = ($genere !== null) ? "'$genere'" : "NULL";
    $note_sql = ($note !== null) ? "'$note'" : "NULL";

    $query = "INSERT INTO album (id_artista, titolo_album, data_uscita, tipo_album, genere, stato_album, note)
              VALUES ('$id_artista', '$titolo_album', $data_sql, '$tipo_album', $genere_sql, '$stato_album', $note_sql)";

    if ($connessione->query($query) === TRUE) {
        header("Location: ../../client/inserisci_album.php?success=1");
        exit;
    } else {
        echo "Errore durante il salvataggio del progetto: " . $connessione->error;
    }
} else {
    echo "Richiesta non valida.";
}
?>