<?php 
/** @var array $data */
require APPROOT . '/views/includes/header.php'; ?>

<div class="container py-5">
    <h1 class="mb-4">My Tickets</h1>

    <!-- Welcome Section -->
    <div class="alert alert-info mb-4">
        <p class="mb-0">Welcome, <strong><?php echo htmlspecialchars($data['user']->firstname); ?></strong>! 
        Below are your booked theatre tickets.</p>
    </div>

    <?php if ($data['has_tickets']): ?>
        <div class="table-responsive">
            <table class="table table-hover">
                <thead class="table-light">
                    <tr>
                        <th>Show</th>
                        <th>Date & Time</th>
                        <th>Venue</th>
                        <th>Seat</th>
                        <th>Price</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($data['tickets'] as $ticket): ?>
                        <tr>
                            <td>
                                <strong><?php echo htmlspecialchars($ticket->show_title); ?></strong><br>
                                <small class="text-muted"><?php echo htmlspecialchars($ticket->genre_name ?? 'Unknown'); ?></small>
                            </td>
                            <td>
                                <?php echo date('d M Y', strtotime($ticket->performance_date)); ?><br>
                                <small><?php echo date('H:i', strtotime($ticket->performance_time)); ?></small>
                            </td>
                            <td><?php echo htmlspecialchars($ticket->venue); ?></td>
                            <td><strong><?php echo htmlspecialchars($ticket->seat_number); ?></strong></td>
                            <td>€<?php echo number_format($ticket->price, 2); ?></td>
                            <td>
                                <?php 
                                $statusBadge = [
                                    'booked' => 'success',
                                    'reserved' => 'info',
                                    'cancelled' => 'danger',
                                    'available' => 'secondary'
                                ];
                                $badgeClass = $statusBadge[$ticket->status] ?? 'secondary';
                                ?>
                                <span class="badge bg-<?php echo $badgeClass; ?>">
                                    <?php echo ucfirst($ticket->status); ?>
                                </span>
                            </td>
                            <td>
                                <a href="/usertickets/viewTicket/<?php echo $ticket->id; ?>" class="btn btn-sm btn-info">
                                    View
                                </a>
                                <?php 
                                $performanceDateTime = strtotime($ticket->performance_date . ' ' . $ticket->performance_time);
                                if ($performanceDateTime > time() && $ticket->status === 'booked'):
                                ?>
                                    <a href="/usertickets/cancelTicket/<?php echo $ticket->id; ?>" 
                                       class="btn btn-sm btn-danger"
                                       onclick="return confirm('Are you sure you want to cancel this ticket?');">
                                        Cancel
                                    </a>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            <h5>Summary</h5>
            <p>Total Tickets: <strong><?php echo count($data['tickets']); ?></strong></p>
            <p>
                Total Value: <strong>€<?php 
                    $total = array_sum(array_map(fn($t) => floatval($t->price), $data['tickets'])); 
                    echo number_format($total, 2); 
                ?></strong>
            </p>
        </div>
    <?php else: ?>
        <div class="alert alert-warning">
            <h5>No Tickets Yet</h5>
            <p>You haven't booked any tickets yet. <a href="/publictickets" class="alert-link">Browse available shows</a> to get started!</p>
        </div>
    <?php endif; ?>

    <div class="mt-5">
        <a href="/publictickets" class="btn btn-primary">Browse More Shows</a>
    </div>
</div>

<?php require APPROOT . '/views/includes/footer.php'; ?>
