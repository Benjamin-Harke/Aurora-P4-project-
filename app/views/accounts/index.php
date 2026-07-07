<?php
/**
 * Account Overview View
 * @var array $data Contains: title, users
 */
require_once APPROOT . '/views/includes/header.php'; ?>

<!-- Account Overview Section -->
<section class="overview-section">
  <div class="container">
    <!-- Header -->
    <div class="overview-header mb-5">
      <h1>Account Overview</h1>
      <p class="subtitle">Manage and view all registered accounts</p>
    </div>

    <!-- Action bar: Back (left) + Create Account (right) -->
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
      <a href="<?= URLROOT; ?>/dashboard" class="btn btn-outline-custom">
        <i class="bi bi-arrow-left"></i> Back to Dashboard
      </a>
      <a href="<?= URLROOT; ?>/accounts/create" class="btn btn-primary-custom">
        <i class="bi bi-plus-lg"></i> Create Account
      </a>
    </div>

    <!-- Accounts Table or No Accounts Message -->
    <?php if (empty($data['users'])): ?>
      <!-- No Accounts Error -->
      <div class="alert alert-warning alert-custom" role="alert">
        <div class="alert-content">
          <i class="bi bi-exclamation-circle"></i>
          <div>
            <h4 class="alert-heading">No Accounts Found</h4>
            <p>There are currently no registered accounts in the system.</p>
          </div>
        </div>
      </div>
    <?php else: ?>
      <!-- Accounts Table -->
      <div class="table-responsive">
        <table class="table table-dark table-hover accounts-table">
          <thead>
            <tr>
              <th>Email</th>
              <th>First Name</th>
              <th>Last Name</th>
              <th>Roles</th>
              <th>Created At</th>
              <th>Status</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($data['users'] as $user): ?>
              <?php 
                $loggedInId = (int)($_SESSION['accountId'] ?? 0);
                $targetId = (int)$user['id'];
                $loggedInRole = strtolower($_SESSION['rolle'] ?? 'bezoeker');
                $targetRole = !empty($user['roles']) ? strtolower(trim($user['roles'][0])) : 'bezoeker';

                $roleRanks = [
                    'admin' => 3,
                    'administrator' => 3,
                    'medewerker' => 2,
                    'receptie' => 1,
                    'bezoeker' => 0
                ];
                $loggedInRank = $roleRanks[$loggedInRole] ?? 0;
                $targetRank = $roleRanks[$targetRole] ?? 0;

                $canEdit = ($loggedInId === $targetId) || ($loggedInRank === 3) || ($loggedInRank > $targetRank);
                $canDelete = ($loggedInId !== $targetId) && (($loggedInRank === 3) || ($loggedInRank > $targetRank));
              ?>
              <tr data-active="<?= $user['is_actief'] ? '1' : '0'; ?>">
                <td class="email-cell" data-label="Email"><?= htmlspecialchars($user['email'] ?? $user['gebruikersnaam'] ?? ''); ?></td>
                <td data-label="First Name"><?= htmlspecialchars($user['voornaam']); ?></td>
                <td data-label="Last Name"><?= htmlspecialchars(($user['tussenvoegsel'] ? $user['tussenvoegsel'] . ' ' : '') . $user['achternaam']); ?></td>
                <td data-label="Roles">
                  <?php if (!empty($user['roles'])): ?>
                    <?php foreach ($user['roles'] as $role): ?>
                      <span class="badge badge-role" style="margin: 2px;">
                        <?= htmlspecialchars(ucfirst(trim($role))); ?>
                      </span>
                    <?php endforeach; ?>
                  <?php else: ?>
                    <span class="badge badge-secondary">No Role</span>
                  <?php endif; ?>
                </td>
                <td data-label="Created"><?= date('d-m-Y H:i', strtotime($user['datum_aangemaakt'])); ?></td>
                <td data-label="Status">
                  <?php if ($user['is_actief']): ?>
                    <span class="badge badge-active">
                      <i class="bi bi-check-circle-fill"></i> Active
                    </span>
                  <?php else: ?>
                    <span class="badge badge-inactive">
                      <i class="bi bi-x-circle-fill"></i> Inactive
                    </span>
                  <?php endif; ?>
                </td>
                <td data-label="">
                  <div class="d-flex flex-wrap gap-2">
                    <?php if ($canEdit): ?>
                      <a href="<?= URLROOT; ?>/accounts/edit/<?= $user['id']; ?>" class="btn btn-sm btn-outline-info">
                        <i class="bi bi-pencil-square"></i> Edit
                      </a>
                    <?php endif; ?>
                    <?php if ($canDelete): ?>
                      <a href="<?= URLROOT; ?>/accounts/delete/<?= $user['id']; ?>" class="btn btn-sm btn-outline-danger"
                        onclick="return confirm('Weet je zeker dat je dit account wilt verwijderen?');">
                        <i class="bi bi-trash"></i> Delete
                      </a>
                    <?php endif; ?>
                  </div>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>

      <!-- Summary Stats -->
      <div class="row g-4 mt-5">
        <div class="col-md-4">
          <div class="stat-card" data-filter="all">
            <div class="stat-icon">
              <i class="bi bi-people"></i>
            </div>
            <div class="stat-content">
              <span class="stat-label">Total Accounts</span>
              <span class="stat-value"><?= count($data['users']); ?></span>
            </div>
          </div>
        </div>
        <div class="col-md-4">
          <div class="stat-card" data-filter="active">
            <div class="stat-icon">
              <i class="bi bi-check-circle"></i>
            </div>
            <div class="stat-content">
              <span class="stat-label">Active Accounts</span>
              <span class="stat-value"><?= count(array_filter($data['users'], fn($a) => $a['is_actief'])); ?></span>
            </div>
          </div>
        </div>
        <div class="col-md-4">
          <div class="stat-card" data-filter="inactive">
            <div class="stat-icon">
              <i class="bi bi-x-circle"></i>
            </div>
            <div class="stat-content">
              <span class="stat-label">Inactive Accounts</span>
              <span class="stat-value"><?= count(array_filter($data['users'], fn($a) => !$a['is_actief'])); ?></span>
            </div>
          </div>
        </div>
      </div>
    <?php endif; ?>
  </div>
