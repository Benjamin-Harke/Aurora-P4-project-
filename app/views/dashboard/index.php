<?php
/**
 * Dashboard View
 * @var array $data Contains: title, firstName, lastName, email
 */
require_once APPROOT . '/views/includes/header.php';
require_once APPROOT . '/views/includes/messages.php'; ?>

<!-- Dashboard Hero Section -->
<section class="hero" style="min-height: 300px;">
  <div class="hero-overlay"></div>
  <div class="container h-100">
    <div class="row h-100 align-items-center">
      <div class="col-lg-8 hero-content">
        <h1>Welcome back, <?= htmlspecialchars($data['firstName']); ?>!</h1>
        <p class="hero-subtitle">
          <?php echo strtolower($data['role']) === 'admin' ? 'Your Aurora Theatre Admin Dashboard' : 'Your Aurora Theatre Booking Dashboard'; ?>
        </p>
        <p class="hero-description">
          <?php echo strtolower($data['role']) === 'admin' ? 'Manage bookings, scan tickets, and administer shows and staff.' : 'Browse available shows, manage your bookings, and view your tickets.'; ?>
        </p>
      </div>
    </div>
  </div>
</section>

<!-- Dashboard Content -->
<section class="dashboard-section">
  <div class="container">
    <!-- Admin Navigation Grid -->
    <?php if (strtolower($data['role']) === 'admin'): ?>
      <div class="row g-4 mb-5">
        <div class="col-md-6 col-lg-4">
          <a href="<?= URLROOT; ?>/admintickets/dashboard" class="dashboard-nav-card">
            <div class="card-icon">
              <i class="bi bi-graph-up"></i>
            </div>
            <h3>Ticket Analytics</h3>
            <p>View inventory & revenue</p>
            <span class="card-link">Go to Analytics <i class="bi bi-arrow-right"></i></span>
          </a>
        </div>

        <div class="col-md-6 col-lg-4">
          <a href="<?= URLROOT; ?>/ticketscanning" class="dashboard-nav-card">
            <div class="card-icon">
              <i class="bi bi-qr-code"></i>
            </div>
            <h3>Scan Tickets</h3>
            <p>Entry control & validation</p>
            <span class="card-link">Open Scanner <i class="bi bi-arrow-right"></i></span>
          </a>
        </div>

        <div class="col-md-6 col-lg-4">
          <a href="<?= URLROOT; ?>/voorstellingen" class="dashboard-nav-card">
            <div class="card-icon">
              <i class="bi bi-calendar-event"></i>
            </div>
            <h3>Voorstellingen</h3>
            <p>Manage all shows and performances</p>
            <span class="card-link">Go to Voorstellingen <i class="bi bi-arrow-right"></i></span>
          </a>
        </div>

        <div class="col-md-6 col-lg-4">
          <a href="<?= URLROOT; ?>/medewerkers" class="dashboard-nav-card">
            <div class="card-icon">
              <i class="bi bi-people"></i>
            </div>
            <h3>Medewerkers</h3>
            <p>Manage staff and employees</p>
            <span class="card-link">Go to Medewerkers <i class="bi bi-arrow-right"></i></span>
          </a>
        </div>

        <div class="col-md-6 col-lg-4">
          <a href="<?= URLROOT; ?>/accounts" class="dashboard-nav-card">
            <div class="card-icon">
              <i class="bi bi-person-vcard"></i>
            </div>
            <h3>Accounts</h3>
            <p>View all registered accounts</p>
            <span class="card-link">Go to Accounts <i class="bi bi-arrow-right"></i></span>
          </a>
        </div>
      </div>
    <?php else: ?>
      <!-- User Navigation Grid -->
      <div class="row g-4 mb-5">
        <div class="col-md-6 col-lg-4">
          <a href="<?= URLROOT; ?>/publictickets" class="dashboard-nav-card">
            <div class="card-icon">
              <i class="bi bi-ticket-detailed"></i>
            </div>
            <h3>Available Tickets</h3>
            <p>Browse and book theatre performances</p>
            <span class="card-link">Browse Tickets <i class="bi bi-arrow-right"></i></span>
          </a>
        </div>

        <div class="col-md-6 col-lg-4">
          <a href="<?= URLROOT; ?>/usertickets/mytickets" class="dashboard-nav-card">
            <div class="card-icon">
              <i class="bi bi-bookmark-check"></i>
            </div>
            <h3>My Bookings</h3>
            <p>View your tickets</p>
            <span class="card-link">View Bookings <i class="bi bi-arrow-right"></i></span>
          </a>
        </div>

        <div class="col-md-6 col-lg-4">
          <a href="<?= URLROOT; ?>/voorstellingen" class="dashboard-nav-card">
            <div class="card-icon">
              <i class="bi bi-calendar-event"></i>
            </div>
            <h3>Upcoming Shows</h3>
            <p>See what's coming</p>
            <span class="card-link">View All <i class="bi bi-arrow-right"></i></span>
          </a>
        </div>
      </div>
    <?php endif; ?>

    <!-- Quick Stats Row -->
    <?php if (strtolower($data['role']) === 'admin'): ?>
      <div class="row g-4 mb-5">
        <div class="col-md-6 col-lg-3">
          <div class="dashboard-card">
            <div class="card-icon">
              <i class="bi bi-ticket-detailed"></i>
            </div>
            <h3>Total Tickets</h3>
            <p class="card-stat">-</p>
          </div>
        </div>

        <div class="col-md-6 col-lg-3">
          <div class="dashboard-card">
            <div class="card-icon">
              <i class="bi bi-calendar-event"></i>
            </div>
            <h3>Performances</h3>
            <p class="card-stat">-</p>
          </div>
        </div>

        <div class="col-md-6 col-lg-3">
          <div class="dashboard-card">
            <div class="card-icon">
              <i class="bi bi-person-circle"></i>
            </div>
            <h3>Profile</h3>
            <p class="card-stat"><?= htmlspecialchars($data['firstName']); ?></p>
          </div>
        </div>

        <div class="col-md-6 col-lg-3">
          <div class="dashboard-card">
            <div class="card-icon">
              <i class="bi bi-gear"></i>
            </div>
            <h3>Settings</h3>
            <p class="card-stat">-</p>
          </div>
        </div>
      </div>
    <?php else: ?>
      <!-- User Quick Stats -->
      <div class="row g-4 mb-5">
        <div class="col-md-6 col-lg-3">
          <a href="<?= URLROOT; ?>/usertickets/mytickets" class="dashboard-card">
            <div class="card-icon">
              <i class="bi bi-ticket-detailed"></i>
            </div>
            <h3>My Bookings</h3>
            <p class="card-stat">View</p>
          </a>
        </div>

        <div class="col-md-6 col-lg-3">
          <a href="<?= URLROOT; ?>/publictickets" class="dashboard-card">
            <div class="card-icon">
              <i class="bi bi-calendar-event"></i>
            </div>
            <h3>Available Shows</h3>
            <p class="card-stat">Browse</p>
          </a>
        </div>

        <div class="col-md-6 col-lg-3">
          <div class="dashboard-card">
            <div class="card-icon">
              <i class="bi bi-person-circle"></i>
            </div>
            <h3>Profile</h3>
            <p class="card-stat"><?= htmlspecialchars($data['firstName']); ?></p>
          </div>
        </div>

        <div class="col-md-6 col-lg-3">
          <div class="dashboard-card">
            <div class="card-icon">
              <i class="bi bi-gear"></i>
            </div>
            <h3>Settings</h3>
            <p class="card-stat">-</p>
          </div>
        </div>
      </div>

      <div class="row g-4 mb-5">
        <div class="col-md-6 col-lg-3">
          <a href="<?= URLROOT; ?>/contact/overzicht" class="dashboard-card">
            <div class="card-icon">
              <i class="bi bi-chat-left-text"></i>
            </div>

            <h3>Ontvangen Feedback</h3>
            <p class="card-stat">Bekijk</p>
          </a>
        </div>
      </div>
      
    <?php endif; ?>

    <!-- Quick Info -->
    <div class="row g-4">
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

  .dashboard-nav-card {
    display: block;
    background: rgba(255, 255, 255, 0.05);
    border: 2px solid var(--primary-teal);
    border-radius: 12px;
    padding: 30px;
    text-align: center;
    transition: all 0.3s ease;
    cursor: pointer;
    text-decoration: none;
    color: inherit;
    height: 100%;
  }

  .dashboard-nav-card:hover {
    background: rgba(255, 255, 255, 0.1);
    border-color: var(--accent-gold);
    transform: translateY(-5px);
    box-shadow: 0 10px 30px rgba(255, 215, 0, 0.2);
  }

  .dashboard-nav-card .card-icon {
    font-size: 50px;
    color: var(--primary-teal);
    margin-bottom: 15px;
    transition: color 0.3s ease;
  }

  .dashboard-nav-card:hover .card-icon {
    color: var(--accent-gold);
  }

  .dashboard-nav-card h3 {
    color: white;
    font-size: 20px;
    margin: 15px 0;
    text-transform: uppercase;
    letter-spacing: 1px;
  }

  .dashboard-nav-card p {
    color: rgba(255, 255, 255, 0.7);
    margin: 10px 0 20px 0;
    font-size: 14px;
  }

  .dashboard-nav-card .card-link {
    color: var(--accent-gold);
    text-decoration: none;
    font-size: 14px;
    transition: all 0.3s ease;
    display: inline-block;
  }

  .dashboard-nav-card:hover .card-link {
    color: white;
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