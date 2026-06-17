<?php
extract($data ?? []);
?>
<?php require_once APPROOT . '/views/includes/header.php'; ?>

<div class="container py-5">

    <div class="meldingen-pagina-header mb-4 d-flex align-items-center justify-content-between flex-wrap gap-3">
        <div>
            <h1>Mijn Meldingen</h1>
            <p>Een overzicht van al je meldingen</p>

            <p style="color: #8a9bb0;">
                Huidige flow:
                <strong style="color: <?= $melding_flow === 'happy' ? 'var(--accent-cyan)' : 'var(--accent-magenta)' ?>;">
                    <?= ucfirst($melding_flow) ?>
                </strong>
            </p>
        </div>

        <?php if (isset($_SESSION['accountId'])): ?>
            <div class="d-flex gap-2 flex-wrap">
                <a href="<?= URLROOT ?>/meldingen/happy" class="btn btn-primary-custom">
                    Happy
                </a>

                <a href="<?= URLROOT ?>/meldingen/unhappy"
                   class="btn btn-outline-custom"
                   style="border-color: var(--accent-magenta); color: var(--accent-magenta);">
                    Unhappy
                </a>

                <button class="btn btn-primary-custom" data-bs-toggle="modal" data-bs-target="#nieuweMeldingModal">
                    <i class="bi bi-plus-lg me-1"></i> Nieuwe Melding
                </button>
            </div>
        <?php endif; ?>
    </div>

    <?php if ($heeft_meldingen): ?>

        <div class="melding-card p-0 overflow-hidden">
            <div class="table-responsive">
                <table class="table mb-0" style="color: var(--text-primary);">
                    <thead>
                        <tr style="border-bottom: 2px solid var(--accent-cyan);">
                            <th style="padding: 18px;">Gebruiker ID</th>
                            <th style="padding: 18px;">Rol</th>
                            <th style="padding: 18px;">Melding Titel</th>
                            <th style="padding: 18px;">Melding Bericht</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php foreach ($meldingen as $melding): ?>
                            <tr style="border-bottom: 1px solid rgba(0, 229, 255, 0.25);">
                                <td style="padding: 18px;">
                                    <?= htmlspecialchars($melding->bezoeker_id ?? $melding->medewerker_id ?? 'NULL') ?>
                                </td>

                                <td style="padding: 18px;">
                                    <?= !empty($melding->bezoeker_id) ? 'Bezoeker' : 'Medewerker' ?>
                                </td>

                                <td style="padding: 18px;">
                                    <span class="melding-badge melding-badge--<?= htmlspecialchars(strtolower($melding->type)) ?>">
                                        <?= htmlspecialchars(ucfirst($melding->type)) ?>
                                    </span>
                                </td>

                                <td style="padding: 18px;">
                                    <div>
                                        <?= htmlspecialchars($melding->bericht) ?>
                                    </div>

                                    <?php if (!empty($melding->opmerking)): ?>
                                        <div style="color: #8a9bb0; margin-top: 6px;">
                                            <?= htmlspecialchars($melding->opmerking) ?>
                                        </div>
                                    <?php endif; ?>

                                    <div style="color: #8a9bb0; font-size: 13px; margin-top: 8px;">
                                        #<?= htmlspecialchars($melding->nummer) ?>
                                        |
                                        <?= htmlspecialchars($melding->is_actief ? 'Actief' : 'Gesloten') ?>
                                        |
                                        <?= htmlspecialchars($melding->datum_aangemaakt) ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

    <?php else: ?>

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
                        <label for="doelgroep" class="form-label">
                            Ontvanger
                        </label>

                        <select class="form-control melding-select" id="doelgroep" name="doelgroep">
                            <option value="" selected>Kies ontvanger...</option>
                            <option value="iedereen">Iedereen</option>
                            <option value="alle_bezoekers">Alle bezoekers</option>
                            <option value="alle_medewerkers">Alle medewerkers</option>
                            <option value="bezoeker">Alleen naar mij</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="ontvangerId" class="form-label">
                            Ontvanger ID
                        </label>

                        <input type="number"
                               class="form-control"
                               id="ontvangerId"
                               name="ontvanger_id"
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

                        <textarea class="form-control"
                                  id="meldingBericht"
                                  name="bericht"
                                  rows="3"
                                  maxlength="250"
                                  placeholder="Typ hier je melding..."
                                  required></textarea>

                        <small style="color: #8a9bb0;">Maximaal 250 tekens.</small>
                    </div>

                    <div class="mb-3">
                        <label for="meldingOpmerking" class="form-label">
                            Opmerking
                        </label>

                        <textarea class="form-control"
                                  id="meldingOpmerking"
                                  name="opmerking"
                                  rows="2"
                                  maxlength="250"
                                  placeholder="Optionele opmerking..."></textarea>

                        <small style="color: #8a9bb0;">Maximaal 250 tekens.</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Status</label>

                        <div class="d-flex gap-3">
                            <div class="form-check">
                                <input class="form-check-input"
                                       type="radio"
                                       name="is_actief"
                                       id="isActiefJa"
                                       value="1"
                                       checked>

                                <label class="form-check-label" for="isActiefJa">
                                    Actief
                                </label>
                            </div>

                            <div class="form-check">
                                <input class="form-check-input"
                                       type="radio"
                                       name="is_actief"
                                       id="isActiefNee"
                                       value="0">

                                <label class="form-check-label" for="isActiefNee">
                                    Gesloten
                                </label>
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

<?php if (isset($_SESSION['melding_db_fout']) && $_SESSION['melding_db_fout']): ?>
    <div class="modal fade" id="dbFoutModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="border-color: var(--accent-magenta);">

                <div class="modal-header" style="border-color: var(--accent-magenta);">
                    <h5 class="modal-title" style="color: var(--accent-magenta);">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i> Fout
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <p class="mb-0" style="color: var(--text-secondary);">
                        Momenteel niet beschikbaar.<br>
                        Geen connectie met database gevonden.
                    </p>
                </div>

                <div class="modal-footer" style="border-color: var(--accent-magenta);">
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
<?php endif; ?>

<?php require_once APPROOT . '/views/includes/footer.php'; ?>