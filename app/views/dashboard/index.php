<?php
/**
 * Dashboard View
 * @var array $data Contains: title, firstName, lastName, email
 */
require_once APPROOT . '/views/includes/header.php'; ?>

<!-- Dashboard Hero Section -->
<section class="hero" style="min-height: 300px;">
  <div class="hero-overlay"></div>
  <div class="container h-100">
    <div class="row h-100 align-items-center">
      <div class="col-lg-8 hero-content">
        <h1>Welcome back, <?= htmlspecialchars($data['firstName']); ?>!</h1>
        <p class="hero-subtitle">Your Aurora Theatre Dashboard</p>
        <p class="hero-description">Manage your bookings, view your tickets, and stay updated on upcoming shows.</p>
      </div>
    </div>
  </div>
</section>

<!-- Dashboard Content -->
<section class="dashboard-section">
  <div class="container">
    <div class="row g-4">
      <!-- Quick Stats -->
      <div class="col-md-6 col-lg-3">
        <div class="dashboard-card">
          <div class="card-icon">
            <i class="bi bi-ticket-detailed"></i>
          </div>
          <h3>My Bookings</h3>
          <p class="card-stat">0</p>
          <a href="#" class="card-link">View All <i class="bi bi-arrow-right"></i></a>
        </div>
      </div>

      <div class="col-md-6 col-lg-3">
        <div class="dashboard-card">
          <div class="card-icon">
            <i class="bi bi-calendar-event"></i>
          </div>
          <h3>Upcoming Shows</h3>
          <p class="card-stat">3</p>
          <a href="#" class="card-link">Explore <i class="bi bi-arrow-right"></i></a>
        </div>
      </div>

      <div class="col-md-6 col-lg-3">
        <div class="dashboard-card">
          <div class="card-icon">
            <i class="bi bi-person-circle"></i>
          </div>
          <h3>Profile</h3>
          <p class="card-stat"><?= htmlspecialchars($data['firstName']); ?></p>
          <a href="#" class="card-link">Edit <i class="bi bi-arrow-right"></i></a>
        </div>
      </div>

      <div class="col-md-6 col-lg-3">
        <div class="dashboard-card">
          <div class="card-icon">
            <i class="bi bi-gear"></i>
          </div>
          <h3>Settings</h3>
          <p class="card-stat">-</p>
          <a href="#" class="card-link">Configure <i class="bi bi-arrow-right"></i></a>
        </div>
      </div>
    </div>

    <!-- Quick Info -->
    <div class="row g-4 mt-5">
      <div class="col-lg-8">
        <div class="info-card">
          <h3>Account Information</h3>
          <div class="info-grid">
            <div class="info-item">
              <span class="info-label">Email</span>
              <span class="info-value"><?= htmlspecialchars($data['email']); ?></span>
            </div>
            <div class="info-item">
              <span class="info-label">Name</span>
              <span class="info-value"><?= htmlspecialchars($data['firstName'] . ' ' . $data['lastName']); ?></span>
            </div>
          </div>
        </div>
      </div>

      <div class="col-lg-4">
        <div class="info-card">
          <h3>Quick Actions</h3>
          <a href="<?= URLROOT; ?>" class="btn btn-primary-custom btn-sm w-100 mb-2">
            <i class="bi bi-house"></i> Back to Home
          </a>
          <a href="<?= URLROOT; ?>/auth/logout" class="btn btn-outline-custom btn-sm w-100">
            <i class="bi bi-box-arrow-right"></i> Logout
          </a>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- Additional Dashboard Styles -->
<style>
  .dashboard-section {
    padding: 60px 0;
    background: linear-gradient(135deg, rgba(0, 20, 40, 0.7), rgba(0, 30, 60, 0.9));
    min-height: 600px;
  }

  .dashboard-card {
    background: rgba(255, 255, 255, 0.05);
    border: 1px solid rgba(255, 215, 0, 0.2);
    border-radius: 12px;
    padding: 30px;
    text-align: center;
    transition: all 0.3s ease;
    cursor: pointer;
  }

  .dashboard-card:hover {
    background: rgba(255, 255, 255, 0.1);
    border-color: var(--accent-gold);
    transform: translateY(-5px);
    box-shadow: 0 10px 30px rgba(255, 215, 0, 0.2);
  }

  .card-icon {
    font-size: 40px;
    color: var(--accent-gold);
    margin-bottom: 15px;
  }

  .dashboard-card h3 {
    color: var(--accent-gold);
    font-size: 18px;
    margin: 15px 0;
    text-transform: uppercase;
    letter-spacing: 1px;
  }

  .card-stat {
    font-size: 28px;
    font-weight: bold;
    color: white;
    margin: 15px 0;
  }

  .card-link {
    color: var(--accent-gold);
    text-decoration: none;
    font-size: 14px;
    transition: all 0.3s ease;
  }

  .card-link:hover {
    color: white;
  }

  .info-card {
    background: rgba(255, 255, 255, 0.05);
    border: 1px solid rgba(255, 215, 0, 0.2);
    border-radius: 12px;
    padding: 30px;
  }

  .info-card h3 {
    color: var(--accent-gold);
    margin-bottom: 25px;
    text-transform: uppercase;
    letter-spacing: 1px;
  }

  .info-grid {
    display: grid;
    grid-template-columns: 1fr;
    gap: 20px;
  }

  .info-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding-bottom: 15px;
    border-bottom: 1px solid rgba(255, 215, 0, 0.1);
  }

  .info-label {
    color: rgba(255, 255, 255, 0.7);
    font-weight: 500;
  }

  .info-value {
    color: white;
    font-weight: 600;
  }
</style>

<?php require_once APPROOT . '/views/includes/footer.php'; ?>
