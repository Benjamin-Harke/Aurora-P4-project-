<?php require_once APPROOT . '/views/includes/header.php'; ?>

<!-- Hero Section -->
<section class="hero">
  <div class="hero-overlay"></div>
  <div class="container h-100">
    <div class="row h-100 align-items-center">
      <div class="col-lg-8 hero-content">
        <span class="hero-badge">✨ LIVE PERFORMANCES</span>
        <h1>Aurora Theatre</h1>
        <p class="hero-subtitle">Where Stories Come to Life</p>
        <p class="hero-description">Step into a world of drama, comedy, and wonder. Experience world-class performances in our beautifully restored 100-year-old theatre.</p>
        <div class="hero-buttons">
          <button class="btn btn-primary-custom btn-lg" data-bs-toggle="modal" data-bs-target="#bookingModal">
            <i class="bi bi-ticket-detailed"></i> Book Tickets Now
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
            <p class="show-desc">Shakespeare's masterpiece brought to life with stunning performances and elaborate set design.</p>
            <div class="show-footer">
              <span class="show-time">8:00 PM</span>
              <button class="btn btn-sm btn-primary-custom">Book Now</button>
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
            <p class="show-desc">A contemporary musical celebrating hope, love, and the power of human connection.</p>
            <div class="show-footer">
              <span class="show-time">7:30 PM</span>
              <button class="btn btn-sm btn-primary-custom">Book Now</button>
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
            <p class="show-desc">An evening of laughter with renowned comedians performing their best routines.</p>
            <div class="show-footer">
              <span class="show-time">9:00 PM</span>
              <button class="btn btn-sm btn-primary-custom">Book Now</button>
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
          <p>Founded in 1924, Aurora Theatre stands as a beacon of artistic excellence and cultural heritage. Our beautifully restored venue has hosted thousands of unforgettable performances, from classical theatre to contemporary productions.</p>
          <p>With state-of-the-art facilities, premium seating, and world-class acoustics, we provide the perfect setting for every type of performance and audience.</p>
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
      <h2>What Our Patrons Say</h2>
      <p>Experience the magic through their words</p>
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
          <p>"An absolutely magical evening! The production quality and venue atmosphere are unmatched."</p>
          <div class="testimonial-author">
            <h4>Sarah Mitchell</h4>
            <p>Theatre Enthusiast</p>
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
          <p>"Aurora Theatre is a cultural gem. The historic architecture combined with modern amenities makes it special."</p>
          <div class="testimonial-author">
            <h4>James Richardson</h4>
            <p>Arts Patron</p>
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
          <p>"Every visit feels like stepping back in time while enjoying world-class entertainment. Highly recommended!"</p>
          <div class="testimonial-author">
            <h4>Elizabeth Garcia</h4>
            <p>Family Visitor</p>
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
      <h2>Ready for an Unforgettable Evening?</h2>
      <p>Join our community of theatre lovers and never miss a show</p>
      <div class="cta-buttons">
        <button class="btn btn-primary-custom btn-lg" data-bs-toggle="modal" data-bs-target="#bookingModal">
          <i class="bi bi-ticket-detailed"></i> Book Tickets
        </button>
        <button class="btn btn-outline-custom btn-lg">
          <i class="bi bi-envelope"></i> Subscribe
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
          <i class="bi bi-box-arrow-in-right"></i> Login to Aurora
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div class="mb-3">
          <label for="email" class="form-label">Email Address</label>
          <input type="email" class="form-control" id="email">
        </div>
        <div class="mb-3">
          <label for="password" class="form-label">Password</label>
          <input type="password" class="form-control" id="password">
        </div>
        <div class="form-check">
          <input class="form-check-input" type="checkbox" id="remember">
          <label class="form-check-label" for="remember">Remember me</label>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-outline-custom" data-bs-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-primary-custom">Login</button>
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
          <i class="bi bi-ticket-detailed"></i> Book Your Tickets
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <p>Ticket booking will be available soon! Check back shortly.</p>
        <div class="alert alert-info">
          <i class="bi bi-info-circle"></i> Subscribe to be notified when tickets go on sale!
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-outline-custom" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<?php require_once APPROOT . '/views/includes/footer.php'; ?>