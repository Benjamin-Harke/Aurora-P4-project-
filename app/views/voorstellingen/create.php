<?php require_once APPROOT . '/views/includes/header.php'; ?>
<?php require_once APPROOT . '/views/includes/messages.php'; ?>

<div class="container mt-5 mb-5">
    <div class="mb-4">
        <a href="<?= URLROOT ?>/voorstellingen" class="btn btn-outline-primary btn-sm">
            <i class="bi bi-arrow-left me-2"></i>Terug naar overzicht
        </a>
    </div>

    <div class="card border-0 shadow-lg">
        <div class="card-header bg-gradient text-white" style="background: linear-gradient(135deg, var(--accent-bordeaux), var(--secondary-purple));">
            <h3 class="mb-0">Nieuwe Voorstelling</h3>
        </div>
        <div class="card-body p-4">
            <form method="post" action="<?= URLROOT ?>/voorstellingen/store">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Titel</label>
                        <input type="text" name="naam" class="form-control" value="<?= htmlspecialchars($data['naam'] ?? '') ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Datum</label>
                        <input type="date" name="datum" class="form-control" value="<?= htmlspecialchars($data['datum'] ?? '') ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Tijd</label>
                        <input type="time" name="tijd" class="form-control" value="<?= htmlspecialchars($data['tijd'] ?? '') ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Max aantal tickets</label>
                        <input type="number" name="max_aantal_tickets" class="form-control" value="<?= htmlspecialchars($data['max_aantal_tickets'] ?? 100) ?>" required>
                    </div>
                    <div class="col-12">
                        <label class="form-label">Beschrijving</label>
                        <textarea name="beschrijving" class="form-control" rows="4"><?= htmlspecialchars($data['beschrijving'] ?? '') ?></textarea>
                    </div>
                    <div class="col-12 text-end">
                        <a href="<?= URLROOT ?>/voorstellingen" class="btn btn-outline-secondary me-2">Annuleren</a>
                        <button class="btn btn-primary">Opslaan</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require_once APPROOT . '/views/includes/footer.php'; ?>
