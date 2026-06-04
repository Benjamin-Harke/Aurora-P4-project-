<?php require_once APPROOT . '/views/includes/header.php'; ?>

<!-- Hero Section -->
<section class="hero">
  <div class="hero-overlay"></div>
  <div class="container h-100">
    <div class="row h-100 align-items-center">
      <div class="col-lg-8 hero-content">
        <span class="hero-badge">✨ LIVE PERFORMANCES</span>
        <h1>Aurora Theatre</h1>
        <p class="hero-subtitle">Waar verhalen tot leven komen.</p>
        <p class="hero-description">Betreed een wereld vol drama, komedie en verwondering. Beleef voorstellingen van wereldklasse in ons prachtig gerestaureerde theater van 100 jaar oud.</p>
        <div class="hero-buttons">
          <button class="btn btn-primary-custom btn-lg" data-bs-toggle="modal" data-bs-target="#bookingModal">
            <i class="bi bi-ticket-detailed"></i> Book Tickets Nu
          </button>
          <button class="btn btn-outline-custom btn-lg">
            <i class="bi bi-calendar-event"></i> View Shows
          </button>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- Upcoming Shows Section -->
<section class="shows-section" id="shows">
  <div class="container">
    <div class="section-header">
      <h2>Upcoming Shows</h2>
      <p>Don't miss our spectacular lineup</p>
    </div>
    <div class="row g-4">
      <div class="col-md-6 col-lg-4">
        <div class="show-card">
          <div class="show-badge">Featured</div>
          <div class="show-image" style="background: linear-gradient(135deg, var(--accent-bordeaux), var(--secondary-purple));">
            <i class="bi bi-theater-masks"></i>
          </div>
          <div class="show-content">
            <h3>Hamlet: A Timeless Tragedy</h3>
            <p class="show-date"><i class="bi bi-calendar"></i> June 15-25, 2026</p>
            <p class="show-desc">Shakespeares meesterwerk komt tot leven met indrukwekkende acteerprestaties en een prachtig uitgewerkt decor.</p>
            <div class="show-footer">
              <span class="show-time">8:00 PM</span>
              <button class="btn btn-sm btn-primary-custom">Book Nu</button>
            </div>
          </div>
        </div>
      </div>
      <div class="col-md-6 col-lg-4">
        <div class="show-card">
          <div class="show-image" style="background: linear-gradient(135deg, var(--secondary-purple), var(--accent-gold));">
            <i class="bi bi-music-note-beamed"></i>
          </div>
          <div class="show-content">
            <h3>Musical: Echoes of Tomorrow</h3>
            <p class="show-date"><i class="bi bi-calendar"></i> July 5-20, 2026</p>
            <p class="show-desc">Een eigentijdse musical die hoop, liefde en de kracht van menselijke verbondenheid viert.</p>
            <div class="show-footer">
              <span class="show-time">7:30 PM</span>
              <button class="btn btn-sm btn-primary-custom">Book Nu</button>
            </div>
          </div>
        </div>
      </div>
      <div class="col-md-6 col-lg-4">
        <div class="show-card">
          <div class="show-image" style="background: linear-gradient(135deg, var(--accent-gold), var(--accent-bordeaux));">
            <i class="bi bi-mic-fill"></i>
          </div>
          <div class="show-content">
            <h3>Comedy Night Extravaganza</h3>
            <p class="show-date"><i class="bi bi-calendar"></i> August 2-10, 2026</p>
            <p class="show-desc">Een avond vol lachen met bekende cabaretiers die hun beste acts en grappen ten gehore brengen.</p>
            <div class="show-footer">
              <span class="show-time">9:00 PM</span>
              <button class="btn btn-sm btn-primary-custom">Book Nu</button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- About Section -->
<section class="about-section" id="about">
  <div class="container">
    <div class="row align-items-center g-5">
      <div class="col-lg-6">
        <div class="about-image">
          <div class="about-image-placeholder">
            <i class="bi bi-building"></i>
            <p>Historic Theatre</p>
          </div>
        </div>
      </div>
      <div class="col-lg-6">
        <div class="about-content">
          <span class="section-badge">Our Story</span>
          <h2>A Century of Excellence</h2>
          <p>Opgericht in 1924 staat Aurora Theatre bekend als een symbool van artistieke kwaliteit en cultureel erfgoed. Ons prachtig gerestaureerde theater heeft duizenden onvergetelijke voorstellingen gehost, van klassieke toneelstukken tot moderne producties.</p>
          <p>Met ultramoderne faciliteiten, comfortabele zitplaatsen en akoestiek van wereldklasse bieden wij de perfecte omgeving voor iedere voorstelling en elke bezoeker.</p>
          <div class="about-features">
            <div class="about-feature">
              <i class="bi bi-check-circle-fill"></i>
              <span>400+ Seats with Premium Comfort</span>
            </div>
            <div class="about-feature">
              <i class="bi bi-check-circle-fill"></i>
              <span>State-of-the-Art Sound System</span>
            </div>
            <div class="about-feature">
              <i class="bi bi-check-circle-fill"></i>
              <span>Accessible for All Audiences</span>
            </div>
          </div>
          <button class="btn btn-primary-custom btn-lg mt-4">Learn More</button>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- Testimonials Section -->
