<?php include("components/navbar.php"); ?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Visualizza - Backstage Manager</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-5">
    <h2 class="mb-4">Visualizza dati</h2>

    <div class="row g-4">

        <div class="col-md-4">
            <a href="artisti.php" class="btn btn-outline-dark w-100 p-3">Artisti</a>
        </div>

        <div class="col-md-4">
            <a href="discografia.php" class="btn btn-outline-dark w-100 p-3">Discografia</a>
        </div>

        <div class="col-md-4">
            <a href="eventi.php" class="btn btn-outline-dark w-100 p-3">Eventi</a>
        </div>

        <div class="col-md-4">
            <a href="scalette.php" class="btn btn-outline-dark w-100 p-3">Scalette</a>
        </div>

    </div>
</div>

</body>
</html>