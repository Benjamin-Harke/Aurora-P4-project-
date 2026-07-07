<?php require_once APPROOT . '/views/includes/header.php'; ?>
<?php require_once APPROOT . '/views/includes/messages.php'; ?>

<div class="container mt-5 mb-5">
    <div class="mb-4">
        <a href="<?= URLROOT ?>/medewerkers/detail/<?= $data['medewerker']->id ?>" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left me-2"></i>Terug naar medewerker
        </a>
    </div>

    <div class="card border-0 shadow-lg">
        <div class="card-header bg-gradient text-white" style="background: linear-gradient(135deg, var(--accent-bordeaux), var(--secondary-purple));">
            <h3 class="mb-0">Bewerk Medewerker</h3>
        </div>
        <div class="card-body p-4">
            <form method="post" action="<?= URLROOT ?>/medewerkers/update">
                <input type="hidden" name="id" value="<?= htmlspecialchars($data['medewerker']->id) ?>">

                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">Voornaam</label>
                        <input type="text" name="voornaam" class="form-control" value="<?= htmlspecialchars($data['medewerker']->voornaam) ?>" required>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Tussenvoegsel</label>
                        <input type="text" name="tussenvoegsel" class="form-control" value="<?= htmlspecialchars($data['medewerker']->tussenvoegsel) ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Achternaam</label>
                        <input type="text" name="achternaam" class="form-control" value="<?= htmlspecialchars($data['medewerker']->achternaam) ?>" required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($data['medewerker']->email) ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Telefoon</label>
                        <input type="text" name="mobiel" class="form-control" value="<?= htmlspecialchars($data['medewerker']->mobiel) ?>">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Medewerker Nr.</label>
                        <input type="text" name="nummer" class="form-control" value="<?= htmlspecialchars($data['medewerker']->nummer) ?>">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Functie</label>
                        <input type="text" name="medewerkersoort" class="form-control" value="<?= htmlspecialchars($data['medewerker']->medewerkersoort) ?>">
                    </div>
                    <div class="col-md-4 d-flex align-items-end">
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" name="is_actief" id="is_actief" <?= $data['medewerker']->is_actief ? 'checked' : '' ?>>
                            <label class="form-check-label" for="is_actief">Actief</label>
                        </div>
                    </div>

                    <div class="col-12">
                        <label class="form-label">Opmerking</label>
                        <textarea name="opmerking" class="form-control" rows="3"><?= htmlspecialchars($data['medewerker']->opmerking) ?></textarea>
                    </div>

                    <div class="col-12 text-end">
                        <a href="<?= URLROOT ?>/medewerkers/detail/<?= $data['medewerker']->id ?>" class="btn btn-outline-secondary me-2">Annuleren</a>
                        <button class="btn btn-primary">Opslaan</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require_once APPROOT . '/views/includes/footer.php'; ?>
