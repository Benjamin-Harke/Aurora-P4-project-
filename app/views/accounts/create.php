<?php
/**
 * Create Account View
 * @var array $data Contains: title, voornaam, tussenvoegsel, achternaam, email, gebruikersnaam, rol, etc.
 */
require_once APPROOT . '/views/includes/header.php'; ?>

<section class="create-section">
  <div class="container">
    <!-- Header -->
    <div class="create-header mb-5">
      <h1><?= htmlspecialchars($data['title']); ?></h1>
      <p class="subtitle">Voeg een nieuwe medewerker of bezoeker toe aan de theaterwebsite</p>
    </div>

    <!-- Back Button -->
    <div class="mb-4">
      <a href="<?= URLROOT; ?>/accounts" class="btn btn-outline-custom">
        <i class="bi bi-arrow-left"></i> Terug naar overzicht
      </a>
    </div>

    <!-- Creation Form -->
    <div class="form-container">
      <form method="POST" action="<?= URLROOT; ?>/accounts/create">
        <div class="row">
          <!-- First Name -->
          <div class="col-md-5 mb-3">
            <label for="voornaam" class="form-label">Voornaam <span class="text-danger">*</span></label>
            <input type="text" name="voornaam" id="voornaam" class="form-control <?= (!empty($data['voornaam_err'])) ? 'is-invalid' : ''; ?>" value="<?= htmlspecialchars($data['voornaam']); ?>" placeholder="Bijv. John">
            <div class="invalid-feedback"><?= $data['voornaam_err']; ?></div>
          </div>

          <!-- Tussenvoegsel -->
          <div class="col-md-2 mb-3">
            <label for="tussenvoegsel" class="form-label">Tussenvoegsel</label>
            <input type="text" name="tussenvoegsel" id="tussenvoegsel" class="form-control" value="<?= htmlspecialchars($data['tussenvoegsel']); ?>" placeholder="Bijv. van de">
          </div>

          <!-- Last Name -->
          <div class="col-md-5 mb-3">
            <label for="achternaam" class="form-label">Achternaam <span class="text-danger">*</span></label>
            <input type="text" name="achternaam" id="achternaam" class="form-control <?= (!empty($data['achternaam_err'])) ? 'is-invalid' : ''; ?>" value="<?= htmlspecialchars($data['achternaam']); ?>" placeholder="Bijv. Doe">
            <div class="invalid-feedback"><?= $data['achternaam_err']; ?></div>
          </div>
        </div>

        <div class="row">
          <!-- Username -->
          <div class="col-md-6 mb-3">
            <label for="gebruikersnaam" class="form-label">Gebruikersnaam <span class="text-danger">*</span></label>
            <input type="text" name="gebruikersnaam" id="gebruikersnaam" class="form-control <?= (!empty($data['gebruikersnaam_err'])) ? 'is-invalid' : ''; ?>" value="<?= htmlspecialchars($data['gebruikersnaam']); ?>" placeholder="Gebruikersnaam voor inloggen">
            <div class="invalid-feedback"><?= $data['gebruikersnaam_err']; ?></div>
          </div>

          <!-- Email -->
          <div class="col-md-6 mb-3">
            <label for="email" class="form-label">E-mailadres <span class="text-danger">*</span></label>
            <input type="email" name="email" id="email" class="form-control <?= (!empty($data['email_err'])) ? 'is-invalid' : ''; ?>" value="<?= htmlspecialchars($data['email']); ?>" placeholder="e-mailadres@domein.com">
            <div class="invalid-feedback"><?= $data['email_err']; ?></div>
          </div>
        </div>

        <div class="row">
          <!-- Role -->
          <div class="col-md-6 mb-3">
            <label for="rol" class="form-label">Rol <span class="text-danger">*</span></label>
            <select name="rol" id="rol" class="form-select <?= (!empty($data['rol_err'])) ? 'is-invalid' : ''; ?>">
              <option value="" disabled <?= empty($data['rol']) ? 'selected' : ''; ?>>Selecteer een rol...</option>
              <option value="Admin" <?= $data['rol'] === 'Admin' ? 'selected' : ''; ?>>Admin</option>
              <option value="Medewerker" <?= $data['rol'] === 'Medewerker' ? 'selected' : ''; ?>>Medewerker</option>
              <option value="Receptie" <?= $data['rol'] === 'Receptie' ? 'selected' : ''; ?>>Receptie</option>
              <option value="Bezoeker" <?= $data['rol'] === 'Bezoeker' ? 'selected' : ''; ?>>Bezoeker</option>
            </select>
            <div class="invalid-feedback"><?= $data['rol_err']; ?></div>
          </div>

          <!-- Mobile Number (Optional) -->
          <div class="col-md-6 mb-3">
            <label for="mobiel" class="form-label">Mobiel telefoonnummer</label>
            <input type="text" name="mobiel" id="mobiel" class="form-control" value="<?= htmlspecialchars($data['mobiel'] ?? ''); ?>" placeholder="Bijv. 0612345678">
          </div>
        </div>

        <div class="row">
          <!-- Password -->
          <div class="col-md-6 mb-3">
            <label for="wachtwoord" class="form-label">Wachtwoord <span class="text-danger">*</span></label>
            <input type="password" name="wachtwoord" id="wachtwoord" class="form-control <?= (!empty($data['wachtwoord_err'])) ? 'is-invalid' : ''; ?>" placeholder="Minimaal 6 tekens">
            <div class="invalid-feedback"><?= $data['wachtwoord_err']; ?></div>
          </div>

          <!-- Password Confirm -->
          <div class="col-md-6 mb-3">
            <label for="wachtwoord_bevestigen" class="form-label">Wachtwoord Bevestigen <span class="text-danger">*</span></label>
            <input type="password" name="wachtwoord_bevestigen" id="wachtwoord_bevestigen" class="form-control <?= (!empty($data['wachtwoord_bevestigen_err'])) ? 'is-invalid' : ''; ?>" placeholder="Herhaal wachtwoord">
            <div class="invalid-feedback"><?= $data['wachtwoord_bevestigen_err']; ?></div>
          </div>
        </div>

        <div class="mt-4 text-end">
          <button type="submit" class="btn btn-primary-custom">
            <i class="bi bi-save"></i> Account Opslaan
          </button>
        </div>
      </form>
    </div>
  </div>
