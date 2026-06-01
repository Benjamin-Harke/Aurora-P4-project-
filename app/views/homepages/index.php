<?php require_once APPROOT . '/views/includes/header.php'; ?>

<!-- Hero Section -->
<section class="hero">
  <div class="hero-overlay">
    <img src="https://chatgpt.com/backend-api/estuary/content?id=file_00000000adc871f4a085791aa39dbae9&ts=494531&p=fsns&cid=1&sig=92598f867071b80223918089563d0e5933331de5198d05d37e006d59a4d09dfe&v=0" alt="">
  </div>
  <div class="container h-100">
    <div class="row h-100 align-items-center">
      <div class="col-lg-8 hero-content">
        <h1>Aurora Theatre</h1>
        <p class="hero-subtitle">Where Stories Come to Life</p>
        <p class="hero-description">Step into a world of drama, comedy, and wonder. Experience world-class performances in our beautifully restored 100-year-old theatre.</p>
        <div class="hero-buttons">
          <button class="btn btn-primary-custom btn-lg" data-bs-toggle="modal" data-bs-target="#bookingModal">
            <i class="bi bi-ticket-detailed"></i> Book Tickets Now
          </button>
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