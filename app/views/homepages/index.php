<?php require_once APPROOT . '/views/includes/header.php'; ?>

<!-- Hier is de Hero sectie -->
<section class="hero">
  <div class="container">
    <div class="hero-content">
      <h1>Aurora Theatre</h1>
      <p class="hero-subtitle">Waar Elk Optreden Een Beleving Wordt</p>
      <p class="hero-description">
        Geniet van bijzondere voorstellingen, indrukwekkende producties en een sfeer die u nergens anders vindt.
      </p>
      <button class="btn btn-light btn-lg" data-bs-toggle="modal" data-bs-target="#bookingModal">
        <i class="bi bi-ticket-detailed"></i> Book Tickets Nu
      </button>
    </div>
  </div>
</section>

<!-- De Reviews sectie onder de hero-->
<section class="reviews-section">
  <div class="container">
    <div class="section-header">
      <h2>Onze Reviews</h2>
      <p>Experience the magic through their words</p>
    </div>

    <div class="row g-4">

      <!-- Review 1 -->
      <div class="col-md-4">
        <div class="review-card">
          <div class="stars">
            <i class="bi bi-star-fill"></i>
            <i class="bi bi-star-fill"></i>
            <i class="bi bi-star-fill"></i>
            <i class="bi bi-star-fill"></i>
          </div>
          <p>"Een prachtige avond van begin tot eind. De voorstelling was indrukwekkend en de sfeer in het theater maakte de ervaring compleet."</p>
          <h5>Emma de Vries</h5>
          <span>REGELMATIGE BEZOEKER</span>
        </div>
      </div>

      <!-- Review 2 -->
      <div class="col-md-4">
        <div class="review-card">
          <div class="stars">
            <i class="bi bi-star-fill"></i>
            <i class="bi bi-star-fill"></i>
            <i class="bi bi-star-fill"></i>
            <i class="bi bi-star-fill"></i>
            <i class="bi bi-star-fill"></i>
          </div>
          <p>"Het theater combineert klassieke charme met moderne faciliteiten. Een van de mooiste locaties die ik heb bezocht."</p>
          <h5>Mark Jansen</h5>
          <span>THEATERLIEFHEBBER</span>
        </div>
      </div>

      <!-- Review 3 -->
      <div class="col-md-4">
        <div class="review-card">
          <div class="stars">
            <i class="bi bi-star-fill"></i>
            <i class="bi bi-star-fill"></i>
            <i class="bi bi-star-fill"></i>
            <i class="bi bi-star-fill"></i>
          </div>
          <p>"Vriendelijke medewerkers, comfortabele zitplaatsen en een geweldige voorstelling. Wij komen zeker terug.</p>
          <h5>Sophie Bakker</h5>
          <span>FAMILIEBEZOEKER</span>
        </div>
      </div>

    </div>
  </div>
</section>

<!-- Login modal -->
<div class="modal fade" id="loginModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><i class="bi bi-box-arrow-in-right"></i> Login</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div class="mb-3">
          <label class="form-label">E-mailadres</label>
          <input type="email" class="form-control" placeholder="jouw@email.nl">
        </div>
        <div class="mb-3">
          <label class="form-label">Wachtwoord</label>
          <input type="password" class="form-control" placeholder="••••••••">
        </div>
        <div class="form-check">
          <input class="form-check-input" type="checkbox" id="remember">
          <label class="form-check-label" for="remember">Onthoud mij</label>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuleren</button>
        <button type="button" class="btn btn-light text-dark">Inloggen</button>
      </div>
    </div>
  </div>
</div>

<!-- Register modal -->
<div class="modal fade" id="registerModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><i class="bi bi-person-plus"></i> Registreren</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div class="mb-3">
          <label class="form-label">Naam</label>
          <input type="text" class="form-control" placeholder="Jouw naam">
        </div>
        <div class="mb-3">
          <label class="form-label">E-mailadres</label>
          <input type="email" class="form-control" placeholder="jouw@email.nl">
        </div>
        <div class="mb-3">
          <label class="form-label">Wachtwoord</label>
          <input type="password" class="form-control" placeholder="••••••••">
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuleren</button>
        <button type="button" class="btn btn-light text-dark">Registreren</button>
      </div>
    </div>
  </div>
</div>

<!-- Booking modal -->
<div class="modal fade" id="bookingModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><i class="bi bi-ticket-detailed"></i> Tickets Boeken</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <p>Tickets zijn binnenkort beschikbaar!</p>
        <div class="alert alert-info">
          <i class="bi bi-info-circle"></i> Schrijf je in om een melding te ontvangen.
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Sluiten</button>
      </div>
    </div>
  </div>
</div>

<?php require_once APPROOT . '/views/includes/footer.php'; ?> 