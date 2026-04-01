<?php
include("../server/config/connessione.php");

$id_album_selezionato = isset($_GET["id_album"]) ? (int)$_GET["id_album"] : 0;

$query_album = "
    SELECT 
        al.id_album,
        al.titolo_album,
        al.tipo_album,
        al.stato_album,
        al.data_uscita,
        a.id_artista,
        a.nome_arte
    FROM album al
    JOIN artisti a ON al.id_artista = a.id_artista
    ORDER BY 
        a.nome_arte ASC,
        al.data_uscita DESC,
        al.titolo_album ASC
";
$result_album = $connessione->query($query_album);

$dati_album = null;
$brani_artista = null;
$brani_gia_presenti = null;

if ($id_album_selezionato > 0) {
    $query_dati_album = "
        SELECT 
            al.id_album,
            al.titolo_album,
            al.tipo_album,
            al.stato_album,
            al.data_uscita,
            a.id_artista,
            a.nome_arte
        FROM album al
        JOIN artisti a ON al.id_artista = a.id_artista
        WHERE al.id_album = $id_album_selezionato
    ";
    $result_dati_album = $connessione->query($query_dati_album);

    if ($result_dati_album && $result_dati_album->num_rows > 0) {
        $dati_album = $result_dati_album->fetch_assoc();

        $id_artista = (int)$dati_album["id_artista"];

        $query_brani_artista = "
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
            WHERE b.id_artista = $id_artista
            GROUP BY 
                b.id_brano,
                b.titolo,
                b.durata,
                b.stato_brano,
                b.genere_brano
            ORDER BY b.titolo ASC
        ";
        $brani_artista = $connessione->query($query_brani_artista);

        $query_brani_gia_presenti = "
            SELECT 
                ab.id_brano,
                ab.numero_traccia,
                ab.disco,
                b.titolo
            FROM album_brani ab
            JOIN brani b ON ab.id_brano = b.id_brano
            WHERE ab.id_album = $id_album_selezionato
            ORDER BY ab.disco ASC, ab.numero_traccia ASC
        ";
        $brani_gia_presenti = $connessione->query($query_brani_gia_presenti);
    }
}
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aggiungi brani al progetto - Backstage Manager</title>
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
        <h2 class="mb-0">Aggiungi brani al progetto</h2>
        <a href="discografia.php" class="btn btn-outline-dark">Vai alla discografia</a>
    </div>

    <?php if (isset($_GET["success"])) { ?>
        <div class="alert alert-success">
            Brani aggiunti correttamente al progetto.
        </div>
    <?php } ?>

    <?php if (isset($_GET["errore"])) { ?>
        <div class="alert alert-danger">
            <?php
            switch ($_GET["errore"]) {
                case "nessun_brano":
                    echo "Devi selezionare almeno un brano.";
                    break;
                case "campi_mancanti":
                    echo "Per ogni brano selezionato devi compilare almeno il numero traccia.";
                    break;
                case "duplicato_brano":
                    echo "Uno o più brani sono già presenti nel progetto.";
                    break;
                case "posizione_occupata":
                    echo "Uno o più numeri traccia sono già occupati nello stesso disco.";
                    break;
                case "conflitto_inserimento":
                    echo "Hai assegnato lo stesso numero traccia a più brani nello stesso disco nella stessa operazione.";
                    break;
                default:
                    echo "Si è verificato un errore durante il salvataggio.";
                    break;
            }
            ?>
        </div>
    <?php } ?>

    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="aggiungi_brani_album.php" class="row g-3 align-items-end">
                <div class="col-md-10">
                    <label for="id_album" class="form-label">Seleziona progetto</label>
                    <select name="id_album" id="id_album" class="form-select" required>
                        <option value="">Seleziona progetto</option>
                        <?php if ($result_album && $result_album->num_rows > 0) {
                            while ($album = $result_album->fetch_assoc()) {
                                $selected = ($id_album_selezionato == $album["id_album"]) ? "selected" : "";
                                $data = !empty($album["data_uscita"]) ? date("d/m/Y", strtotime($album["data_uscita"])) : "data n/d";
                                echo "<option value='{$album["id_album"]}' $selected>
                                        {$album["nome_arte"]} - {$album["titolo_album"]} ({$album["tipo_album"]}, {$data})
                                      </option>";
                            }
                        } ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-dark w-100">Carica</button>
                </div>
            </form>
        </div>
    </div>

    <?php if ($dati_album) { ?>
        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <h4 class="mb-1"><?php echo htmlspecialchars($dati_album["titolo_album"]); ?></h4>
                <p class="text-muted mb-0">
                    <?php echo htmlspecialchars($dati_album["nome_arte"]); ?> ·
                    <?php echo htmlspecialchars($dati_album["tipo_album"]); ?> ·
                    <?php echo htmlspecialchars($dati_album["stato_album"]); ?>
                    <?php if (!empty($dati_album["data_uscita"])) { ?>
                        · <?php echo date("d/m/Y", strtotime($dati_album["data_uscita"])); ?>
                    <?php } ?>
                </p>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-lg-8">
                <div class="card shadow-sm">
                    <div class="card-header bg-dark text-white">
                        <h5 class="mb-0">Brani disponibili dell'artista</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="../server/api/album_brani.php">
                            <input type="hidden" name="id_album" value="<?php echo $id_album_selezionato; ?>">

                            <?php if ($brani_artista && $brani_artista->num_rows > 0) { ?>
                                <div class="table-responsive">
                                    <table class="table table-striped table-bordered align-middle">
                                        <thead class="table-dark">
                                            <tr>
                                                <th>Seleziona</th>
                                                <th>Titolo</th>
                                                <th>Feat</th>
                                                <th>Durata</th>
                                                <th>Stato</th>
                                                <th>Disco</th>
                                                <th>Traccia</th>
                                                <th>Note</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php while ($brano = $brani_artista->fetch_assoc()) { ?>
                                                <tr>
                                                    <td class="text-center">
                                                        <input 
                                                            class="form-check-input seleziona-brano" 
                                                            type="checkbox" 
                                                            name="brani_selezionati[]" 
                                                            value="<?php echo $brano["id_brano"]; ?>"
                                                            data-id="<?php echo $brano["id_brano"]; ?>">
                                                    </td>
                                                    <td><?php echo htmlspecialchars($brano["titolo"]); ?></td>
                                                    <td><?php echo !empty($brano["feat"]) ? htmlspecialchars($brano["feat"]) : "-"; ?></td>
                                                    <td><?php echo htmlspecialchars($brano["durata"]); ?></td>
                                                    <td><?php echo htmlspecialchars($brano["stato_brano"]); ?></td>
                                                    <td style="min-width: 100px;">
                                                        <input 
                                                            type="number" 
                                                            name="disco[<?php echo $brano["id_brano"]; ?>]" 
                                                            class="form-control campo-brano" 
                                                            min="1" 
                                                            value="1"
                                                            disabled>
                                                    </td>
                                                    <td style="min-width: 110px;">
                                                        <input 
                                                            type="number" 
                                                            name="numero_traccia[<?php echo $brano["id_brano"]; ?>]" 
                                                            class="form-control campo-brano" 
                                                            min="1"
                                                            disabled>
                                                    </td>
                                                    <td style="min-width: 180px;">
                                                        <input 
                                                            type="text" 
                                                            name="note[<?php echo $brano["id_brano"]; ?>]" 
                                                            class="form-control campo-brano" 
                                                            maxlength="255"
                                                            disabled>
                                                    </td>
                                                </tr>
                                            <?php } ?>
                                        </tbody>
                                    </table>
                                </div>

                                <div class="d-flex justify-content-end">
                                    <button type="submit" class="btn btn-dark">Aggiungi brani al progetto</button>
                                </div>
                            <?php } else { ?>
                                <div class="alert alert-warning mb-0">
                                    Nessun brano disponibile per questo artista.
                                </div>
                            <?php } ?>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card shadow-sm">
                    <div class="card-header bg-secondary text-white d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Tracklist attuale</h5>
                        <small>Trascina per riordinare</small>
                    </div>
                    <div class="card-body">
                        <?php if ($brani_gia_presenti && $brani_gia_presenti->num_rows > 0) {
                            $tracklist_array = [];
                            while ($presente = $brani_gia_presenti->fetch_assoc()) {
                                $tracklist_array[] = $presente;
                            }
                        ?>
                            <ul class="list-group" id="lista-tracklist">
                                <?php foreach ($tracklist_array as $presente) { ?>
                                    <li class="list-group-item riga-trascinabile d-flex justify-content-between align-items-start"
                                        data-id-brano="<?php echo (int)$presente['id_brano']; ?>"
                                        data-disco="<?php echo (int)$presente['disco']; ?>">
                                        <div class="me-2">
                                            <strong><?php echo htmlspecialchars($presente['titolo']); ?></strong><br>
                                            <small class="text-muted">
                                                Disco <?php echo (int)$presente['disco']; ?> · Traccia <span class="numero-traccia"><?php echo (int)$presente['numero_traccia']; ?></span>
                                            </small>
                                        </div>
                                        <div class="d-flex flex-column gap-1 align-items-end">
                                            <span class="badge bg-dark">#<span class="badge-traccia"><?php echo (int)$presente['numero_traccia']; ?></span></span>
                                            <form action="../server/api/elimina_brano_album.php" method="POST"
                                                  onsubmit="return confirm('Rimuovere questo brano dal progetto?');">
                                                <input type="hidden" name="id_album" value="<?php echo $id_album_selezionato; ?>">
                                                <input type="hidden" name="id_brano" value="<?php echo (int)$presente['id_brano']; ?>">
                                                <button type="submit" class="btn btn-sm btn-outline-danger">Rimuovi</button>
                                            </form>
                                        </div>
                                    </li>
                                <?php } ?>
                            </ul>

                            <form id="form-riordino" action="../server/api/riordina_album.php" method="POST" class="mt-3">
                                <input type="hidden" name="id_album" value="<?php echo $id_album_selezionato; ?>">
                                <input type="hidden" name="ordine_brani" id="ordine_brani">
                                <div class="d-grid">
                                    <button type="submit" class="btn btn-dark">Salva nuovo ordine</button>
                                </div>
                            </form>
                        <?php } else { ?>
                            <div class="alert alert-info mb-0">
                                Nessun brano ancora collegato a questo progetto.
                            </div>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
    <?php } ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.2/Sortable.min.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function () {

    // Checkbox → abilita/disabilita campi di riga
    const checkboxBrani = document.querySelectorAll(".seleziona-brano");
    checkboxBrani.forEach(function (checkbox) {
        checkbox.addEventListener("change", function () {
            const riga = checkbox.closest("tr");
            const campi = riga.querySelectorAll(".campo-brano");
            campi.forEach(function (campo) {
                campo.disabled = !checkbox.checked;
                if (!checkbox.checked && campo.type !== "number") {
                    campo.value = "";
                }
                if (!checkbox.checked && campo.type === "number") {
                    campo.value = campo.name.includes("disco") ? 1 : "";
                }
            });
        });
    });

    // Drag & drop tracklist
    const lista = document.getElementById("lista-tracklist");
    const ordineInput = document.getElementById("ordine_brani");

    if (lista && ordineInput) {
        const aggiornaOrdine = function () {
            const voci = [];
            // Raggruppa per disco e ricalcola numero traccia
            const perDisco = {};
            lista.querySelectorAll("li").forEach(function (item) {
                const disco = item.dataset.disco;
                if (!perDisco[disco]) perDisco[disco] = [];
                perDisco[disco].push(item);
            });

            Object.keys(perDisco).sort().forEach(function (disco) {
                perDisco[disco].forEach(function (item, index) {
                    const traccia = index + 1;
                    const span = item.querySelector(".numero-traccia");
                    const badge = item.querySelector(".badge-traccia");
                    if (span) span.textContent = traccia;
                    if (badge) badge.textContent = traccia;
                    voci.push(item.dataset.idBrano + ":" + disco + ":" + traccia);
                });
            });

            ordineInput.value = voci.join(",");
        };

        new Sortable(lista, {
            animation: 150,
            ghostClass: "ghost",
            onEnd: aggiornaOrdine
        });

        aggiornaOrdine();
    }
});
</script>

</body>
</html>