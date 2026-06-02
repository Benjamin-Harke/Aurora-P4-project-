<?php require_once APPROOT . '/views/includes/header.php'; ?>

<div class="container mt-5 mb-5">
    <div class="row mb-4">
        <div class="col-lg-8">
            <h1 class="display-4">Medewerkers Directory</h1>
            <p class="lead text-muted">Vind contactgegevens van onze medewerkers en organiseer eenvoudig communicatie</p>
        </div>
        <div class="col-lg-4">
            <div class="search-box">
                <input type="text" class="form-control" placeholder="Zoek medewerker..." id="searchInput">
            </div>
        </div>
    </div>

    <?php if (empty($data['medewerkers'])): ?>
        <div class="alert alert-info text-center">
            <i class="bi bi-info-circle me-2"></i>
            Momenteel zijn er geen medewerkers beschikbaar.
        </div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table table-hover align-middle employees-table">
                <thead class="table-dark">
                    <tr>
                        <th scope="col">
                            <i class="bi bi-person-circle me-2"></i>Naam
                        </th>
                        <th scope="col">
                            <i class="bi bi-briefcase me-2"></i>Functie
                        </th>
                        <th scope="col">
                            <i class="bi bi-envelope me-2"></i>Email
                        </th>
                        <th scope="col">
                            <i class="bi bi-telephone me-2"></i>Telefoonnummer
                        </th>
                        <th scope="col">
                            <i class="bi bi-hash me-2"></i>Medewerker Nr.
                        </th>
                        <th scope="col">Status</th>
                        <th scope="col">Actie</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($data['medewerkers'] as $medewerker): ?>
                        <tr class="medewerker-row" data-searchtext="<?= strtolower($medewerker->voornaam . ' ' . $medewerker->tussenvoegsel . ' ' . $medewerker->achternaam . ' ' . $medewerker->email . ' ' . $medewerker->mobiel) ?>">
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar me-2">
                                        <i class="bi bi-person-circle" style="font-size: 2rem; color: var(--accent-bordeaux);"></i>
                                    </div>
                                    <div>
                                        <strong><?= htmlspecialchars($medewerker->voornaam) ?></strong><br>
                                        <small class="text-muted">
                                            <?php 
                                            $fullName = '';
                                            if (!empty($medewerker->tussenvoegsel)) {
                                                $fullName = $medewerker->tussenvoegsel . ' ';
                                            }
                                            $fullName .= $medewerker->achternaam;
                                            echo htmlspecialchars($fullName);
                                            ?>
                                        </small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-info"><?= htmlspecialchars($medewerker->medewerkersoort) ?></span>
                            </td>
                            <td>
                                <a href="mailto:<?= htmlspecialchars($medewerker->email) ?>">
                                    <?= htmlspecialchars($medewerker->email) ?>
                                </a>
                            </td>
                            <td>
                                <a href="tel:<?= preg_replace('/\D/', '', $medewerker->mobiel) ?>">
                                    <?= htmlspecialchars($medewerker->mobiel) ?>
                                </a>
                            </td>
                            <td>
                                <code><?= htmlspecialchars($medewerker->nummer) ?></code>
                            </td>
                            <td>
                                <?php if ($medewerker->is_actief): ?>
                                    <span class="badge bg-success">
                                        <i class="bi bi-check-circle me-1"></i>Actief
                                    </span>
                                <?php else: ?>
                                    <span class="badge bg-danger">
                                        <i class="bi bi-x-circle me-1"></i>Inactief
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <a href="<?= URLROOT ?>/medewerkers/detail/<?= $medewerker->id ?>" class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-eye me-1"></i>Details
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Summary -->
        <div class="row mt-4">
            <div class="col-md-4">
                <div class="card text-center border-0 shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title">Totale Medewerkers</h5>
                        <h2 class="text-primary"><?= count($data['medewerkers']) ?></h2>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card text-center border-0 shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title">Actieve Medewerkers</h5>
                        <h2 class="text-success">
                            <?php 
                            echo count(array_filter($data['medewerkers'], function($m) { 
                                return $m->is_actief; 
                            })); 
                            ?>
                        </h2>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card text-center border-0 shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title">Functies</h5>
                        <h2 class="text-info">
                            <?php 
                            echo count(array_unique(array_column($data['medewerkers'], 'medewerkersoort'))); 
                            ?>
                        </h2>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php require_once APPROOT . '/views/includes/footer.php'; ?>

<style>
.employees-table {
    background-color: white;
}

.employees-table tbody tr {
    transition: background-color 0.3s ease;
}

.employees-table tbody tr:hover {
    background-color: #f8f9fa;
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

.avatar {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: #f0f0f0;
}

.table a {
    text-decoration: none;
    color: var(--accent-bordeaux);
}

.table a:hover {
    color: var(--secondary-purple);
    text-decoration: underline;
}
</style>

<script>
// Search functionality for employees
document.getElementById('searchInput').addEventListener('keyup', function() {
    const searchTerm = this.value.toLowerCase();
    const rows = document.querySelectorAll('.medewerker-row');
    
    rows.forEach(row => {
        const searchText = row.dataset.searchtext;
        if (searchText.includes(searchTerm)) {
            row.style.display = 'table-row';
        } else {
            row.style.display = 'none';
        }
    });
});
</script>
