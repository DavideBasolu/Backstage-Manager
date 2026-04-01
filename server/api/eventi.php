<?php
include("../config/connessione.php");

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    echo "Richiesta non valida.";
    exit;
}

$id_artista = isset($_POST["id_artista"]) ? (int)$_POST["id_artista"] : 0;
$nome_evento = isset($_POST["nome_evento"]) ? trim($_POST["nome_evento"]) : "";
$data_evento = isset($_POST["data_evento"]) ? trim($_POST["data_evento"]) : "";
$luogo = isset($_POST["luogo"]) ? trim($_POST["luogo"]) : "";
$cachet = isset($_POST["cachet"]) && $_POST["cachet"] !== "" ? (float)$_POST["cachet"] : "NULL";
$id_scaletta = isset($_POST["id_scaletta"]) && $_POST["id_scaletta"] !== "" ? (int)$_POST["id_scaletta"] : "NULL";
$artisti_ospiti = isset($_POST["artisti_ospiti"]) ? $_POST["artisti_ospiti"] : [];

if ($id_artista <= 0 || $nome_evento === "" || $data_evento === "" || $luogo === "") {
    header("Location: ../../client/inserisci_evento.php?errore=1");
    exit;
}

$nome_evento = mysqli_real_escape_string($connessione, $nome_evento);
$luogo = mysqli_real_escape_string($connessione, $luogo);

$cachet_sql = ($cachet === "NULL") ? "NULL" : $cachet;
$id_scaletta_sql = ($id_scaletta === "NULL") ? "NULL" : $id_scaletta;

$query_evento = "
    INSERT INTO eventi (id_artista, nome_evento, data_evento, luogo, cachet, id_scaletta)
    VALUES ($id_artista, '$nome_evento', '$data_evento', '$luogo', $cachet_sql, $id_scaletta_sql)
";

if (!$connessione->query($query_evento)) {
    header("Location: ../../client/inserisci_evento.php?errore=1");
    exit;
}

$id_evento = $connessione->insert_id;

if (!empty($artisti_ospiti)) {
    foreach ($artisti_ospiti as $id_ospite_raw) {
        $id_ospite = (int)$id_ospite_raw;

        if ($id_ospite <= 0 || $id_ospite === $id_artista) {
            continue;
        }

        $query_check = "
            SELECT 1
            FROM eventi_artisti
            WHERE id_evento = $id_evento AND id_artista = $id_ospite
            LIMIT 1
        ";
        $result_check = $connessione->query($query_check);

        if ($result_check && $result_check->num_rows > 0) {
            continue;
        }

        $query_ospite = "
            INSERT INTO eventi_artisti (id_evento, id_artista, ruolo_evento)
            VALUES ($id_evento, $id_ospite, 'ospite')
        ";

        $connessione->query($query_ospite);
    }
}

header("Location: ../../client/inserisci_evento.php?success=1");
exit;
?>