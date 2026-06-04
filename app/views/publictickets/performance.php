<?php require APPROOT . '/views/includes/header.php'; ?>

<div class="container py-5">
    <a href="/publictickets" class="btn btn-secondary mb-4">← Back to Shows</a>

    <div class="row">
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <!-- Fix: Changed show_title to naam -->
                    <h2 class="mb-0"><?php echo htmlspecialchars($data['performance']->naam); ?></h2>
                </div>
                <div class="card-body">
                    <h5>Performance Details</h5>
                    <div class="row mt-3">
                        <div class="col-md-6">
                            <!-- Fix: Changed performance_date to datum -->
                            <p><strong>Date:</strong> <?php echo date('l, d F Y', strtotime($data['performance']->datum)); ?></p>
                            <!-- Fix: Changed performance_time to tijd -->
                            <p><strong>Time:</strong> <?php echo date('H:i', strtotime($data['performance']->tijd)); ?></p>
                        </div>
                        <div class="col-md-6">
                            <!-- Note: Your DB doesn't have a 'venue' column, using placeholder -->
                            <p><strong>Venue:</strong> Main Theatre Hall</p>
                            <p><strong>Price per Ticket:</strong> €25.00</p>
                        </div>
                    </div>

                    <hr>
                    <h5>Show Description</h5>
                    <!-- Fix: Changed to beschrijving -->
                    <p><?php echo !empty($data['performance']->beschrijving) ? htmlspecialchars($data['performance']->beschrijving) : 'No description available.'; ?></p>
                    
                    <hr>
                    <h5>Availability</h5>
                    <p>Available Seats: <strong><?php echo $data['available_seats']; ?></strong> / 
                       <!-- Fix: Changed total_seats to max_aantal_tickets -->
                       <?php echo $data['performance']->max_aantal_tickets; ?></p>

                    <div class="mt-4">
                        <?php if ($data['is_sold_out']): ?>
                            <button class="btn btn-danger btn-lg w-100" disabled>SOLD OUT</button>
                        <?php else: ?>
                            <a href="/usertickets/selectSeat/<?php echo $data['performance']->id; ?>" class="btn btn-primary btn-lg">Select Your Seat</a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Summary Sidebar -->
        <div class="col-md-4">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h6 class="text-muted text-uppercase small">Show</h6>
                    <!-- Fix: Changed show_title to naam -->
                    <p class="h5"><?php echo htmlspecialchars($data['performance']->naam); ?></p>
                    
                    <h6 class="text-muted text-uppercase small mt-3">When</h6>
                    <!-- Fix: Changed performance_date to datum -->
                    <p class="mb-0"><?php echo date('d M Y', strtotime($data['performance']->datum)); ?></p>
                    <!-- Fix: Changed performance_time to tijd -->
                    <p><?php echo date('H:i', strtotime($data['performance']->tijd)); ?></p>
                    
                    <h6 class="text-muted text-uppercase small mt-3">Available</h6>
                    <p><?php echo $data['available_seats']; ?> seats remaining</p>

                    <?php if ($data['available_seats'] < 20 && !$data['is_sold_out']): ?>
                        <div class="alert alert-warning py-2 small">
                            <strong>Limited Seats!</strong> Book now to avoid missing out.
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require APPROOT . '/views/includes/footer.php'; ?>