</section>

<!-- Additional Styles -->
<style>
  .overview-section {
    padding: 60px 0;
    background: linear-gradient(135deg, rgba(0, 20, 40, 0.7), rgba(0, 30, 60, 0.9));
    min-height: 600px;
  }

  .overview-header {
    text-align: center;
    margin-bottom: 40px;
  }

  .overview-header h1 {
    font-size: 2.5rem;
    color: var(--primary-teal);
    margin-bottom: 10px;
    text-transform: uppercase;
    letter-spacing: 2px;
  }

  .overview-header .subtitle {
    font-size: 1.1rem;
    color: rgba(255, 255, 255, 0.7);
  }

  .table-responsive {
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 8px 32px rgba(0, 217, 255, 0.1);
  }

  .accounts-table {
    margin-bottom: 0;
    background: rgba(255, 255, 255, 0.03);
    border-color: rgba(255, 215, 0, 0.1);
  }

  .accounts-table thead th {
    background: rgba(0, 217, 255, 0.1);
    border-color: rgba(255, 215, 0, 0.2);
    color: var(--primary-teal);
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 1px;
    padding: 15px;
  }

  .accounts-table tbody tr {
    border-color: rgba(255, 215, 0, 0.1);
    transition: all 0.3s ease;
  }

  .accounts-table tbody tr:hover {
    background: rgba(0, 217, 255, 0.05);
    box-shadow: inset 0 0 10px rgba(0, 217, 255, 0.1);
  }

  .accounts-table td {
    padding: 15px;
    vertical-align: middle;
    color: rgba(255, 255, 255, 0.9);
  }

  .email-cell {
    color: var(--primary-teal);
    font-weight: 500;
  }

  .badge-active {
    background: rgba(0, 217, 255, 0.2);
    color: var(--primary-teal);
    border: 1px solid var(--primary-teal);
    padding: 5px 12px;
    border-radius: 20px;
    font-weight: 500;
  }

  .badge-inactive {
    background: rgba(255, 0, 110, 0.2);
    color: var(--accent-magenta);
    border: 1px solid var(--accent-magenta);
    padding: 5px 12px;
    border-radius: 20px;
    font-weight: 500;
  }

  .alert-custom {
    background: rgba(255, 193, 7, 0.1);
    border: 2px solid var(--accent-gold);
    border-radius: 12px;
    padding: 30px;
    color: var(--accent-gold);
  }

  .alert-content {
    display: flex;
    align-items: flex-start;
    gap: 20px;
  }

  .alert-content i {
    font-size: 24px;
    margin-top: 5px;
  }

  .alert-custom h4 {
    color: var(--accent-gold);
    margin-bottom: 10px;
  }

  .alert-custom p {
    margin: 0;
    color: rgba(255, 215, 0, 0.8);
  }

  .stat-card {
    background: rgba(255, 255, 255, 0.05);
    border: 1px solid rgba(255, 215, 0, 0.2);
    border-radius: 12px;
    padding: 25px;
    display: flex;
    align-items: center;
    gap: 20px;
    transition: all 0.3s ease;
    cursor: pointer;
  }

  .stat-card:hover {
    background: rgba(255, 255, 255, 0.1);
    border-color: var(--accent-gold);
    transform: translateY(-5px);
    box-shadow: 0 10px 30px rgba(255, 215, 0, 0.2);
  }

  .stat-card.active-filter {
    border-color: var(--primary-teal);
    box-shadow: 0 0 15px rgba(0, 217, 255, 0.3);
    background: rgba(0, 217, 255, 0.05);
  }

  .stat-icon {
    font-size: 40px;
    color: var(--accent-gold);
  }

  .stat-content {
    display: flex;
    flex-direction: column;
  }

  .stat-label {
    color: rgba(255, 255, 255, 0.7);
    font-size: 0.9rem;
    text-transform: uppercase;
    letter-spacing: 1px;
  }

  .stat-value {
    color: var(--primary-teal);
    font-size: 2rem;
    font-weight: bold;
    line-height: 1;
  }

  .btn-outline-info {
    color: var(--primary-teal);
    border-color: var(--primary-teal);
    transition: all 0.3s ease;
  }

  .btn-outline-info:hover {
    background: var(--primary-teal);
    border-color: var(--primary-teal);
    color: var(--bg-dark);
  }

  .badge-role {
    background: rgba(0, 217, 255, 0.2);
    color: var(--primary-teal);
    border: 1px solid var(--primary-teal);
    padding: 4px 8px;
    border-radius: 12px;
    font-size: 0.8rem;
    font-weight: 500;
  }

  /* ---- Responsive: Account Overview ---- */

  @media (max-width: 768px) {
    .overview-section {
      padding: 40px 0;
    }

    .overview-header h1 {
      font-size: 1.8rem;
      letter-spacing: 1px;
    }

    .overview-header .subtitle {
      font-size: 0.95rem;
    }

    /* Table → card layout */
    .accounts-table thead {
      display: none;
    }

    .accounts-table,
    .accounts-table tbody,
    .accounts-table tr,
    .accounts-table td {
      display: block;
      width: 100%;
    }

    .accounts-table tbody tr {
      background: rgba(255, 255, 255, 0.03);
      border: 1px solid rgba(0, 217, 255, 0.15);
      border-radius: 12px;
      margin-bottom: 1rem;
      padding: 1rem;
      overflow: hidden;
    }

    .accounts-table tbody tr:hover {
      border-color: var(--primary-teal);
    }

    .accounts-table td {
      padding: 0.5rem 0;
      border: none;
      display: flex;
      justify-content: space-between;
      align-items: center;
      gap: 0.5rem;
    }

    /* Data labels for mobile card rows */
    .accounts-table td::before {
      content: attr(data-label);
      font-weight: 700;
      font-size: 0.75rem;
      color: var(--primary-teal);
      text-transform: uppercase;
      letter-spacing: 0.5px;
      flex-shrink: 0;
      min-width: 90px;
    }

    .accounts-table td.email-cell {
      border-bottom: 1px solid rgba(0, 217, 255, 0.1);
      padding-bottom: 0.75rem;
      margin-bottom: 0.25rem;
      word-break: break-all;
    }

    .accounts-table td:last-child {
      padding-top: 0.75rem;
      border-top: 1px solid rgba(0, 217, 255, 0.1);
      margin-top: 0.25rem;
      justify-content: flex-end;
    }

    .accounts-table td:last-child::before {
      display: none;
    }

    /* Stat cards: full-width stacked */
    .stat-card {
      padding: 18px;
      gap: 14px;
    }

    .stat-icon {
      font-size: 30px;
    }

    .stat-value {
      font-size: 1.6rem;
    }
  }

  @media (max-width: 576px) {
    .overview-section {
      padding: 30px 0;
    }

    .overview-header {
      margin-bottom: 24px;
    }

    .overview-header h1 {
      font-size: 1.4rem;
    }

    .overview-header .subtitle {
      font-size: 0.85rem;
    }

    .accounts-table tbody tr {
      padding: 0.8rem;
    }

    .accounts-table td {
      font-size: 0.85rem;
      flex-direction: column;
      align-items: flex-start;
      gap: 0.25rem;
    }

    .accounts-table td::before {
      font-size: 0.7rem;
    }

    .stat-card {
      padding: 14px;
    }

    .stat-value {
      font-size: 1.4rem;
    }

    .stat-label {
      font-size: 0.78rem;
    }
  }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const cards = document.querySelectorAll('.stat-card');
    const rows = document.querySelectorAll('.accounts-table tbody tr');

    cards.forEach(card => {
        card.addEventListener('click', function() {
            const filter = this.getAttribute('data-filter');

            // Toggle active styling
            cards.forEach(c => c.classList.remove('active-filter'));
            this.classList.add('active-filter');

            rows.forEach(row => {
                const isActive = row.getAttribute('data-active');
                if (filter === 'all') {
                    row.style.display = '';
                } else if (filter === 'active') {
                    row.style.display = isActive === '1' ? '' : 'none';
                } else if (filter === 'inactive') {
                    row.style.display = isActive === '0' ? '' : 'none';
                }
            });
        });
    });

    // Set the 'all' card as default active filter visual
    const allCard = document.querySelector('.stat-card[data-filter="all"]');
    if (allCard) {
        allCard.classList.add('active-filter');
    }
});
</script>

<?php require_once APPROOT . '/views/includes/footer.php'; ?>