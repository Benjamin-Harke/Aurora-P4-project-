<?php require_once APPROOT . '/views/includes/header.php'; ?>

<div class="container mt-5 mb-5">
    <?php require_once APPROOT . '/views/includes/messages.php'; ?>

    <div class="row mb-4">
        <div class="col-lg-8">
            <h1 class="display-4">Alle Voorstellingen</h1>
            <p class="lead text-muted">Bekijk ons volledige programma en kies een voorstelling die u wil bezoeken</p>
        </div>
        <div class="col-lg-4 text-end">
            <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin'): ?>
                <a href="<?= URLROOT ?>/voorstellingen/create" class="btn btn-primary-custom mt-3">
                    <i class="bi bi-plus-lg me-2"></i>Nieuwe voorstelling toevoegen
                </a>
            <?php endif; ?>
        </div>
    </div>
    <div class="row mb-4">
        <div class="col-lg-12">
            <div class="search-box">
                <input type="text" class="form-control" placeholder="Zoek naar voorstellingen..." id="searchInput">
            </div>
        </div>
    </div>

    <?php if (empty($data['voorstellingen'])): ?>
        <div class="alert alert-info text-center">
            <i class="bi bi-info-circle me-2"></i>
            Momenteel zijn er geen voorstellingen beschikbaar.
        </div>
    <?php else: ?>
        <div class="row g-4">
            <?php foreach ($data['voorstellingen'] as $voorstelling): 
                // Only show active performances
                if (!$voorstelling->is_actief) continue;
                
                // Format date and time
                $date = new DateTime($voorstelling->datum);
                $time = new DateTime($voorstelling->tijd);
                $formattedDate = $date->format('d-m-Y');
                $formattedTime = $time->format('H:i');
                $formattedDayName = $date->format('l');
                
                // Translate day names to Dutch
                $dayNames = [
                    'Monday' => 'Maandag',
                    'Tuesday' => 'Dinsdag',
                    'Wednesday' => 'Woensdag',
                    'Thursday' => 'Donderdag',
                    'Friday' => 'Vrijdag',
                    'Saturday' => 'Zaterdag',
                    'Sunday' => 'Zondag'
                ];
                $dutchDay = $dayNames[$formattedDayName] ?? $formattedDayName;
            ?>
                <div class="col-md-6 col-lg-4">
                    <div class="card h-100 performance-card border-0 shadow-sm">
                        <!-- Card Header with Badge -->
                        <div class="card-header bg-gradient border-0 position-relative" style="background: linear-gradient(135deg, var(--accent-bordeaux), var(--secondary-purple));">
                            <span class="badge bg-warning text-dark position-absolute top-0 end-0 m-2">
                                <i class="bi bi-calendar-event"></i> <?= $formattedDate ?>
                            </span>
                        </div>

                        <!-- Card Body -->
                        <div class="card-body">
                            <h5 class="card-title"><?= htmlspecialchars($voorstelling->naam) ?></h5>
                            
                            <?php if ($voorstelling->beschrijving): ?>
                                <p class="card-text text-muted small">
                                    <?= htmlspecialchars(substr($voorstelling->beschrijving, 0, 100)) ?>
                                    <?php if (strlen($voorstelling->beschrijving) > 100): ?>...<?php endif; ?>
                                </p>
                            <?php endif; ?>

                            <!-- Event Details -->
                            <div class="event-details">
                                <div class="detail-item mb-2">
                                    <i class="bi bi-clock text-primary me-2"></i>
                                    <strong><?= $formattedTime ?></strong> - <?= $dutchDay ?>
                                </div>
                                <div class="detail-item mb-2">
                                    <i class="bi bi-ticket-perforated text-success me-2"></i>
                                    <strong><?= $voorstelling->max_aantal_tickets ?></strong> Beschikbare kaarten
                                </div>
                                <div class="detail-item">
                                    <i class="bi bi-bookmark-check text-info me-2"></i>
                                    <span class="badge bg-light text-dark"><?= htmlspecialchars($voorstelling->beschikbaarheid) ?></span>
                                </div>
                            </div>
                        </div>

                        <!-- Card Footer with Action -->
                        <div class="card-footer bg-light border-top">
                            <a href="<?= URLROOT ?>/voorstellingen/detail/<?= $voorstelling->id ?>" class="btn btn-primary-custom w-100">
                                <i class="bi bi-arrow-right me-2"></i>Meer informatie
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php require_once APPROOT . '/views/includes/footer.php'; ?>

<style>
.performance-card {
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.performance-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15) !important;
}

.event-details {
    font-size: 0.95rem;
}

.detail-item {
    display: flex;
    align-items: center;
}

.search-box {
    position: relative;
}

.search-box input {
    border-radius: 50px;
    padding: 10px 20px;
    border: 2px solid #e0e0e0;
    transition: border-color 0.3s ease;
}

.search-box input:focus {
    border-color: var(--accent-bordeaux);
    box-shadow: 0 0 0 0.2rem rgba(128, 0, 32, 0.25);
}
</style>

<script>
// Simple search functionality
document.getElementById('searchInput').addEventListener('keyup', function() {
    const searchTerm = this.value.toLowerCase();
    const cards = document.querySelectorAll('.performance-card');
    
    cards.forEach(card => {
        const title = card.querySelector('.card-title').textContent.toLowerCase();
        const description = card.querySelector('.card-text')?.textContent.toLowerCase() || '';
        
        if (title.includes(searchTerm) || description.includes(searchTerm)) {
            card.parentElement.style.display = 'block';
        } else {
            card.parentElement.style.display = 'none';
        }
    });
});
</script>
