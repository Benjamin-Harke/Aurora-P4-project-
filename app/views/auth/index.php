<?php
/**
 * Auth View
 * @var array $data Contains: title, email_err, password_err, firstName_err, lastName_err
 */
require_once APPROOT . '/views/includes/header.php'; ?>

<!-- Auth Section -->
<section class="auth-section">
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-lg-8">
        <div class="auth-container">
          <h1 class="auth-title">Aurora Theatre</h1>
          <p class="auth-subtitle">Login or Create an Account</p>
          
          <div class="row g-4">
            <!-- Login Column -->
            <div class="col-md-6">
              <div class="auth-card">
                <h2>Existing Users</h2>
                <form method="POST" action="<?= URLROOT; ?>/auth/login">
                  <div class="mb-3">
                    <label for="loginEmail" class="form-label">Email</label>
                    <input type="email" class="form-control" id="loginEmail" name="login_email" required>
                  </div>
                  <div class="mb-3">
                    <label for="loginPassword" class="form-label">Password</label>
                    <input type="password" class="form-control" id="loginPassword" name="login_password" required>
                  </div>
                  <button type="submit" class="btn btn-primary-custom w-100">
                    <i class="bi bi-box-arrow-in-right"></i> Login
                  </button>
                </form>
              </div>
            </div>

            <!-- Register Column -->
            <div class="col-md-6">
              <div class="auth-card">
                <h2>New Users</h2>
                <form method="POST" action="<?= URLROOT; ?>/auth/register">
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
                    <input type="password" class="form-control" id="registerPassword" name="register_password" required minlength="6">
                    <small class="form-text text-muted">At least 6 characters</small>
                  </div>
                  <div class="mb-3">
                    <label for="registerPasswordConfirm" class="form-label">Confirm Password</label>
                    <input type="password" class="form-control" id="registerPasswordConfirm" name="register_password_confirm" required minlength="6">
                  </div>
                  <button type="submit" class="btn btn-primary-custom w-100">
                    <i class="bi bi-person-plus"></i> Register
                  </button>
                </form>
              </div>
            </div>
          </div>

          <div class="auth-footer">
            <p>By logging in or registering, you agree to our <a href="#">Terms of Service</a></p>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- Auth Styles -->
<style>
  .auth-section {
    padding: 80px 20px;
    background: linear-gradient(135deg, rgba(0, 20, 40, 0.7), rgba(0, 30, 60, 0.9));
    min-height: calc(100vh - 200px);
    display: flex;
    align-items: center;
  }

  .auth-container {
    text-align: center;
  }

  .auth-title {
    color: var(--accent-gold);
    font-size: 3rem;
    margin-bottom: 10px;
    text-transform: uppercase;
    letter-spacing: 3px;
    text-shadow: 0 4px 15px rgba(255, 215, 0, 0.3);
  }

  .auth-subtitle {
    color: rgba(255, 255, 255, 0.7);
    font-size: 1.2rem;
    margin-bottom: 50px;
  }

  .auth-card {
    background: rgba(255, 255, 255, 0.05);
    border: 2px solid var(--primary-teal);
    border-radius: 12px;
    padding: 40px;
    backdrop-filter: blur(20px);
  }

  .auth-card h2 {
    color: var(--accent-gold);
    margin-bottom: 30px;
    font-size: 1.5rem;
    text-transform: uppercase;
    letter-spacing: 2px;
  }

  .auth-card .form-label {
    color: var(--text-light);
    font-weight: 600;
    letter-spacing: 0.5px;
  }

  .auth-card .form-control {
    background-color: rgba(0, 217, 255, 0.08);
    border-color: var(--primary-teal);
    color: white;
    transition: all 0.3s ease;
    margin-bottom: 10px;
  }

  .auth-card .form-control:focus {
    background-color: rgba(0, 217, 255, 0.12);
    border-color: var(--accent-magenta);
    box-shadow: 0 0 15px rgba(0, 217, 255, 0.3);
    color: white;
  }

  .auth-card .form-control::placeholder {
    color: var(--text-muted);
  }

  .form-text {
    display: block;
    margin-top: 5px;
    margin-bottom: 15px;
  }

  .auth-footer {
    margin-top: 40px;
    color: rgba(255, 255, 255, 0.6);
    font-size: 0.9rem;
  }

  .auth-footer a {
    color: var(--accent-gold);
    text-decoration: none;
    transition: color 0.3s ease;
  }

  .auth-footer a:hover {
    color: white;
    text-decoration: underline;
  }

  @media (max-width: 768px) {
    .auth-title {
      font-size: 2rem;
      letter-spacing: 2px;
    }

    .auth-subtitle {
      font-size: 1rem;
    }

    .auth-card {
      padding: 25px;
    }

    .auth-card h2 {
      font-size: 1.2rem;
    }
  }
</style>

<?php require_once APPROOT . '/views/includes/footer.php'; ?>
