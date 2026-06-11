<?php
extract($data ?? []);
?>
<?php require_once APPROOT . '/views/includes/header.php'; ?>

<div class="container py-5">

    <div class="meldingen-pagina-header mb-4">
        <h1>Mijn Meldingen</h1>
        <p>Een overzicht van al je meldingen</p>
    </div>

    <?php if ($heeft_meldingen): ?>

        <!-- HAPPY FLOW -->
        <div class="d-flex flex-column gap-3">
            <?php foreach ($meldingen as $melding): ?>
                <div class="melding-card">
                    <div class="d-flex">

                        <div class="melding-streep melding-streep--<?= htmlspecialchars(strtolower($melding->type)) ?>"></div>

                        <div class="p-3 flex-grow-1">
                            <div class="d-flex align-items-center gap-2 mb-2">
                                <span class="melding-badge melding-badge--<?= htmlspecialchars(strtolower($melding->type)) ?>">
                                    <?= htmlspecialchars(ucfirst($melding->type)) ?>
                                </span>
                                <span class="melding-datum ms-auto">#<?= htmlspecialchars($melding->nummer) ?></span>
                            </div>

                            <p class="melding-bericht">
                                <?= htmlspecialchars($melding->bericht) ?>
                            </p>

                            <div class="d-flex align-items-center justify-content-between">
                                <span class="melding-datum">
                                    <i class="bi bi-clock me-1"></i>
                                    <?php
                                    $datum = new DateTime($melding->datum_aangemaakt);
                                    echo $datum->format('d M Y \o\m H:i');
                                    ?>
                                </span>
                                <span
                                    class="melding-status <?= $melding->is_actief ? 'melding-status--actief' : 'melding-status--gesloten' ?>">
                                    <?= $melding->is_actief ? 'Actief' : 'Gesloten' ?>
                                </span>
                            </div>
                        </div>

                    </div>
                </div>
            <?php endforeach; ?>
        </div>

    <?php else: ?>

        <!-- UNHAPPY FLOW -->
        <div class="melding-empty">
            <i class="bi bi-bell-slash mb-3"></i>
            <h4 class="mt-2 mb-2">Geen meldingen</h4>
            <p class="mb-4">
                Er zijn momenteel geen meldingen voor jouw account.<br>
                Zodra er iets te melden valt, vind je dat hier terug.
            </p>
            <a href="<?= URLROOT ?>" class="btn btn-outline-custom">
                <i class="bi bi-house me-1"></i> Terug naar home
            </a>
        </div>

    <?php endif; ?>

</div>

<?php require_once APPROOT . '/views/includes/footer.php'; ?>