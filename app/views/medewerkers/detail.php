<?php require_once APPROOT . '/views/includes/header.php'; ?>
<?php require_once APPROOT . '/views/includes/messages.php'; ?>

<div class="container mt-5 mb-5">
    <!-- Back Button -->
    <div class="mb-4">
        <a href="<?= URLROOT ?>/medewerkers" class="btn btn-outline-primary btn-sm">
            <i class="bi bi-arrow-left me-2"></i>Terug naar overzicht
        </a>
    </div>

    <?php if (isset($data['medewerker'])): 
        $medewerker = $data['medewerker'];
        $fullName = '';
        if (!empty($medewerker->tussenvoegsel)) {
            $fullName = $medewerker->voornaam . ' ' . $medewerker->tussenvoegsel . ' ' . $medewerker->achternaam;
        } else {
            $fullName = $medewerker->voornaam . ' ' . $medewerker->achternaam;
        }
    ?>
        <div class="row">
            <!-- Main Content -->
            <div class="col-lg-8">
                <div class="card border-0 shadow-lg">
                    <!-- Header -->
                    <div class="card-header bg-gradient text-white" style="background: linear-gradient(135deg, var(--accent-bordeaux), var(--secondary-purple));">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-person-circle" style="font-size: 3rem; margin-right: 20px;"></i>
                            <div>
                                <h1 class="mb-0"><?= htmlspecialchars($fullName) ?></h1>
                                <p class="mb-0 text-white-50"><?= htmlspecialchars($medewerker->medewerkersoort) ?></p>
                            </div>
                        </div>
                    </div>

                    <!-- Body -->
                    <div class="card-body p-4">
                        <!-- Contact Information -->
                        <div class="info-section mb-4 pb-4 border-bottom">
                            <h5 class="mb-3">
                                <i class="bi bi-telephone text-primary me-2"></i>Contactgegevens
                            </h5>
                            <div class="row">
                                <div class="col-md-6">
                                    <p><strong>Email:</strong></p>
                                    <a href="mailto:<?= htmlspecialchars($medewerker->email) ?>" class="text-decoration-none">
                                        <i class="bi bi-envelope me-2"></i><?= htmlspecialchars($medewerker->email) ?>
                                    </a>
                                </div>
                                <div class="col-md-6">
                                    <p><strong>Telefoonnummer:</strong></p>
                                    <a href="tel:<?= preg_replace('/\D/', '', $medewerker->mobiel) ?>" class="text-decoration-none">
                                        <i class="bi bi-telephone me-2"></i><?= htmlspecialchars($medewerker->mobiel) ?>
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- Employee Information -->
                        <div class="info-section mb-4 pb-4 border-bottom">
                            <h5 class="mb-3">
                                <i class="bi bi-briefcase text-info me-2"></i>Werkgegevens
                            </h5>
                            <div class="row">
                                <div class="col-md-6">
                                    <p><strong>Medewerkersoort:</strong></p>
                                    <span class="badge bg-info ps-3 pe-3 py-2"><?= htmlspecialchars($medewerker->medewerkersoort) ?></span>
                                </div>
                                <div class="col-md-6">
                                    <p><strong>Medewerker Nummer:</strong></p>
                                    <code><?= htmlspecialchars($medewerker->nummer) ?></code>
                                </div>
                            </div>
                        </div>

                        <!-- Status -->
                        <div class="info-section mb-4 pb-4 border-bottom">
                            <h5 class="mb-3">
                                <i class="bi bi-bookmark-check text-warning me-2"></i>Status
                            </h5>
                            <?php if ($medewerker->is_actief): ?>
                                <span class="badge bg-success ps-3 pe-3 py-2">
                                    <i class="bi bi-check-circle me-1"></i>Actieve Medewerker
                                </span>
                            <?php else: ?>
                                <span class="badge bg-danger ps-3 pe-3 py-2">
                                    <i class="bi bi-x-circle me-1"></i>Inactieve Medewerker
                                </span>
                            <?php endif; ?>
                        </div>

                        <!-- Additional Notes -->
                        <?php if (!empty($medewerker->opmerking)): ?>
                            <div class="info-section">
                                <h5 class="mb-3">
                                    <i class="bi bi-sticky text-secondary me-2"></i>Opmerkingen
                                </h5>
                                <p class="text-muted"><?= nl2br(htmlspecialchars($medewerker->opmerking)) ?></p>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Footer -->
                    <div class="card-footer bg-light">
                        <a href="mailto:<?= htmlspecialchars($medewerker->email) ?>" class="btn btn-primary-custom me-2">
                            <i class="bi bi-envelope me-2"></i>Email Sturen
                        </a>
                        <a href="tel:<?= preg_replace('/\D/', '', $medewerker->mobiel) ?>" class="btn btn-outline-primary">
                            <i class="bi bi-telephone me-2"></i>Bellen
                        </a>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="col-lg-4">
                <!-- Quick Actions Card -->
                <div class="card border-0 shadow-sm mt-4">
                    <div class="card-header bg-light">
                        <h5 class="mb-0">
                            <i class="bi bi-share text-info me-2"></i>Contacteer
                        </h5>
                    </div>
                    <div class="card-body d-grid gap-2">
                        <a href="mailto:<?= htmlspecialchars($medewerker->email) ?>" class="btn btn-sm btn-primary">
                            <i class="bi bi-envelope me-2"></i>Email
                        </a>
                        <a href="tel:<?= preg_replace('/\D/', '', $medewerker->mobiel) ?>" class="btn btn-sm btn-info">
                            <i class="bi bi-telephone me-2"></i>Bellen
                        </a>
                        <a href="<?= URLROOT ?>/medewerkers/edit/<?= $medewerker->id ?>" class="btn btn-sm btn-outline-primary">
                            <i class="bi bi-pencil-square me-2"></i>Bewerk
                        </a>
                        <form method="post" action="<?= URLROOT ?>/medewerkers/delete" onsubmit="return confirm('Weet je zeker dat je deze medewerker wilt verwijderen?');" style="display:inline-block;">
                            <input type="hidden" name="id" value="<?= $medewerker->id ?>">
                            <button type="submit" class="btn btn-sm btn-danger">
                                <i class="bi bi-trash me-2"></i>Verwijder
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    <?php else: ?>
        <div class="alert alert-danger">
            <i class="bi bi-exclamation-triangle me-2"></i>
            Medewerker niet gevonden
        </div>
    <?php endif; ?>
</div>

<?php require_once APPROOT . '/views/includes/footer.php'; ?>

<style>
.info-section h5 {
    color: var(--accent-bordeaux);
    font-weight: 600;
}

.info-section p {
    margin-bottom: 0.5rem;
}

code {
    background-color: #f5f5f5;
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 0.9rem;
}

a.btn-primary-custom {
    background-color: var(--accent-bordeaux);
    color: white;
    border: none;
}

a.btn-primary-custom:hover {
    background-color: var(--secondary-purple);
    color: white;
}
</style>
