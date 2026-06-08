<?php 
/** @var array $data */
require APPROOT . '/views/includes/header.php'; ?>

<div class="container py-5">
    <a href="/publictickets" class="btn btn-secondary mb-4">← Back to Shows</a>

    <div class="row">
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-body">
                    <!-- FIX: show_title -> naam -->
                    <h1 class="card-title"><?php echo htmlspecialchars($data['performance']->naam); ?></h1>
                    <!-- FIX: Using a placeholder as genre isn't in the main table -->
                    <p class="text-muted"><span class="badge bg-secondary">Theatre Performance</span></p>

                    <hr>

                    <h5>Performance Details</h5>
                    <div class="row">
                        <div class="col-md-6">
                            <!-- FIX: performance_date -> datum -->
                            <p><strong>Date:</strong><br><?php echo date('l, d F Y', strtotime($data['performance']->datum)); ?></p>
                            <!-- FIX: performance_time -> tijd -->
                            <p><strong>Time:</strong><br><?php echo date('H:i', strtotime($data['performance']->tijd)); ?></p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Venue:</strong><br>Aurora Main Stage</p>
                            <p><strong>Price per Ticket:</strong><br>€25.00</p>
                        </div>
                    </div>

                    <hr>

                    <h5>Show Description</h5>
                    <!-- FIX: show_description -> beschrijving -->
                    <p><?php echo nl2br(htmlspecialchars($data['performance']->beschrijving ?? 'No description available.')); ?></p>

                    <hr>

                    <h5>Availability</h5>
                    <div class="mb-3">
                        <!-- FIX: total_seats -> max_aantal_tickets -->
                        <p>Available Seats: <strong><?php echo $data['available_seats']; ?> / <?php echo $data['performance']->max_aantal_tickets; ?></strong></p>
                        <div class="progress" style="height: 30px;">
                            <?php 
                            $totalSeats = $data['performance']->max_aantal_tickets;
                            $percentage = $totalSeats > 0 ? ($data['available_seats'] / $totalSeats) * 100 : 0;
                            ?>
                            <div class="progress-bar bg-<?php echo $percentage > 50 ? 'success' : ($percentage > 20 ? 'warning' : 'danger'); ?>" 
                                 role="progressbar" 
                                 style="width: <?php echo $percentage; ?>%"
                                 aria-valuenow="<?php echo $percentage; ?>" 
                                 aria-valuemin="0" 
                                 aria-valuemax="100">
                                <?php echo round($percentage); ?>% Available
                            </div>
                        </div>
                    </div>

                    <!-- Status Badges -->
                    <div class="mb-4">
                        <?php if ($data['is_sold_out']): ?>
                            <h4><span class="badge bg-danger">SOLD OUT</span></h4>
                            <p class="text-muted">Unfortunately, all tickets for this performance have been sold.</p>
                        <?php elseif ($percentage <= 20): ?>
                            <h4><span class="badge bg-warning">LIMITED SEATS REMAINING</span></h4>
                            <p class="text-danger">Only a few tickets left! Book now to avoid missing out.</p>
                        <?php else: ?>
                            <h4><span class="badge bg-success">ON SALE</span></h4>
                        <?php endif; ?>
                    </div>

                    <!-- Call to Action -->
                    <?php if (!$data['is_sold_out']): ?>
                        <!-- FIX: Changed from Modal to direct link to Seat Selection -->
                        <a href="/usertickets/selectSeat/<?php echo $data['performance']->id; ?>" class="btn btn-primary btn-lg">
                            Select Your Seat & Book
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card sticky-top" style="top: 20px;">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Quick Info</h5>
                </div>
                <div class="card-body">
                    <!-- FIX: Mapping English to Dutch keys here too -->
                    <p><small class="text-muted">SHOW</small><br><?php echo htmlspecialchars($data['performance']->naam); ?></p>
                    <p><small class="text-muted">WHEN</small><br><?php echo date('d M Y, H:i', strtotime($data['performance']->datum . ' ' . $data['performance']->tijd)); ?></p>
                    <p><small class="text-muted">WHERE</small><br>Aurora Main Stage</p>
                    <p><small class="text-muted">PRICE</small><br>€25.00</p>
                    <p><small class="text-muted">AVAILABLE</small><br><?php echo $data['available_seats']; ?> seats</p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require APPROOT . '/views/includes/footer.php'; ?>