</section>

<style>
  .create-section {
    padding: 60px 0;
    background: linear-gradient(135deg, rgba(0, 20, 40, 0.7), rgba(0, 30, 60, 0.9));
    min-height: 600px;
  }

  .create-header {
    text-align: center;
    margin-bottom: 40px;
  }

  .create-header h1 {
    font-size: 2.5rem;
    color: var(--primary-teal);
    margin-bottom: 10px;
    text-transform: uppercase;
    letter-spacing: 2px;
  }

  .create-header .subtitle {
    font-size: 1.1rem;
    color: rgba(255, 255, 255, 0.7);
  }

  .form-container {
    background: rgba(255, 255, 255, 0.05);
    border: 1px solid rgba(255, 215, 0, 0.2);
    border-radius: 12px;
    padding: 40px;
    box-shadow: 0 8px 32px rgba(0, 217, 255, 0.1);
  }

  .form-label {
    color: var(--text-light);
    font-weight: 600;
    letter-spacing: 0.5px;
  }

  .form-control, .form-select {
    background-color: rgba(0, 217, 255, 0.08);
    border-color: var(--primary-teal);
    color: white;
    transition: all 0.3s ease;
  }

  .form-control:focus, .form-select:focus {
    background-color: rgba(0, 217, 255, 0.12);
    border-color: var(--accent-magenta);
    box-shadow: 0 0 15px rgba(0, 217, 255, 0.3);
    color: white;
  }

  .form-control::placeholder {
    color: var(--text-muted);
  }

  .form-control.is-invalid, .form-select.is-invalid {
    border-color: var(--accent-magenta) !important;
    background-image: none !important;
  }

  .invalid-feedback {
    color: var(--accent-magenta-light) !important;
    font-weight: 500;
  }
</style>

<?php require_once APPROOT . '/views/includes/footer.php'; ?>
