<?php
extract($data ?? []);
?>
<?php require_once APPROOT . '/views/includes/header.php'; ?>

<div class="container py-5 meldingen-overzicht-page">

    <div class="meldingen-pagina-header mb-4 d-flex align-items-center justify-content-between flex-wrap gap-3">
        <div>
            <h1>Mijn Meldingen</h1>
            <p>Een overzicht van al je meldingen</p>

            <?php if (!empty($is_admin)): ?>
                <p class="melding-flow">
                    Huidige flow:
                    <strong class="<?= $melding_flow === 'happy' ? 'flow-happy' : 'flow-unhappy' ?>">
                        <?= ucfirst($melding_flow) ?>
                    </strong>
                </p>
            <?php endif; ?>
        </div>

        <?php if (!empty($is_admin)): ?>
            <div class="d-flex gap-2 flex-wrap">

                <a href="<?= URLROOT ?>/meldingen/happy" class="btn btn-primary-custom">
                    Happy
                </a>

                <a href="<?= URLROOT ?>/meldingen/unhappy" class="btn btn-outline-custom melding-unhappy-btn">
                    Unhappy
                </a>

                <button class="btn btn-primary-custom" data-bs-toggle="modal" data-bs-target="#nieuweMeldingModal">
                    <i class="bi bi-plus-lg me-1"></i> Nieuwe Melding
                </button> <!---Nieuwe melding KNOP-->

            </div>
        <?php endif; ?>
    </div>

    <?php if ($heeft_meldingen): ?>

        <div class="melding-table-wrapper">
            <table class="melding-table">
                <thead>
                    <tr>
                        <th>Nummer</th>
                        <th>Rol</th>
                        <th>Type</th>
                        <th>Bericht</th>
                        <th>Status</th>
                        <th>Opmerking</th>
                        <th>Aangemaakt</th>
                        <th>Gewijzigd</th>
                        <?php if ($data['is_admin']): ?>
                            <th>Actie</th>
                        <?php endif; ?>
                    </tr>
                </thead>

                <tbody>
                    <?php foreach ($meldingen as $melding): ?>
                        <tr>
                            <td>#<?= htmlspecialchars($melding->nummer) ?></td>

                            <td>
                                <?= !empty($melding->medewerker_id) ? 'Medewerker' : 'Bezoeker' ?>
                            </td>

                            <td><?= htmlspecialchars(ucfirst($melding->type)) ?></td>

                            <td><?= htmlspecialchars($melding->bericht) ?></td>

                            <td>
                                <?= $melding->is_actief ? 'Actief' : 'Gesloten' ?>
                            </td>

                            <td>
                                <?= !empty($melding->opmerking) ? htmlspecialchars($melding->opmerking) : '-' ?>
                            </td>

                            <td>
                                <?= date('d-m-Y H:i', strtotime($melding->datum_aangemaakt)) ?>
                            </td>

                            <td>
                                <?= date('d-m-Y H:i', strtotime($melding->datum_gewijzigd)) ?>
                            </td>
                            <?php if ($data['is_admin']): ?>
                                <td>
                                    <a href="<?= URLROOT; ?>/meldingen/hersturen/<?= $melding->id; ?>"
                                        class="btn btn-primary-custom btn-sm">
                                        Hersturen
                                    </a>
                                </td>
                            <?php endif; ?>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

    <?php else: ?>

        <div class="melding-empty"> <!--Als er geen melding is---->
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

