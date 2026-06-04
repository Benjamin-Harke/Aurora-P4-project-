<?php 
/** @var array $data */
require APPROOT . '/views/includes/header.php'; ?>

<div class="container py-5">
    <!-- Flash Messages -->
    <?php require APPROOT . '/views/includes/messages.php'; ?>
    
    <a href="/usertickets/mytickets" class="btn btn-secondary mb-4">← Back to My Tickets</a>

    <div class="row">
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">Ticket Details</h4>
                </div>
                <div class="card-body">
                    <h3><?php echo htmlspecialchars($data['ticket']->show_title); ?></h3>
                    <p class="text-muted">
                        <span class="badge bg-secondary"><?php echo htmlspecialchars($data['ticket']->genre_name ?? 'Unknown Genre'); ?></span>
                    </p>

                    <hr>

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h6>Performance Information</h6>
                            <p><strong>Date:</strong> <?php echo date('l, d F Y', strtotime($data['ticket']->performance_date)); ?></p>
                            <p><strong>Time:</strong> <?php echo date('H:i', strtotime($data['ticket']->performance_time)); ?></p>
                            <p><strong>Venue:</strong> <?php echo htmlspecialchars($data['ticket']->venue); ?></p>
                        </div>
                        <div class="col-md-6">
                            <h6>Ticket Information</h6>
                            <p><strong>Seat:</strong> <?php echo htmlspecialchars($data['ticket']->seat_number); ?></p>
                            <p><strong>Price:</strong> €<?php echo number_format($data['ticket']->price, 2); ?></p>
                            <p><strong>Status:</strong> 
                                <?php 
                                $statusBadge = [
                                    'booked' => 'success',
                                    'reserved' => 'info',
                                    'cancelled' => 'danger',
                                    'available' => 'secondary'
                                ];
                                $badgeClass = $statusBadge[$data['ticket']->status] ?? 'secondary';
                                ?>
                                <span class="badge bg-<?php echo $badgeClass; ?>"><?php echo ucfirst($data['ticket']->status); ?></span>
                            </p>
                        </div>
                    </div>

                    <hr>

                    <!-- QR Code Section -->
                    <?php if ($data['ticket']->status === 'booked'): ?>
                        <div class="text-center mb-4">
                            <h6>Entry QR Code</h6>
                            <p class="text-muted small">Show this QR code at the entrance</p>
                            
                            <!-- The QR Code Image -->
                            <div class="bg-white p-3 d-inline-block border shadow-sm">
                                <img src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=<?php echo $data['ticket']->barcode; ?>" 
                                    alt="Ticket QR Code" 
                                    style="width: 200px; height: 200px;">
                            </div>
                            
                            <p class="mt-2"><small class="text-muted">Code: <?php echo htmlspecialchars($data['ticket']->barcode); ?></small></p>
                        </div>
                        <hr>
                    <?php endif; ?>

                    <!-- Action Buttons -->
                    <div>
                        <button class="btn btn-primary" onclick="window.print();">Print Ticket</button>
                        <a href="/usertickets/downloadTicket/<?php echo $data['ticket']->id; ?>" class="btn btn-secondary">
                            Download PDF (Coming Soon)
                        </a>

                        <?php 
                        $performanceDateTime = strtotime($data['ticket']->performance_date . ' ' . $data['ticket']->performance_time);
                        if ($performanceDateTime > time() && $data['ticket']->status === 'booked'):
                        ?>
                            <a href="/usertickets/cancelTicket/<?php echo $data['ticket']->id; ?>" 
                               class="btn btn-danger"
                               onclick="return confirm('Are you sure you want to cancel this ticket?');">
                                Cancel Ticket
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Printable Section -->
            <div class="d-none d-print-block">
                <h2><?php echo htmlspecialchars($data['ticket']->show_title); ?></h2>
                <p><strong>VALIDATION CODE:</strong> <?php echo htmlspecialchars($data['ticket']->barcode); ?></p>
                <p>Seat: <?php echo htmlspecialchars($data['ticket']->seat_number); ?></p>
                <p>Date: <?php echo date('d F Y H:i', strtotime($data['ticket']->performance_date . ' ' . $data['ticket']->performance_time)); ?></p>
                <p>Venue: <?php echo htmlspecialchars($data['ticket']->venue); ?></p>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Ticket Summary</h5>
                </div>
                <div class="card-body">
                    <p><small class="text-muted">VALIDATION CODE</small><br><strong style="..."><?php echo htmlspecialchars($data['ticket']->barcode ?? 'N/A'); ?></strong></p>
                    <hr>
                    <p><small class="text-muted">SHOW TITLE</small><br><?php echo htmlspecialchars($data['ticket']->show_title); ?></p>
                    <hr>
                    <p><small class="text-muted">DATE & TIME</small><br>
                        <?php echo date('d M Y', strtotime($data['ticket']->performance_date)); ?><br>
                        <?php echo date('H:i', strtotime($data['ticket']->performance_time)); ?>
                    </p>
                    <hr>
                    <p><small class="text-muted">VENUE</small><br><?php echo htmlspecialchars($data['ticket']->venue); ?></p>
                    <hr>
                    <p><small class="text-muted">SEAT NUMBER</small><br><strong style="font-size: 1.5em;"><?php echo htmlspecialchars($data['ticket']->seat_number); ?></strong></p>
                    <hr>
                    <p><small class="text-muted">PRICE</small><br><strong>€<?php echo number_format($data['ticket']->price, 2); ?></strong></p>
                </div>
                <div class="d-none d-print-block">
                    <h2><?php echo htmlspecialchars($data['ticket']->show_title); ?></h2>
                    <!-- Add the QR code image here for printing -->
                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=<?php echo $data['ticket']->barcode; ?>">
                    <p><strong>VALIDATION CODE:</strong> <?php echo htmlspecialchars($data['ticket']->barcode); ?></p>
                    ...
                </div>
            </div>

            <!-- Important Info -->
            <div class="alert alert-info mt-3">
                <h6>Important</h6>
                <ul class="mb-0 small">
                    <li>Show your <strong>validation code</strong> at entrance</li>
                    <li>Arrive 15 minutes early</li>
                    <li>Bring a valid ID</li>
                    <li>Keep this ticket safe</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<?php require APPROOT . '/views/includes/footer.php'; ?>