<section class="testimonials-section">
  <div class="container">
    <div class="section-header">
      <h2>Wat onze bezoekers zeggen</h2>
      <p>Ervaar de magie door hun woorden.</p>
    </div>
    <div class="row g-4">
      <div class="col-md-6 col-lg-4">
        <div class="testimonial-card">
          <div class="stars">
            <i class="bi bi-star-fill"></i>
            <i class="bi bi-star-fill"></i>
            <i class="bi bi-star-fill"></i>
            <i class="bi bi-star-fill"></i>
            <i class="bi bi-star-fill"></i>
          </div>
          <p>"Een absoluut magische avond! De kwaliteit van de voorstelling en de sfeer van het theater zijn ongeëvenaard."</p>
          <div class="testimonial-author">
            <h4>Sarah Mitchell</h4>
            <p>Theaterliefhebber</p>
          </div>
        </div>
      </div>
      <div class="col-md-6 col-lg-4">
        <div class="testimonial-card">
          <div class="stars">
            <i class="bi bi-star-fill"></i>
            <i class="bi bi-star-fill"></i>
            <i class="bi bi-star-fill"></i>
            <i class="bi bi-star-fill"></i>
            <i class="bi bi-star-fill"></i>
          </div>
          <p>"Aurora Theatre is een cultureel juweeltje. De historische architectuur in combinatie met moderne voorzieningen maakt het echt bijzonder."</p>
          <div class="testimonial-author">
            <h4>James Richardson</h4>
            <p>Kunstliefhebber</p>
          </div>
        </div>
      </div>
      <div class="col-md-6 col-lg-4">
        <div class="testimonial-card">
          <div class="stars">
            <i class="bi bi-star-fill"></i>
            <i class="bi bi-star-fill"></i>
            <i class="bi bi-star-fill"></i>
            <i class="bi bi-star-fill"></i>
            <i class="bi bi-star-fill"></i>
          </div>
          <p>"Elk bezoek voelt alsof je terug in de tijd stapt terwijl je geniet van entertainment van wereldklasse. Absoluut een aanrader!"</p>
          <div class="testimonial-author">
            <h4>Elizabeth Garcia</h4>
            <p>Bezoeker met familie</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- CTA Section -->
<section class="cta-section">
  <div class="container">
    <div class="cta-content">
      <h2>Klaar voor een onvergetelijke avond?</h2>
      <p>Sluit je aan bij onze gemeenschap van theaterliefhebbers en mis nooit meer een voorstelling.</p>
      <div class="cta-buttons">
        <button class="btn btn-primary-custom btn-lg" data-bs-toggle="modal" data-bs-target="#bookingModal">
          <i class="bi bi-ticket-detailed"></i> Tickets Reserveren
        </button>
        <button class="btn btn-outline-custom btn-lg">
          <i class="bi bi-envelope"></i> Abonneren
        </button>
      </div>
    </div>
  </div>
</section>

<!-- Login Modal -->
<div class="modal fade" id="loginModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">
          <i class="bi bi-box-arrow-in-right"></i> Inloggen bij Aurora  
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div class="mb-3">
          <label for="email" class="form-label">E-mailadres</label>
          <input type="email" class="form-control" id="email">
        </div>
        <div class="mb-3">
          <label for="password" class="form-label">Wachtwoord</label>
          <input type="password" class="form-control" id="password">
        </div>
        <div class="form-check">
          <input class="form-check-input" type="checkbox" id="remember">
          <label class="form-check-label" for="remember">Onthoud mij</label>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-outline-custom" data-bs-dismiss="modal">Annuleren</button>
        <button type="button" class="btn btn-primary-custom">Inloggen</button>
      </div>
    </div>
  </div>
</div>

<!-- Booking Modal -->
<div class="modal fade" id="bookingModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">
          <i class="bi bi-ticket-detailed"></i> Reserveer je tickets
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <p>Ticketreserveringen zijn binnenkort beschikbaar. Kom snel terug voor meer informatie.</p>
        <div class="alert alert-info">
          <i class="bi bi-info-circle"></i> Abonneer je om een melding te ontvangen zodra de tickets in verkoop gaan!
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-outline-custom" data-bs-dismiss="modal">Sluiten</button>
      </div>
    </div>
  </div>
</div>

<?php require_once APPROOT . '/views/includes/footer.php'; ?>