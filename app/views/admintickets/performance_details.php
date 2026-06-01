<?php 
/** @var array $data */
require APPROOT . '/views/includes/header.php'; ?>

<div class="container-fluid py-5">
    <a href="/admintickets/dashboard" class="btn btn-secondary mb-4">← Back to Dashboard</a>

    <div class="row mb-4">
        <div class="col-md-8">
            <h1><?php echo htmlspecialchars($data['performance']->show_title); ?></h1>
        </div>
        <div class="col-md-4 text-end">
            <h5><?php echo date('d F Y H:i', strtotime($data['performance']->performance_date . ' ' . $data['performance']->performance_time)); ?></h5>
        </div>
    </div>

    <!-- Performance Info Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h6 class="text-muted">Total Tickets</h6>
                    <h3><?php echo $data['total_tickets']; ?></h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center bg-success text-white">
                <div class="card-body">
                    <h6>Booked</h6>
                    <h3><?php echo $data['booked_tickets']; ?></h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center bg-warning">
                <div class="card-body">
                    <h6>Available</h6>
                    <h3><?php echo $data['available_tickets']; ?></h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h6 class="text-muted">Occupancy</h6>
                    <h3><?php echo round(($data['booked_tickets'] / $data['total_tickets']) * 100); ?>%</h3>
                </div>
            </div>
        </div>
    </div>

    <!-- Performance Details -->
    <div class="card mb-4">
        <div class="card-header bg-light">
            <h5 class="mb-0">Performance Details</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <p><strong>Show:</strong> <?php echo htmlspecialchars($data['performance']->show_title); ?></p>
                    <p><strong>Genre:</strong> <?php echo htmlspecialchars($data['performance']->genre_name ?? 'Unknown'); ?></p>
                    <p><strong>Date:</strong> <?php echo date('l, d F Y', strtotime($data['performance']->performance_date)); ?></p>
                    <p><strong>Time:</strong> <?php echo date('H:i', strtotime($data['performance']->performance_time)); ?></p>
                </div>
                <div class="col-md-6">
                    <p><strong>Venue:</strong> <?php echo htmlspecialchars($data['performance']->venue); ?></p>
                    <p><strong>Status:</strong> 
                        <span class="badge bg-<?php echo $data['performance']->status === 'on_sale' ? 'success' : ($data['performance']->status === 'sold_out' ? 'danger' : 'secondary'); ?>">
                            <?php echo ucfirst($data['performance']->status); ?>
                        </span>
                    </p>
                    <p><strong>Total Capacity:</strong> <?php echo $data['performance']->total_seats; ?> seats</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Ticket Details Table -->
    <div class="card">
        <div class="card-header bg-dark text-white">
            <h5 class="mb-0">Ticket Details</h5>
        </div>
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Seat</th>
                        <th>Price</th>
                        <th>Status</th>
                        <th>Booked By</th>
                        <th>Booking Date</th>
                        <th>QR Code</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($data['tickets'])): ?>
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">No tickets found</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($data['tickets'] as $ticket): ?>
                            <tr>
                                <td><strong><?php echo htmlspecialchars($ticket->seat_number); ?></strong></td>
                                <td>€<?php echo number_format($ticket->price, 2); ?></td>
                                <td>
                                    <span class="badge bg-<?php 
                                        echo $ticket->status === 'booked' ? 'success' : 
                                             ($ticket->status === 'reserved' ? 'info' : 
                                             ($ticket->status === 'cancelled' ? 'danger' : 'secondary'));
                                    ?>">
                                        <?php echo ucfirst($ticket->status); ?>
                                    </span>
                                </td>
                                <td>
                                    <?php if ($ticket->user_id): ?>
                                        <?php echo htmlspecialchars($ticket->firstname . ' ' . ($ticket->infix ? $ticket->infix . ' ' : '') . $ticket->lastname); ?>
                                    <?php else: ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php echo $ticket->booking_date ? date('d M Y H:i', strtotime($ticket->booking_date)) : '-'; ?>
                                </td>
                                <td>
                                    <?php if ($ticket->qr_code): ?>
                                        <small><?php echo htmlspecialchars(substr($ticket->qr_code, 0, 10)); ?>...</small>
                                    <?php else: ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require APPROOT . '/views/includes/footer.php'; ?>
