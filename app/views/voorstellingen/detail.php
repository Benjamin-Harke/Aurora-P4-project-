<?php require_once APPROOT . '/views/includes/header.php'; ?>

<div class="container mt-5 mb-5">
    <!-- Back Button -->
    <div class="mb-4">
        <a href="<?= URLROOT ?>/voorstellingen" class="btn btn-outline-primary btn-sm">
            <i class="bi bi-arrow-left me-2"></i>Terug naar overzicht
        </a>
    </div>

    <?php if (isset($data['voorstelling'])): 
        $voorstelling = $data['voorstelling'];
        $date = new DateTime($voorstelling->datum);
        $time = new DateTime($voorstelling->tijd);
        $formattedDate = $date->format('d-m-Y');
        $formattedTime = $time->format('H:i');
        $formattedDay = $date->format('l');
        
        $dayNames = [
            'Monday' => 'Maandag',
            'Tuesday' => 'Dinsdag',
            'Wednesday' => 'Woensdag',
            'Thursday' => 'Donderdag',
            'Friday' => 'Vrijdag',
            'Saturday' => 'Zaterdag',
            'Sunday' => 'Zondag'
        ];
        $dutchDay = $dayNames[$formattedDay] ?? $formattedDay;
    ?>
        <div class="row">
            <!-- Main Content -->
            <div class="col-lg-8">
                <div class="card border-0 shadow-lg">
                    <!-- Header -->
                    <div class="card-header bg-gradient text-white" style="background: linear-gradient(135deg, var(--accent-bordeaux), var(--secondary-purple));">
                        <h1 class="mb-0"><?= htmlspecialchars($voorstelling->naam) ?></h1>
                    </div>

                    <!-- Body -->
                    <div class="card-body p-4">
                        <!-- Date & Time Info -->
                        <div class="info-section mb-4 pb-4 border-bottom">
                            <h5 class="mb-3">
                                <i class="bi bi-calendar-event text-primary me-2"></i>Datum & Tijd
                            </h5>
                            <div class="row">
                                <div class="col-md-6">
                                    <p><strong>Datum:</strong> <?= $formattedDate ?></p>
                                    <p><strong>Dag:</strong> <?= $dutchDay ?></p>
                                </div>
                                <div class="col-md-6">
                                    <p><strong>Starttijd:</strong> <?= $formattedTime ?></p>
                                </div>
                            </div>
                        </div>

                        <!-- Description -->
                        <?php if ($voorstelling->beschrijving): ?>
                            <div class="info-section mb-4 pb-4 border-bottom">
                                <h5 class="mb-3">
                                    <i class="bi bi-info-circle text-info me-2"></i>Beschrijving
                                </h5>
                                <p><?= nl2br(htmlspecialchars($voorstelling->beschrijving)) ?></p>
                            </div>
                        <?php endif; ?>

                        <!-- Ticket Information -->
                        <div class="info-section mb-4 pb-4 border-bottom">
                            <h5 class="mb-3">
                                <i class="bi bi-ticket-perforated text-success me-2"></i>Ticketinformatie
                            </h5>
                            <div class="row">
                                <div class="col-md-6">
                                    <p><strong>Beschikbare Kaarten:</strong></p>
                                    <h6><?= $voorstelling->max_aantal_tickets ?> kaarten</h6>
                                </div>
                                <div class="col-md-6">
                                    <p><strong>Status:</strong></p>
                                    <span class="badge bg-info ps-3 pe-3 py-2"><?= htmlspecialchars($voorstelling->beschikbaarheid) ?></span>
                                </div>
                            </div>
                        </div>

                        <!-- Additional Info -->
                        <div class="info-section">
                            <h5 class="mb-3">
                                <i class="bi bi-bookmark-check text-warning me-2"></i>Status
                            </h5>
                            <?php if ($voorstelling->is_actief): ?>
                                <span class="badge bg-success ps-3 pe-3 py-2">
                                    <i class="bi bi-check-circle me-1"></i>Deze voorstelling is actief
                                </span>
                            <?php else: ?>
                                <span class="badge bg-danger ps-3 pe-3 py-2">
                                    <i class="bi bi-x-circle me-1"></i>Deze voorstelling is niet actief
                                </span>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Footer -->
                    <div class="card-footer bg-light">
                        <button class="btn btn-primary-custom btn-lg" data-bs-toggle="modal" data-bs-target="#bookingModal">
                            <i class="bi bi-ticket-detailed me-2"></i>Kaarten Reserveren
                        </button>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm sticky-top" style="top: 100px;">
                    <div class="card-header bg-light">
                        <h5 class="mb-0">
                            <i class="bi bi-lightning-fill text-warning me-2"></i>Snelle Info
                        </h5>
                    </div>
                    <div class="card-body">
                        <ul class="list-unstyled">
                            <li class="mb-3 pb-3 border-bottom">
                                <strong>Voorstelling ID:</strong><br>
                                <code><?= $voorstelling->id ?></code>
                            </li>
                            <li class="mb-3 pb-3 border-bottom">
                                <strong>Datum:</strong><br>
                                <?= $formattedDate ?>
                            </li>
                            <li class="mb-3 pb-3 border-bottom">
                                <strong>Starttijd:</strong><br>
                                <?= $formattedTime ?>
                            </li>
                            <li>
                                <strong>Beschikbaarheid:</strong><br>
                                <span class="badge bg-info"><?= htmlspecialchars($voorstelling->beschikbaarheid) ?></span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    <?php else: ?>
        <div class="alert alert-danger">
            <i class="bi bi-exclamation-triangle me-2"></i>
            Voorstelling niet gevonden
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
</style>
