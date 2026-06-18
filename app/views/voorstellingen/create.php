<?php require_once APPROOT . '/views/includes/header.php'; ?>

<?php
$data = $data ?? [
    'naam' => '',
    'beschrijving' => '',
    'datum' => '',
    'tijd' => '',
    'locatie' => '',
    'max_aantal_tickets' => '100',
    'beschikbaarheid' => 'Ingepland',
    'errors' => []
];
?>

<div class="container mt-5 mb-5">
    <?php require_once APPROOT . '/views/includes/messages.php'; ?>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-gradient text-white" style="background: linear-gradient(135deg, var(--accent-bordeaux), var(--secondary-purple));">
                    <h2 class="mb-0">Nieuwe voorstelling toevoegen</h2>
                </div>
                <div class="card-body">
                    <form method="post" action="<?= URLROOT ?>/voorstellingen/create">
                        <div class="mb-3">
                            <label for="naam" class="form-label">Titel</label>
                            <input type="text" class="form-control <?php echo isset($data['errors']['naam']) ? 'is-invalid' : ''; ?>" id="naam" name="naam" value="<?= htmlspecialchars($data['naam']) ?>">
                            <?php if (isset($data['errors']['naam'])): ?>
                                <div class="invalid-feedback"><?= htmlspecialchars($data['errors']['naam']) ?></div>
                            <?php endif; ?>
                        </div>

                        <div class="mb-3">
                            <label for="beschrijving" class="form-label">Beschrijving</label>
                            <textarea class="form-control" id="beschrijving" name="beschrijving" rows="4"><?= htmlspecialchars($data['beschrijving']) ?></textarea>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="datum" class="form-label">Datum</label>
                                <input type="date" class="form-control <?php echo isset($data['errors']['datum']) ? 'is-invalid' : ''; ?>" id="datum" name="datum" value="<?= htmlspecialchars($data['datum']) ?>">
                                <?php if (isset($data['errors']['datum'])): ?>
                                    <div class="invalid-feedback"><?= htmlspecialchars($data['errors']['datum']) ?></div>
                                <?php endif; ?>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="tijd" class="form-label">Tijd</label>
                                <input type="time" class="form-control <?php echo isset($data['errors']['tijd']) ? 'is-invalid' : ''; ?>" id="tijd" name="tijd" value="<?= htmlspecialchars($data['tijd']) ?>">
                                <?php if (isset($data['errors']['tijd'])): ?>
                                    <div class="invalid-feedback"><?= htmlspecialchars($data['errors']['tijd']) ?></div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="locatie" class="form-label">Locatie</label>
                            <input type="text" class="form-control <?php echo isset($data['errors']['locatie']) ? 'is-invalid' : ''; ?>" id="locatie" name="locatie" value="<?= htmlspecialchars($data['locatie']) ?>">
                            <?php if (isset($data['errors']['locatie'])): ?>
                                <div class="invalid-feedback"><?= htmlspecialchars($data['errors']['locatie']) ?></div>
                            <?php endif; ?>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="max_aantal_tickets" class="form-label">Maximaal aantal tickets</label>
                                <input type="number" min="1" class="form-control <?php echo isset($data['errors']['max_aantal_tickets']) ? 'is-invalid' : ''; ?>" id="max_aantal_tickets" name="max_aantal_tickets" value="<?= htmlspecialchars($data['max_aantal_tickets']) ?>">
                                <?php if (isset($data['errors']['max_aantal_tickets'])): ?>
                                    <div class="invalid-feedback"><?= htmlspecialchars($data['errors']['max_aantal_tickets']) ?></div>
                                <?php endif; ?>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="beschikbaarheid" class="form-label">Beschikbaarheid</label>
                                <select class="form-select" id="beschikbaarheid" name="beschikbaarheid">
                                    <option value="Ingepland" <?= $data['beschikbaarheid'] === 'Ingepland' ? 'selected' : '' ?>>Ingepland</option>
                                    <option value="Uitverkocht" <?= $data['beschikbaarheid'] === 'Uitverkocht' ? 'selected' : '' ?>>Uitverkocht</option>
                                    <option value="Beperkt beschikbaar" <?= $data['beschikbaarheid'] === 'Beperkt beschikbaar' ? 'selected' : '' ?>>Beperkt beschikbaar</option>
                                </select>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between align-items-center mt-4">
                            <a href="<?= URLROOT ?>/voorstellingen" class="btn btn-secondary">Annuleren</a>
                            <button type="submit" class="btn btn-primary-custom">Voorstelling opslaan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once APPROOT . '/views/includes/footer.php'; ?>
