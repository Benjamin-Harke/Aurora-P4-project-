<?php
require_once APPROOT . '/models/Melding.php';
?>
<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Aurora Theatre</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
  <link rel="stylesheet" href="<?= URLROOT; ?>/css/style.css">
  <link rel="shortcut icon" href="<?= URLROOT; ?>/img/favicon.ico" type="image/x-icon">
</head>

<body>
  <!-- Navigation Bar -->
  <nav class="navbar navbar-expand-lg navbar-dark sticky-top">
    <div class="container">
      <a class="navbar-brand" href="<?= URLROOT; ?>">
        <i class="bi bi-masks"></i> Aurora Theatre
      </a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse justify-content-center" id="navbarNav">
        <ul class="navbar-nav ms-auto">
          <li class="nav-item">
            <a class="nav-link" href="<?= URLROOT; ?>">Home</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="#shows">Voorstellingen</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="#about">Over ons</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="<?= URLROOT; ?>/contact">Contact</a>
          </li>
          <?php if (isset($_SESSION['accountId'])): ?>
            <li class="nav-item">
              <a class="nav-link" href="<?= URLROOT; ?>/dashboard">
                <i class="bi bi-speedometer2"></i> Dashboard
              </a>
            </li>
            <li class="nav-item">
              <a class="btn btn-outline-custom ms-2" href="<?= URLROOT; ?>/auth/logout">
                <i class="bi bi-box-arrow-right"></i> Logout
              </a>
            </li>
          <?php else: ?>
            <li class="nav-item ms-2">
              <button class="btn btn-primary-custom" data-bs-toggle="modal" data-bs-target="#loginModal">
                <i class="bi bi-box-arrow-in-right"></i> Login
              </button>
            </li>
            <li class="nav-item ms-2">
              <button class="btn btn-primary-custom" data-bs-toggle="modal" data-bs-target="#registerModal">
                <i class="bi bi-person-plus"></i> Register
              </button>
            </li>
          <?php endif; ?>

<!-- Melding Bell -->
<li class="nav-item d-flex align-items-center ms-2">
  <?php
  $__actieveMeldingen = [];

  if (isset($_SESSION['accountId'])) {
      $__meldingModel = new Melding();
      $__bezoeker = $__meldingModel->getBezoekerByGebruikerId($_SESSION['accountId']);

      if ($__bezoeker) {
          $__meldingen = $__meldingModel->getByBezoekerId($__bezoeker->id);

          foreach ($__meldingen as $__melding) {
              $__isActief =
                  $__melding->is_actief == 1 ||
                  $__melding->is_actief === "\x01" ||
                  ord((string)$__melding->is_actief) === 1;

              if ($__isActief) {
                  $__actieveMeldingen[] = $__melding;
              }
          }
      }
  }

  $__aantalMeldingen = count($__actieveMeldingen);
  ?>

  <div class="bell-wrapper" id="bellWrapper">
    <button class="bell-btn <?= $__aantalMeldingen > 0 ? 'bell-btn-active' : ''; ?>" id="bellBtn" type="button">
      <i class="bi <?= $__aantalMeldingen > 0 ? 'bi-bell-fill' : 'bi-bell'; ?>"></i>

      <?php if ($__aantalMeldingen > 0): ?>
        <span class="bell-badge"><?= $__aantalMeldingen; ?></span>
      <?php endif; ?>
    </button>

    <div class="bell-dropdown" id="bellDropdown">
      <div class="bell-dropdown-header">
        <span><i class="bi bi-bell me-1"></i> Meldingen</span>
      </div>

      <div class="bell-dropdown-body">
        <?php if (!isset($_SESSION['accountId'])): ?>
          <p class="bell-dropdown-text">Log eerst in om je meldingen te bekijken.</p>

        <?php elseif ($__aantalMeldingen === 0): ?>
          <p class="bell-dropdown-text">Je hebt geen nieuwe meldingen.</p>

        <?php else: ?>
          <?php foreach ($__actieveMeldingen as $__melding): ?>
            <div class="bell-message">
              <strong><?= htmlspecialchars(ucfirst($__melding->type)); ?></strong>
              <p><?= htmlspecialchars($__melding->bericht); ?></p>

              <a href="<?= URLROOT; ?>/meldingen/gelezen/<?= $__melding->id; ?>">
                Markeer als gelezen
              </a>
            </div>
          <?php endforeach; ?>
        <?php endif; ?>
      </div>

      <?php if (isset($_SESSION['accountId'])): ?>
        <div class="bell-dropdown-footer">
          <a href="<?= URLROOT ?>/meldingen" class="bell-overzicht-btn">
            Overzicht <i class="bi bi-arrow-right ms-1"></i>
          </a>
        </div>
      <?php endif; ?>
    </div>
  </div>
