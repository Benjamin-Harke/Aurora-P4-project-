<?php require APPROOT . '/views/includes/header.php'; ?>

<?php 
    // Defensive check: If performance is missing, show a clear error instead of 9 warnings
    if (!isset($data['performance']) || !$data['performance']) :
?>
    <div class="container py-5">
        <div class="alert alert-danger">
            <h4>Error: Performance data not found.</h4>
            <p>Please check your Controller and Model to ensure data is being passed correctly.</p>
            <a href="/publictickets" class="btn btn-secondary">Back to Shows</a>
        </div>
    </div>
<?php else: ?>

<div class="container py-5">
    <a href="/publictickets" class="btn btn-secondary mb-4">← Back to Shows</a>

    <div class="row">
        <!-- Main Content Column -->
        <div class="col-md-8">
            <div class="card mb-4 shadow">
                <div class="card-header bg-primary text-white">
                    <h2 class="mb-0"><?php echo htmlspecialchars($data['performance']->naam ?? 'Unknown'); ?></h2>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h1 class="card-title h3"><?php echo htmlspecialchars($data['performance']->naam ?? 'Unknown'); ?></h1>
                            <p class="text-muted"><span class="badge bg-secondary">Theatre Performance</span></p>
                        </div>
                    </div>

                    <hr>

                    <h5>Performance Details</h5>
                    <div class="row mt-3">
                        <div class="col-md-6">
                            <p><strong>Date:</strong><br><?php echo date('l, d F Y', strtotime($data['performance']->datum ?? 'today')); ?></p>
                            <p><strong>Time:</strong><br><?php echo date('H:i', strtotime($data['performance']->tijd ?? '00:00')); ?></p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Venue:</strong><br>Aurora Main Stage</p>
                            <p><strong>Price per Ticket:</strong><br>€25.00</p>
                        </div>
                    </div>

                    <hr>

                    <h5>Show Description</h5>
                    <div class="lead" style="font-size: 1.1rem;">
                        <?php echo nl2br(htmlspecialchars($data['performance']->beschrijving ?? 'No description available.')); ?>
                    </div>
                    
                    <hr>

                    <h5>Availability</h5>
                    <div class="mb-3">
                        <p>Available Seats: <strong><?php echo $data['available_seats'] ?? 0; ?> / <?php echo $data['performance']->max_aantal_tickets ?? 0; ?></strong></p>
                        <div class="progress" style="height: 25px;">
                            <?php 
                            $totalSeats = (int)($data['performance']->max_aantal_tickets ?? 0);
                            $availSeats = (int)($data['available_seats'] ?? 0);
                            $percentage = $totalSeats > 0 ? ($availSeats / $totalSeats) * 100 : 0;
                            $barClass = $percentage > 50 ? 'success' : ($percentage > 20 ? 'warning' : 'danger');
                            ?>
                            <div class="progress-bar bg-<?php echo $barClass; ?>" 
                                 role="progressbar" 
                                 style="width: <?php echo $percentage; ?>%"
                                 aria-valuenow="<?php echo $percentage; ?>" 
                                 aria-valuemin="0" 
                                 aria-valuemax="100">
                                <?php echo round($percentage); ?>% Free
                            </div>
                        </div>
                    </div>

                    <!-- Call to Action -->
                    <div class="mt-4">
                        <?php if (isset($data['is_sold_out']) && $data['is_sold_out']): ?>
                            <div class="alert alert-danger text-center">
                                <h4 class="alert-heading mb-0">SOLD OUT</h4>
                                <p class="mb-0">All tickets for this performance have been booked.</p>
                            </div>
                        <?php else: ?>
                            <?php if ($percentage <= 20): ?>
                                <div class="alert alert-warning py-2 small mb-3">
                                    <strong>Limited Seats Remaining!</strong> Book now to avoid missing out.
                                </div>
                            <?php endif; ?>
                            <a href="/usertickets/selectSeat/<?php echo $data['performance']->id ?? ''; ?>" class="btn btn-primary btn-lg w-100">
                                Select Your Seat & Book Now
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar Column -->
        <div class="col-md-4">
            <div class="card sticky-top shadow-sm" style="top: 20px;">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Quick Info</h5>
                </div>
                <div class="card-body">
                    <h6 class="text-muted text-uppercase small">Show</h6>
                    <p class="fw-bold"><?php echo htmlspecialchars($data['performance']->naam ?? 'Unknown'); ?></p>
                    
                    <h6 class="text-muted text-uppercase small mt-3">When</h6>
                    <p class="mb-0"><?php echo date('d M Y', strtotime($data['performance']->datum ?? 'today')); ?></p>
                    <p><?php echo date('H:i', strtotime($data['performance']->tijd ?? '00:00')); ?></p>
                    
                    <h6 class="text-muted text-uppercase small mt-3">Venue</h6>
                    <p>Aurora Theatre - Main Stage</p>

                    <h6 class="text-muted text-uppercase small mt-3">Price</h6>
                    <p class="h5 text-primary">€25.00</p>
                    
                    <hr>
                    
                    <h6 class="text-muted text-uppercase small">Current Status</h6>
                    <?php if(isset($data['is_sold_out']) && $data['is_sold_out']): ?>
                        <span class="badge bg-danger w-100">Sold Out</span>
                    <?php else: ?>
                        <span class="badge bg-success w-100">Tickets Available</span>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<?php require APPROOT . '/views/includes/footer.php'; ?>