<?php
include("../server/config/connessione.php");
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Artisti - Backstage Manager</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="home.php">Backstage Manager</a>
            <div class="navbar-nav ms-auto">
                <a class="nav-link" href="artisti.php">Artisti</a>
                <a class="nav-link" href="eventi.php">Eventi</a>
                <a class="nav-link" href="inserisci_artista.php">Inserisci artista</a>
                <a class="nav-link" href="inserisci_evento.php">Inserisci evento</a>
                <a class="nav-link text-danger" href="index.php">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container mt-5">
        <div class="bg-white p-4 rounded shadow-sm">
            <h2 class="mb-4">Lista Artisti</h2>

            <?php
            $query = "SELECT * FROM artisti";
            $result = $connessione->query($query);

            if ($result->num_rows > 0) {
                echo "<div class='table-responsive'>";
                echo "<table class='table table-striped table-bordered'>";
                echo "<thead class='table-dark'>
                        <tr>
                            <th>ID</th>
                            <th>Nome</th>
                            <th>Cognome</th>
                            <th>Nome d'arte</th>
                            <th>Genere</th>
                            <th>Email</th>
                            <th>Telefono</th>
                            <th>Città</th>
                        </tr>
                      </thead><tbody>";

                while($row = $result->fetch_assoc()) {
                    echo "<tr>
                            <td>{$row['id_artista']}</td>
                            <td>{$row['nome']}</td>
                            <td>{$row['cognome']}</td>
                            <td>{$row['nome_arte']}</td>
                            <td>{$row['genere_musicale']}</td>
                            <td>{$row['email']}</td>
                            <td>{$row['telefono']}</td>
                            <td>{$row['citta']}</td>
                          </tr>";
                }

                echo "</tbody></table></div>";
            } else {
                echo "<p>Nessun artista trovato.</p>";
            }
            ?>
        </div>
    </div>

</body>
</html>