</li>
<!-- /Melding Bell -->

        </ul>
      </div>
    </div>
  </nav>

  <!-- Login Modal -->
  <div class="modal fade" id="loginModal" tabindex="-1">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header border-bottom-0">
          <h5 class="modal-title">Login to Aurora Theatre</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
        </div>
        <form method="POST" action="<?= URLROOT; ?>/auth/login">
          <div class="modal-body">
            <div class="mb-3">
              <label for="loginEmail" class="form-label">Email</label>
              <input type="email" class="form-control" id="loginEmail" name="login_email" required>
            </div>
            <div class="mb-3">
              <label for="loginPassword" class="form-label">Password</label>
              <input type="password" class="form-control" id="loginPassword" name="login_password" required>
            </div>
          </div>
          <div class="modal-footer border-top-0">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-primary-custom">Login</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Register Modal -->
  <div class="modal fade" id="registerModal" tabindex="-1">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header border-bottom-0">
          <h5 class="modal-title">Create Your Account</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
        </div>
        <form method="POST" action="<?= URLROOT; ?>/auth/register">
          <div class="modal-body">
            <div class="mb-3">
              <label for="registerFirstname" class="form-label">First Name</label>
              <input type="text" class="form-control" id="registerFirstname" name="register_firstname" required>
            </div>
            <div class="mb-3">
              <label for="registerLastname" class="form-label">Last Name</label>
              <input type="text" class="form-control" id="registerLastname" name="register_lastname" required>
            </div>
            <div class="mb-3">
              <label for="registerEmail" class="form-label">Email</label>
              <input type="email" class="form-control" id="registerEmail" name="register_email" required>
            </div>
            <div class="mb-3">
              <label for="registerPassword" class="form-label">Password</label>
              <input type="password" class="form-control" id="registerPassword" name="register_password" required
                minlength="6">
              <small class="form-text text-muted">At least 6 characters</small>
            </div>
            <div class="mb-3">
              <label for="registerPasswordConfirm" class="form-label">Confirm Password</label>
              <input type="password" class="form-control" id="registerPasswordConfirm" name="register_password_confirm"
                required minlength="6">
            </div>
          </div>
          <div class="modal-footer border-top-0">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-primary-custom">Register</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Error/Success Alert -->
  <?php if (isset($_SESSION['error'])): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert" style="margin-top: 70px;">
      <?= htmlspecialchars($_SESSION['error']); ?>
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php unset($_SESSION['error']); ?>
  <?php endif; ?>
  <?php if (isset($_SESSION['success'])): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert" style="margin-top: 70px;">
      <?= htmlspecialchars($_SESSION['success']); ?>
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php unset($_SESSION['success']); ?>
  <?php endif; ?>

  <!-- Auto-open Login Modal -->
  <?php if (isset($_SESSION['showLoginModal']) && $_SESSION['showLoginModal']): ?>
    <script>
      document.addEventListener('DOMContentLoaded', function () {
        const loginModal = new bootstrap.Modal(document.getElementById('loginModal'));
        loginModal.show();
      });
    </script>
    <?php unset($_SESSION['showLoginModal']); ?>
  <?php endif; ?>

  <!-- Bell Dropdown Toggle -->
  <script>
    document.addEventListener('DOMContentLoaded', function () {
      const bellBtn = document.getElementById('bellBtn');
      const bellDropdown = document.getElementById('bellDropdown');

      if (bellBtn && bellDropdown) {
        bellBtn.addEventListener('click', function (e) {
          e.stopPropagation();
          bellDropdown.classList.toggle('open');
        });

        document.addEventListener('click', function (e) {
          if (!bellBtn.contains(e.target) && !bellDropdown.contains(e.target)) {
            bellDropdown.classList.remove('open');
          }
        });
      }
    });
  </script>