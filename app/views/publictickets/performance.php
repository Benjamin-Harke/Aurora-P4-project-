<?php 
/** @var array $data */
require APPROOT . '/views/includes/header.php'; ?>

<div class="container py-5">
    <a href="/publictickets" class="btn btn-secondary mb-4">← Back to Shows</a>

    <div class="row">
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-body">
                    <h1 class="card-title"><?php echo htmlspecialchars($data['performance']->show_title); ?></h1>
                    <p class="text-muted"><?php echo htmlspecialchars($data['performance']->genre_name ?? 'Unknown'); ?></p>

                    <hr>

                    <h5>Performance Details</h5>
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Date:</strong><br><?php echo date('l, d F Y', strtotime($data['performance']->performance_date)); ?></p>
                            <p><strong>Time:</strong><br><?php echo date('H:i', strtotime($data['performance']->performance_time)); ?></p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Venue:</strong><br><?php echo htmlspecialchars($data['performance']->venue); ?></p>
                            <p><strong>Price per Ticket:</strong><br>€<?php echo number_format($data['performance']->price ?? 0, 2); ?></p>
                        </div>
                    </div>

                    <hr>

                    <h5>Show Description</h5>
                    <p><?php echo nl2br(htmlspecialchars($data['performance']->show_description ?? 'No description available.')); ?></p>

                    <hr>

                    <h5>Availability</h5>
                    <div class="mb-3">
                        <p>Available Seats: <strong><?php echo $data['available_seats']; ?> / <?php echo $data['performance']->total_seats; ?></strong></p>
                        <div class="progress" style="height: 30px;">
                            <?php 
                            $percentage = $data['performance']->total_seats > 0 ? 
                                ($data['available_seats'] / $data['performance']->total_seats) * 100 : 0;
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

                    <!-- Status -->
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
                        <button class="btn btn-primary btn-lg" data-bs-toggle="modal" data-bs-target="#bookingModal">
                            Book Tickets
                        </button>
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
                    <p><small class="text-muted">SHOW</small><br><?php echo htmlspecialchars($data['performance']->show_title); ?></p>
                    <p><small class="text-muted">WHEN</small><br><?php echo date('d M Y, H:i', strtotime($data['performance']->performance_date . ' ' . $data['performance']->performance_time)); ?></p>
                    <p><small class="text-muted">WHERE</small><br><?php echo htmlspecialchars($data['performance']->venue); ?></p>
                    <p><small class="text-muted">PRICE</small><br>€<?php echo number_format($data['performance']->price ?? 0, 2); ?></p>
                    <p><small class="text-muted">AVAILABLE</small><br><?php echo $data['available_seats']; ?> seats</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Booking Modal -->
<div class="modal fade" id="bookingModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Book Tickets</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Ticket booking feature will be available on the <strong>feature/ticket-creation</strong> branch.</p>
                <p>For now, you can view show details and availability.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<?php require APPROOT . '/views/includes/footer.php'; ?>