<?php if (!empty($is_admin)): ?>
    <div class="modal fade" id="nieuweMeldingModal" tabindex="-1" aria-labelledby="nieuweMeldingLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title" id="nieuweMeldingLabel">
                        <i class="bi bi-bell-fill me-2"></i> Nieuwe Melding Opstellen
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>

                <form method="POST" action="<?= URLROOT ?>/meldingen/opslaan">
                    <div class="modal-body">

                        <div class="mb-3">
                            <label for="doelgroep" class="form-label">Ontvanger</label>

                            <select class="form-control melding-select" id="doelgroep" name="doelgroep"> <!--Keuzes bij melding maken-->
                                <option value="" selected>Kies ontvanger...</option>
                                <option value="iedereen">Iedereen</option>
                                <option value="alle_bezoekers">Alle bezoekers</option>
                                <option value="alle_medewerkers">Alle medewerkers</option>
                                <option value="bezoeker">Alleen naar mij</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="ontvangerId" class="form-label">Ontvanger ID</label>

                            <input type="number" class="form-control" id="ontvangerId" name="ontvanger_id"
                                placeholder="Vul ID in als je naar 1 bezoeker wilt sturen">
                        </div>

                        <div class="mb-3">
                            <label for="meldingType" class="form-label">
                                Type <span class="text-danger">*</span>
                            </label>

                            <select class="form-control melding-select" id="meldingType" name="type" required>
                                <option value="" disabled selected>Kies een type...</option>
                                <option value="notificatie">Notificatie</option>
                                <option value="klacht">Klacht</option>
                                <option value="review">Review</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="meldingBericht" class="form-label">
                                Bericht <span class="text-danger">*</span>
                            </label>

                            <textarea class="form-control" id="meldingBericht" name="bericht" rows="3" maxlength="250"
                                placeholder="Typ hier je melding..." required></textarea>

                            <small class="melding-form-help">Maximaal 250 tekens.</small>
                        </div>

                        <div class="mb-3">
                            <label for="meldingOpmerking" class="form-label">Opmerking</label>

                            <textarea class="form-control" id="meldingOpmerking" name="opmerking" rows="2" maxlength="250"
                                placeholder="Optionele opmerking..."></textarea>

                            <small class="melding-form-help">Maximaal 250 tekens.</small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Status</label>

                            <div class="d-flex gap-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="is_actief" id="isActiefJa" value="1"
                                        checked>

                                    <label class="form-check-label" for="isActiefJa">Actief</label>
                                </div>

                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="is_actief" id="isActiefNee"
                                        value="0">

                                    <label class="form-check-label" for="isActiefNee">Gesloten</label>
                                </div>
                            </div>
                        </div>

                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            Annuleren
                        </button>

                        <button type="submit" class="btn btn-primary-custom">
                            <i class="bi bi-send me-1"></i> Versturen
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>
<?php endif; ?>

<?php if (isset($_SESSION['melding_db_fout']) && $_SESSION['melding_db_fout']): ?>
    <div class="modal fade" id="dbFoutModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content melding-error-modal">

                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i> Fout
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>

                </div>
                <p class="mb-0">
                    <?= htmlspecialchars($_SESSION['melding_db_fout']) ?>
                </p>

                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-custom" data-bs-dismiss="modal">
                        Sluiten
                    </button>
                </div>

            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            new bootstrap.Modal(document.getElementById('dbFoutModal')).show();
        });
    </script>

    <?php unset($_SESSION['melding_db_fout']); ?>
    <?php if (isset($_SESSION['melding_succes'])): ?>
        <script>
            alert("<?= $_SESSION['melding_succes']; ?>");
        </script>
        <?php unset($_SESSION['melding_succes']); ?>
    <?php endif; ?>
<?php endif; ?>

<?php if (isset($_SESSION['melding_succes'])): ?>
    <div class="modal fade" id="meldingSuccesModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content melding-error-modal">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="bi bi-check-circle-fill me-2"></i> Gelukt
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <p class="mb-0"><?= htmlspecialchars($_SESSION['melding_succes']) ?></p>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-custom" data-bs-dismiss="modal">
                        Sluiten
                    </button>
                </div>
            </div>
        </div>
    </div>

    <?php unset($_SESSION['melding_succes']); ?>
<?php endif; ?>


<?php if (isset($_SESSION['melding_bericht_popup'])): ?>
    <div class="modal fade" id="meldingBerichtModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content melding-error-modal">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="bi bi-bell-fill me-2"></i> Nieuwe melding
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <p class="mb-0"><?= htmlspecialchars($_SESSION['melding_bericht_popup']) ?></p>
                </div>

                <div class="modal-footer">
                    <a href="<?= URLROOT ?>/meldingen/gelezen/<?= $_SESSION['melding_popup_id'] ?>"
                        class="btn btn-outline-custom">
                        Sluiten
                    </a>
                </div>
            </div>
        </div>
    </div>

    <?php unset($_SESSION['melding_bericht_popup']); ?>
    <?php unset($_SESSION['melding_popup_id']); ?>
<?php endif; ?>


<script>
    document.addEventListener('DOMContentLoaded', function () {
        const succesModalElement = document.getElementById('meldingSuccesModal');
        const berichtModalElement = document.getElementById('meldingBerichtModal');

        if (succesModalElement) {
            const succesModal = new bootstrap.Modal(succesModalElement);
            succesModal.show();

            succesModalElement.addEventListener('hidden.bs.modal', function () {
                if (berichtModalElement) {
                    const berichtModal = new bootstrap.Modal(berichtModalElement);
                    berichtModal.show();
                }
            });
        } else if (berichtModalElement) {
            const berichtModal = new bootstrap.Modal(berichtModalElement);
            berichtModal.show();
        }
    });
</script>

<?php require_once APPROOT . '/views/includes/footer.php'; ?>