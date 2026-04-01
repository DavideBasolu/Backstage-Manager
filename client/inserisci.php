<?php include("components/navbar.php"); ?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Inserisci - Backstage Manager</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-5">
    <h2 class="mb-4">Inserisci dati</h2>

    <div class="row g-4">

        <div class="col-md-4">
            <a href="inserisci_artista.php" class="btn btn-dark w-100 p-3">Inserisci artista</a>
        </div>

        <div class="col-md-4">
            <a href="inserisci_album.php" class="btn btn-dark w-100 p-3">Inserisci progetto</a>
        </div>

        <div class="col-md-4">
            <a href="inserisci_brano.php" class="btn btn-dark w-100 p-3">Inserisci brano</a>
        </div>

        <div class="col-md-4">
            <a href="aggiungi_brani_album.php" class="btn btn-dark w-100 p-3">Aggiungi brani a progetto</a>
        </div>

        <div class="col-md-4">
            <a href="inserisci_evento.php" class="btn btn-dark w-100 p-3">Inserisci evento</a>
        </div>

        <div class="col-md-4">
            <a href="aggiungi_brani_scaletta.php" class="btn btn-dark w-100 p-3">
        Aggiungi brani a scaletta</a>
        </div>

        <div class="col-md-4">
            <a href="inserisci_scaletta.php" class="btn btn-dark w-100 p-3">Inserisci scaletta</a>
        </div>

    </div>
</div>

</body>
</html>