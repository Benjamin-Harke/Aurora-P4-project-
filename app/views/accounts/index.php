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
              <tr>
                <td class="email-cell"><?= htmlspecialchars($user['email']); ?></td>
                <td><?= htmlspecialchars($user['voornaam']); ?></td>
                <td><?= htmlspecialchars(($user['tussenvoegsel'] ? $user['tussenvoegsel'] . ' ' : '') . $user['achternaam']); ?></td>
                <td>
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
                <td><?= date('d-m-Y H:i', strtotime($user['datum_aangemaakt'])); ?></td>
                <td>
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
                <td>
                  <button class="btn btn-sm btn-outline-info" title="View Details">
                    <i class="bi bi-eye"></i> View
                  </button>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>

      <!-- Summary Stats -->
      <div class="row g-4 mt-5">
        <div class="col-md-4">
          <div class="stat-card">
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
          <div class="stat-card">
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
          <div class="stat-card">
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
  }

  .stat-card:hover {
    background: rgba(255, 255, 255, 0.1);
    border-color: var(--accent-gold);
    transform: translateY(-5px);
    box-shadow: 0 10px 30px rgba(255, 215, 0, 0.2);
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
</style>

<?php require_once APPROOT . '/views/includes/footer.php'; ?>