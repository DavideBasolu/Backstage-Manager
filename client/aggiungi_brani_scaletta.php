<?php
include("../server/config/connessione.php");

$id_scaletta = isset($_GET["id_scaletta"]) ? (int)$_GET["id_scaletta"] : 0;
$ricerca = isset($_GET["ricerca"]) ? trim($_GET["ricerca"]) : "";
$filtro_stato = isset($_GET["stato_brano"]) ? trim($_GET["stato_brano"]) : "";

// elenco scalette per selezione iniziale
$query_lista_scalette = "
    SELECT 
        s.id_scaletta,
        s.nome_scaletta,
        a.nome_arte
    FROM scalette s
    JOIN artisti a ON s.id_artista = a.id_artista
    ORDER BY a.nome_arte ASC, s.nome_scaletta ASC
";
$result_lista_scalette = $connessione->query($query_lista_scalette);

$scaletta = null;
$result_brani = null;
$brani_presenti = [];

if ($id_scaletta > 0) {
    $query_scaletta = "
        SELECT s.*, a.nome_arte
        FROM scalette s
        JOIN artisti a ON s.id_artista = a.id_artista
        WHERE s.id_scaletta = $id_scaletta
    ";
    $result_scaletta = $connessione->query($query_scaletta);

    if ($result_scaletta && $result_scaletta->num_rows > 0) {
        $scaletta = $result_scaletta->fetch_assoc();
        $id_artista = (int)$scaletta["id_artista"];

        $condizioni_brani = [];
        $condizioni_brani[] = "b.id_artista = $id_artista";

        if ($ricerca !== "") {
            $ricerca_sql = mysqli_real_escape_string($connessione, $ricerca);
            $condizioni_brani[] = "b.titolo LIKE '%$ricerca_sql%'";
        }

        if ($filtro_stato !== "") {
            $stato_sql = mysqli_real_escape_string($connessione, $filtro_stato);
            $condizioni_brani[] = "b.stato_brano = '$stato_sql'";
        }

        $where_brani = "WHERE " . implode(" AND ", $condizioni_brani);

        $query_brani = "
            SELECT 
                b.id_brano,
                b.titolo,
                b.durata,
                b.stato_brano,
                b.genere_brano,
                GROUP_CONCAT(DISTINCT af.nome_arte ORDER BY af.nome_arte SEPARATOR ', ') AS feat
            FROM brani b
            LEFT JOIN feat_brani fb ON b.id_brano = fb.id_brano
            LEFT JOIN artisti af ON fb.id_artista = af.id_artista
            $where_brani
            GROUP BY 
                b.id_brano,
                b.titolo,
                b.durata,
                b.stato_brano,
                b.genere_brano
            ORDER BY b.titolo ASC
        ";
        $result_brani = $connessione->query($query_brani);

        $query_attuale = "
            SELECT 
                sb.id_brano,
                sb.posizione,
                sb.note,
                b.titolo,
                b.durata,
                GROUP_CONCAT(DISTINCT af.nome_arte ORDER BY af.nome_arte SEPARATOR ', ') AS feat
            FROM scaletta_brani sb
            JOIN brani b ON sb.id_brano = b.id_brano
            LEFT JOIN feat_brani fb ON b.id_brano = fb.id_brano
            LEFT JOIN artisti af ON fb.id_artista = af.id_artista
            WHERE sb.id_scaletta = $id_scaletta
            GROUP BY 
                sb.id_brano,
                sb.posizione,
                sb.note,
                b.titolo,
                b.durata
            ORDER BY sb.posizione ASC
        ";
        $result_attuale = $connessione->query($query_attuale);

        if ($result_attuale && $result_attuale->num_rows > 0) {
            while ($r = $result_attuale->fetch_assoc()) {
                $brani_presenti[] = $r;
            }
        }
    } else {
        $id_scaletta = 0;
    }
}
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestisci scaletta - Backstage Manager</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .riga-trascinabile { cursor: grab; }
        .ghost { opacity: 0.4; background: #f8f9fa; }
    </style>
</head>
<body class="bg-light">

<?php include("components/navbar.php"); ?>

<div class="container mt-5 mb-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">Gestisci scaletta</h2>
        <a href="scalette.php" class="btn btn-outline-dark">Torna alle scalette</a>
    </div>

    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="aggiungi_brani_scaletta.php" class="row g-3 align-items-end">
                <div class="col-md-10">
                    <label for="id_scaletta" class="form-label">Seleziona scaletta</label>
                    <select name="id_scaletta" id="id_scaletta" class="form-select" required>
                        <option value="">Seleziona scaletta</option>
                        <?php
                        if ($result_lista_scalette && $result_lista_scalette->num_rows > 0) {
                            while ($s = $result_lista_scalette->fetch_assoc()) {
                                $selected = ($id_scaletta == $s["id_scaletta"]) ? "selected" : "";
                                echo "<option value='{$s["id_scaletta"]}' $selected>
                                        " . htmlspecialchars($s["nome_arte"]) . " - " . htmlspecialchars($s["nome_scaletta"]) . "
                                      </option>";
                            }
                        }
                        ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-dark w-100">Carica</button>
                </div>
            </form>
        </div>
    </div>

    <?php if (!$scaletta) { ?>
        <div class="alert alert-info">
            Seleziona una scaletta per aggiungere, eliminare o riordinare i brani.
        </div>
    <?php } else { ?>

        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h3 class="mb-1"><?php echo htmlspecialchars($scaletta["nome_scaletta"]); ?></h3>
                <p class="text-muted mb-0">
                    Artista: <?php echo htmlspecialchars($scaletta["nome_arte"]); ?>
                    <?php if (!empty($scaletta["durata_totale"])) { ?>
                        · Durata totale: <?php echo htmlspecialchars($scaletta["durata_totale"]); ?>
                    <?php } ?>
                </p>
            </div>
        </div>

        <?php if (isset($_GET["errore"])) { ?>
            <div class="alert alert-danger">
                <?php
                switch ($_GET["errore"]) {
                    case "nessun_brano":
                        echo "Devi selezionare almeno un brano.";
                        break;
                    default:
                        echo "Si è verificato un errore.";
                        break;
                }
                ?>
            </div>
        <?php } ?>

        <?php if (isset($_GET["success"])) { ?>
            <div class="alert alert-success">Operazione completata correttamente.</div>
        <?php } ?>

        <div class="row g-4">
            <div class="col-lg-7">
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-dark text-white">
                        <h5 class="mb-0">Filtra brani disponibili</h5>
                    </div>
                    <div class="card-body">
                        <form method="GET" action="aggiungi_brani_scaletta.php" class="row g-3">
                            <input type="hidden" name="id_scaletta" value="<?php echo $id_scaletta; ?>">

                            <div class="col-md-6">
                                <label for="ricerca" class="form-label">Cerca titolo</label>
                                <input type="text" name="ricerca" id="ricerca" class="form-control" value="<?php echo htmlspecialchars($ricerca); ?>">
                            </div>

                            <div class="col-md-4">
                                <label for="stato_brano" class="form-label">Stato</label>
                                <select name="stato_brano" id="stato_brano" class="form-select">
                                    <option value="">Tutti</option>
                                    <option value="demo" <?php if ($filtro_stato === "demo") echo "selected"; ?>>Demo</option>
                                    <option value="pubblicato" <?php if ($filtro_stato === "pubblicato") echo "selected"; ?>>Pubblicato</option>
                                    <option value="inedito" <?php if ($filtro_stato === "inedito") echo "selected"; ?>>Inedito</option>
                                    <option value="cover" <?php if ($filtro_stato === "cover") echo "selected"; ?>>Cover</option>
                                </select>
                            </div>

                            <div class="col-md-2 d-flex align-items-end">
                                <button type="submit" class="btn btn-dark w-100">Filtra</button>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="card shadow-sm">
                    <div class="card-header bg-dark text-white">
                        <h5 class="mb-0">Aggiungi brani alla scaletta</h5>
                    </div>
                    <div class="card-body">
                        <form action="../server/api/scaletta_brani.php" method="POST">
                            <input type="hidden" name="id_scaletta" value="<?php echo $id_scaletta; ?>">

                            <?php if ($result_brani && $result_brani->num_rows > 0) { ?>
                                <div class="table-responsive">
                                    <table class="table table-bordered align-middle">
                                        <thead class="table-light">
                                            <tr>
                                                <th></th>
                                                <th>Titolo</th>
                                                <th>Feat</th>
                                                <th>Durata</th>
                                                <th>Stato</th>
                                                <th>Note</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php while ($brano = $result_brani->fetch_assoc()) {
                                                $gia_presente = false;
                                                foreach ($brani_presenti as $presente) {
                                                    if ((int)$presente["id_brano"] === (int)$brano["id_brano"]) {
                                                        $gia_presente = true;
                                                        break;
                                                    }
                                                }
                                            ?>
                                                <tr class="<?php echo $gia_presente ? 'table-secondary' : ''; ?>">
                                                    <td class="text-center">
                                                        <?php if ($gia_presente) { ?>
                                                            <span class="badge bg-secondary">Già presente</span>
                                                        <?php } else { ?>
                                                            <input type="checkbox" name="brani[]" value="<?php echo $brano["id_brano"]; ?>" class="form-check-input check-brano">
                                                        <?php } ?>
                                                    </td>

                                                    <td>
                                                        <strong><?php echo htmlspecialchars($brano["titolo"]); ?></strong>
                                                        <?php if (!empty($brano["genere_brano"])) { ?>
                                                            <br><small class="text-muted"><?php echo htmlspecialchars($brano["genere_brano"]); ?></small>
                                                        <?php } ?>
                                                    </td>

                                                    <td><?php echo !empty($brano["feat"]) ? htmlspecialchars($brano["feat"]) : "-"; ?></td>
                                                    <td><?php echo htmlspecialchars($brano["durata"]); ?></td>
                                                    <td><?php echo htmlspecialchars($brano["stato_brano"]); ?></td>

                                                    <td style="min-width:180px;">
                                                        <?php if (!$gia_presente) { ?>
                                                            <input type="text" name="note[<?php echo $brano["id_brano"]; ?>]" class="form-control campo" disabled>
                                                        <?php } ?>
                                                    </td>
                                                </tr>
                                            <?php } ?>
                                        </tbody>
                                    </table>
                                </div>

                                <div class="d-flex justify-content-end">
                                    <button class="btn btn-dark">Aggiungi alla scaletta</button>
                                </div>
                            <?php } else { ?>
                                <div class="alert alert-warning mb-0">Nessun brano trovato con i filtri selezionati.</div>
                            <?php } ?>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-lg-5">
                <div class="card shadow-sm">
                    <div class="card-header bg-secondary text-white d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Tracklist attuale</h5>
                        <small>Trascina per riordinare</small>
                    </div>
                    <div class="card-body">
                        <?php if (count($brani_presenti) > 0) { ?>
                            <ul class="list-group" id="lista-tracklist">
                                <?php foreach ($brani_presenti as $r) { ?>
                                    <li class="list-group-item riga-trascinabile d-flex justify-content-between align-items-start"
                                        data-id-brano="<?php echo (int)$r["id_brano"]; ?>">
                                        <div class="me-3">
                                            <strong><?php echo htmlspecialchars($r["titolo"]); ?></strong><br>
                                            <small class="text-muted">
                                                Posizione attuale: <span class="numero-posizione"><?php echo (int)$r["posizione"]; ?></span>
                                                <?php if (!empty($r["durata"])) { ?> · <?php echo htmlspecialchars($r["durata"]); ?><?php } ?>
                                            </small>
                                            <?php if (!empty($r["feat"])) { ?>
                                                <br><small class="text-muted">feat. <?php echo htmlspecialchars($r["feat"]); ?></small>
                                            <?php } ?>
                                            <?php if (!empty($r["note"])) { ?>
                                                <br><small class="text-secondary">Note: <?php echo htmlspecialchars($r["note"]); ?></small>
                                            <?php } ?>
                                        </div>

                                        <div class="d-flex flex-column gap-2">
                                            <span class="badge bg-dark">#<?php echo (int)$r["posizione"]; ?></span>

                                            <form action="../server/api/elimina_brano_scaletta.php" method="POST" onsubmit="return confirm('Vuoi eliminare questo brano dalla scaletta?');">
                                                <input type="hidden" name="id_scaletta" value="<?php echo $id_scaletta; ?>">
                                                <input type="hidden" name="id_brano" value="<?php echo (int)$r["id_brano"]; ?>">
                                                <button type="submit" class="btn btn-sm btn-outline-danger">Elimina</button>
                                            </form>
                                        </div>
                                    </li>
                                <?php } ?>
                            </ul>

                            <form id="form-riordino" action="../server/api/riordina_scaletta.php" method="POST" class="mt-3">
                                <input type="hidden" name="id_scaletta" value="<?php echo $id_scaletta; ?>">
                                <input type="hidden" name="ordine_brani" id="ordine_brani">
                                <div class="d-grid">
                                    <button type="submit" class="btn btn-dark">Salva nuovo ordine</button>
                                </div>
                            </form>
                        <?php } else { ?>
                            <div class="alert alert-warning mb-0">Scaletta vuota.</div>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
    <?php } ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.2/Sortable.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.querySelectorAll(".check-brano").forEach(function(chk) {
    chk.addEventListener("change", function() {
        const row = this.closest("tr");
        const inputs = row.querySelectorAll(".campo");
        inputs.forEach(function(inp) {
            inp.disabled = !chk.checked;
            if (!chk.checked) inp.value = "";
        });
    });
});

const lista = document.getElementById("lista-tracklist");
const ordineInput = document.getElementById("ordine_brani");

if (lista && ordineInput) {
    const aggiornaOrdine = function () {
        const ids = [];
        lista.querySelectorAll("li").forEach(function(item, index) {
            ids.push(item.dataset.idBrano);
            const numero = index + 1;
            const posText = item.querySelector(".numero-posizione");
            if (posText) posText.textContent = numero;
            const badge = item.querySelector(".badge.bg-dark");
            if (badge) badge.textContent = "#" + numero;
        });
        ordineInput.value = ids.join(",");
    };

    new Sortable(lista, {
        animation: 150,
        ghostClass: "ghost",
        onEnd: aggiornaOrdine
    });

    aggiornaOrdine();
}
</script>

</body>
</html>