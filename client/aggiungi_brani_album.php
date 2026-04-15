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
$stats_album = null;
$result_artisti = $connessione->query("SELECT id_artista, nome_arte FROM artisti ORDER BY nome_arte ASC");

if ($id_album_selezionato > 0) {
    $query_dati_album = "
        SELECT 
            al.*,
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

        $query_stats_album = "
            SELECT 
                COUNT(ab.id_brano) AS numero_brani,
                SEC_TO_TIME(SUM(TIME_TO_SEC(b.durata))) AS durata_totale
            FROM album al
            LEFT JOIN album_brani ab ON al.id_album = ab.id_album
            LEFT JOIN brani b ON ab.id_brano = b.id_brano
            WHERE al.id_album = $id_album_selezionato
        ";
        $result_stats_album = $connessione->query($query_stats_album);
        if ($result_stats_album) {
            $stats_album = $result_stats_album->fetch_assoc();
        }

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
        ab.note,
        b.titolo,
        b.durata,
        GROUP_CONCAT(DISTINCT af.nome_arte ORDER BY af.nome_arte SEPARATOR ', ') AS feat
    FROM album_brani ab
    JOIN brani b ON ab.id_brano = b.id_brano
    LEFT JOIN feat_brani fb ON b.id_brano = fb.id_brano
    LEFT JOIN artisti af ON fb.id_artista = af.id_artista
    WHERE ab.id_album = $id_album_selezionato
    GROUP BY 
        ab.id_brano,
        ab.numero_traccia,
        ab.note,
        b.titolo,
        b.durata
    ORDER BY ab.numero_traccia ASC
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
    <title>Gestione progetto - Backstage Manager</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .riga-trascinabile {
            cursor: grab;
        }

        .ghost {
            opacity: 0.4;
            background: #f8f9fa;
        }

        .track-item {
    border: 1px solid #dee2e6;
    border-radius: 12px;
    padding: 14px 16px;
    background: #fff;
    transition: 0.2s ease;
}

.track-item:hover {
    box-shadow: 0 0.25rem 0.75rem rgba(0, 0, 0, 0.08);
}

.track-numero {
    width: 48px;
    height: 48px;
    border-radius: 12px;
    background: #212529;
    color: #fff;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
    font-size: 1rem;
    flex-shrink: 0;
}

.track-titolo {
    font-weight: 600;
    font-size: 1rem;
    margin-bottom: 4px;
}

.track-meta {
    font-size: 0.88rem;
    color: #6c757d;
}

.track-note {
    font-size: 0.85rem;
    color: #495057;
    margin-top: 4px;
}
    </style>
</head>
<body class="bg-light">

<?php include("components/navbar.php"); ?>

<div class="container mt-5 mb-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">Gestione progetto</h2>
        <a href="discografia.php" class="btn btn-outline-dark">Torna alla discografia</a>
    </div>

    <?php if (isset($_GET["album_salvato"])) { ?>
        <div class="alert alert-success">Dettagli progetto salvati correttamente.</div>
    <?php } ?>

    <?php if (isset($_GET["brani_aggiunti"])) { ?>
        <div class="alert alert-success">Brani aggiunti correttamente al progetto.</div>
    <?php } ?>

    <?php if (isset($_GET["brano_creato"])) { ?>
        <div class="alert alert-success">Nuovo brano creato e aggiunto al progetto.</div>
    <?php } ?>

    <?php if (isset($_GET["brano_rimosso"])) { ?>
        <div class="alert alert-success">Brano rimosso correttamente dal progetto.</div>
    <?php } ?>

    <?php if (isset($_GET["ordine_salvato"])) { ?>
        <div class="alert alert-success">Ordine tracklist salvato correttamente.</div>
    <?php } ?>

    <?php if (isset($_GET["errore"])) { ?>
        <div class="alert alert-danger">
            <?php
            switch ($_GET["errore"]) {
                case "nessun_brano":
                    echo "Devi selezionare almeno un brano.";
                    break;
                case "campi_mancanti":
                    echo "Per ogni brano selezionato devi compilare il numero traccia.";
                    break;
                case "duplicato_brano":
                    echo "Uno o più brani sono già presenti nel progetto.";
                    break;
                case "posizione_occupata":
                    echo "Uno o più numeri traccia sono già occupati.";
                    break;
                case "conflitto_inserimento":
                    echo "Hai assegnato lo stesso numero traccia a più brani nella stessa operazione.";
                    break;
                case "campi_nuovo_brano":
                    echo "Compila tutti i campi obbligatori del nuovo brano.";
                    break;
                case "album_non_trovato":
                    echo "Album non trovato.";
                    break;
                case "traccia_occupata":
                    echo "Il numero traccia scelto è già occupato.";
                    break;
                case "errore_creazione_brano":
                    echo "Errore durante la creazione del brano.";
                    break;
                case "errore_collegamento_album":
                    echo "Brano creato, ma errore nel collegamento al progetto.";
                    break;
                case "errore_inserimento":
                    echo "Errore durante l'aggiunta dei brani al progetto.";
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
                <div class="d-flex flex-column flex-lg-row justify-content-between gap-3">
                    <div>
                        <h3 class="mb-1"><?php echo htmlspecialchars($dati_album["titolo_album"]); ?></h3>
                        <p class="text-muted mb-2">
                            <?php echo htmlspecialchars($dati_album["nome_arte"]); ?>
                            <?php if (!empty($dati_album["data_uscita"])) { ?>
                                · <?php echo date("d/m/Y", strtotime($dati_album["data_uscita"])); ?>
                            <?php } ?>
                        </p>

                        <div class="d-flex flex-wrap gap-2">
                            <span class="badge bg-dark"><?php echo htmlspecialchars($dati_album["tipo_album"]); ?></span>
                            <span class="badge bg-secondary"><?php echo htmlspecialchars($dati_album["stato_album"]); ?></span>
                            <span class="badge bg-light text-dark border">
                                <?php echo !empty($dati_album["genere"]) ? htmlspecialchars($dati_album["genere"]) : "Genere n/d"; ?>
                            </span>
                            <span class="badge bg-light text-dark border">
                                <?php echo !empty($stats_album["numero_brani"]) ? $stats_album["numero_brani"] : 0; ?> brani
                            </span>
                            <span class="badge bg-light text-dark border">
                                <?php echo !empty($stats_album["durata_totale"]) ? $stats_album["durata_totale"] : "Durata n/d"; ?>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card shadow-sm mb-4">
            <div class="card-header bg-dark text-white">
                <h5 class="mb-0">Dettagli progetto</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="../server/api/modifica_album.php">
                    <input type="hidden" name="id_album" value="<?php echo $id_album_selezionato; ?>">

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Titolo progetto</label>
                            <input type="text" name="titolo_album" class="form-control"
                                   value="<?php echo htmlspecialchars($dati_album["titolo_album"]); ?>" required>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Data uscita</label>
                            <input type="date" name="data_uscita" class="form-control"
                                   value="<?php echo !empty($dati_album["data_uscita"]) ? htmlspecialchars($dati_album["data_uscita"]) : ""; ?>">
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Tipo progetto</label>
                            <select name="tipo_album" class="form-select">
                                <?php
                                $tipi = ["album", "ep", "mixtape", "singolo", "deluxe"];
                                foreach ($tipi as $tipo) {
                                    $selected = ($dati_album["tipo_album"] === $tipo) ? "selected" : "";
                                    echo "<option value='$tipo' $selected>" . ucfirst($tipo) . "</option>";
                                }
                                ?>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Genere</label>
                            <input type="text" name="genere" class="form-control"
                                   value="<?php echo htmlspecialchars($dati_album["genere"] ?? ""); ?>">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Stato progetto</label>
                            <select name="stato_album" class="form-select">
                                <?php
                                $stati_album = ["in lavorazione", "programmato", "pubblicato", "sospeso"];
                                foreach ($stati_album as $stato) {
                                    $selected = ($dati_album["stato_album"] === $stato) ? "selected" : "";
                                    echo "<option value='$stato' $selected>" . ucfirst($stato) . "</option>";
                                }
                                ?>
                            </select>
                        </div>

                        <div class="col-12">
                            <label class="form-label">Note</label>
                            <textarea name="note" class="form-control" rows="3"><?php echo htmlspecialchars($dati_album["note"] ?? ""); ?></textarea>
                        </div>

                        <div class="col-12 d-flex justify-content-end">
                            <button type="submit" class="btn btn-dark">Salva dettagli progetto</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-lg-6">
                <div class="card shadow-sm h-100">
                    <div class="card-header bg-dark text-white">
                        <h5 class="mb-0">Nuovo brano e aggiunta immediata</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="../server/api/crea_brano_album.php">
                            <input type="hidden" name="id_album" value="<?php echo $id_album_selezionato; ?>">

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Titolo</label>
                                    <input type="text" name="titolo" class="form-control" required>
                                </div>

                                <div class="col-md-3">
                                    <label class="form-label">Durata</label>
                                    <input type="time" name="durata" class="form-control" step="1" required>
                                </div>

                                <div class="col-md-3">
                                    <label class="form-label">Traccia</label>
                                    <input type="number" name="numero_traccia" class="form-control" min="1" required>
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label">BPM</label>
                                    <input type="number" name="bpm" class="form-control">
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label">Tonalità</label>
                                    <input type="text" name="tonalita" class="form-control">
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label">Anno pubblicazione</label>
                                    <input type="number" name="anno_pubblicazione" class="form-control">
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Genere brano</label>
                                    <input type="text" name="genere_brano" class="form-control">
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Stato brano</label>
                                    <select name="stato_brano" class="form-select" required>
                                        <option value="pubblicato">Pubblicato</option>
                                        <option value="inedito">Inedito</option>
                                        <option value="demo">Demo</option>
                                        <option value="cover">Cover</option>
                                    </select>
                                </div>

                                <div class="col-12">
                                    <label class="form-label">Feat (facoltativo)</label>
                                    <select name="feat[]" class="form-select" multiple size="5">
                                        <?php
                                        if ($result_artisti && $result_artisti->num_rows > 0) {
                                            $result_artisti->data_seek(0);
                                            while ($artista = $result_artisti->fetch_assoc()) {
                                                if ($artista["id_artista"] != $dati_album["id_artista"]) {
                                                    echo "<option value='{$artista["id_artista"]}'>{$artista["nome_arte"]}</option>";
                                                }
                                            }
                                        }
                                        ?>
                                    </select>
                                    <small class="text-muted">Tieni premuto Ctrl per selezionare più artisti.</small>
                                </div>

                                <div class="col-12">
                                    <label class="form-label">Testo</label>
                                    <textarea name="testo" class="form-control" rows="4"></textarea>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Note brano</label>
                                    <textarea name="note" class="form-control" rows="3"></textarea>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Note collegamento progetto</label>
                                    <textarea name="note_album_brano" class="form-control" rows="3"></textarea>
                                </div>

                                <div class="col-12 d-flex justify-content-end">
                                    <button type="submit" class="btn btn-dark">Crea e aggiungi al progetto</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-lg-6">
    <div class="card shadow-sm h-100">
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
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <small class="text-muted">
                        <?php echo count($tracklist_array); ?> brani nella tracklist
                    </small>
                </div>

                <div id="lista-tracklist" class="d-flex flex-column gap-3">
                    <?php foreach ($tracklist_array as $presente) { ?>
                        <div class="track-item riga-trascinabile d-flex justify-content-between align-items-start gap-3"
                             data-id-brano="<?php echo (int)$presente['id_brano']; ?>">

                            <div class="d-flex align-items-start gap-3 flex-grow-1">
                                <div class="track-numero">
                                    <span class="numero-traccia">
                                        <?php echo str_pad((int)$presente['numero_traccia'], 2, "0", STR_PAD_LEFT); ?>
                                    </span>
                                </div>

                                <div class="flex-grow-1">
                                    <div class="track-titolo">
                                        <?php echo htmlspecialchars($presente['titolo']); ?>
                                    </div>

                                    <div class="track-meta">
                                        <?php
                                        $meta = [];

                                        if (!empty($presente["durata"])) {
                                            $meta[] = htmlspecialchars($presente["durata"]);
                                        }

                                        if (!empty($presente["feat"])) {
                                            $meta[] = "Feat: " . htmlspecialchars($presente["feat"]);
                                        }

                                        echo !empty($meta) ? implode(" · ", $meta) : "Nessuna informazione aggiuntiva";
                                        ?>
                                    </div>

                                    <?php if (!empty($presente["note"])) { ?>
                                        <div class="track-note">
                                            <strong>Note:</strong> <?php echo htmlspecialchars($presente["note"]); ?>
                                        </div>
                                    <?php } ?>
                                </div>
                            </div>

                            <div class="d-flex flex-column gap-2 align-items-end">
                                <span class="badge bg-dark">
                                    #<span class="badge-traccia"><?php echo (int)$presente['numero_traccia']; ?></span>
                                </span>

                                <form action="../server/api/elimina_brano_album.php" method="POST"
                                      onsubmit="return confirm('Rimuovere questo brano dal progetto?');">
                                    <input type="hidden" name="id_album" value="<?php echo $id_album_selezionato; ?>">
                                    <input type="hidden" name="id_brano" value="<?php echo (int)$presente['id_brano']; ?>">
                                    <button type="submit" class="btn btn-sm btn-outline-danger">Rimuovi</button>
                                </form>
                            </div>
                        </div>
                    <?php } ?>
                </div>

                <form id="form-riordino" action="../server/api/riordina_album.php" method="POST" class="mt-4">
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

        <div class="card shadow-sm mt-4">
            <div class="card-header bg-light d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Aggiungi brani già esistenti</h5>
                <button class="btn btn-sm btn-outline-dark" type="button" data-bs-toggle="collapse" data-bs-target="#collapseBraniEsistenti">
                    Mostra / Nascondi
                </button>
            </div>

            <div class="collapse" id="collapseBraniEsistenti">
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
                                                        value="<?php echo $brano["id_brano"]; ?>">
                                                </td>
                                                <td><?php echo htmlspecialchars($brano["titolo"]); ?></td>
                                                <td><?php echo !empty($brano["feat"]) ? htmlspecialchars($brano["feat"]) : "-"; ?></td>
                                                <td><?php echo htmlspecialchars($brano["durata"]); ?></td>
                                                <td><?php echo htmlspecialchars($brano["stato_brano"]); ?></td>
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
    <?php } ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.2/Sortable.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function () {
    const checkboxBrani = document.querySelectorAll(".seleziona-brano");

    checkboxBrani.forEach(function (checkbox) {
        checkbox.addEventListener("change", function () {
            const riga = checkbox.closest("tr");
            const campi = riga.querySelectorAll(".campo-brano");

            campi.forEach(function (campo) {
                campo.disabled = !checkbox.checked;

                if (!checkbox.checked) {
                    campo.value = "";
                }
            });
        });
    });

    const lista = document.getElementById("lista-tracklist");
    const ordineInput = document.getElementById("ordine_brani");

    if (lista && ordineInput) {
        const aggiornaOrdine = function () {
            const voci = [];

            lista.querySelectorAll("[data-id-brano]").forEach(function (item, index) {
    const traccia = index + 1;
    const span = item.querySelector(".numero-traccia");
    const badge = item.querySelector(".badge-traccia");

    if (span) span.textContent = String(traccia).padStart(2, "0");
    if (badge) badge.textContent = traccia;

    voci.push(item.dataset.idBrano + ":" + traccia);
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