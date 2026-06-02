<?php 
/** @var array $data */
require APPROOT . '/views/includes/header.php'; ?>

<div class="container-fluid py-5">
    <a href="/admintickets/dashboard" class="btn btn-secondary mb-4">← Back to Dashboard</a>

    <h1 class="mb-4">Search Tickets</h1>

    <!-- Search Form -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="get" action="/admintickets/search">
                <div class="input-group">
                    <input type="text" 
                           class="form-control form-control-lg" 
                           name="q" 
                           placeholder="Search by Ticket ID or Customer Name..." 
                           value="<?php echo htmlspecialchars($data['search_query'] ?? ''); ?>"
                           autofocus>
                    <button class="btn btn-primary btn-lg" type="submit">Search</button>
                    <a href="/admintickets/search" class="btn btn-secondary btn-lg">Clear</a>
                </div>
                <small class="text-muted mt-2 d-block">
                    Enter a ticket ID number or customer first/last name to find tickets.
                </small>
            </form>
        </div>
    </div>

    <!-- Search Results -->
    <?php if (!empty($data['search_query'])): ?>
        <?php if (empty($data['results'])): ?>
            <div class="alert alert-warning">
                <h5>No Results Found</h5>
                <p>No tickets found matching "<strong><?php echo htmlspecialchars($data['search_query']); ?></strong>". 
                Try a different search term.</p>
            </div>
        <?php else: ?>
            <div class="card">
                <div class="card-header bg-dark text-white">
                    <h5 class="mb-0">Search Results (<?php echo count($data['results']); ?> result<?php echo count($data['results']) !== 1 ? 's' : ''; ?>)</h5>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Ticket ID</th>
                                <th>Show</th>
                                <th>Date & Time</th>
                                <th>Seat</th>
                                <th>Customer</th>
                                <th>Status</th>
                                <th>Price</th>
                                <th>Booked Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($data['results'] as $ticket): ?>
                                <tr>
                                    <td><strong>#<?php echo $ticket->id; ?></strong></td>
                                    <td><?php echo htmlspecialchars($ticket->show_title); ?></td>
                                    <td><?php echo date('d M Y H:i', strtotime($ticket->performance_date . ' ' . $ticket->performance_time)); ?></td>
                                    <td><?php echo htmlspecialchars($ticket->seat_number); ?></td>
                                    <td>
                                        <?php if ($ticket->user_id): ?>
                                            <?php echo htmlspecialchars($ticket->firstname . ' ' . ($ticket->infix ? $ticket->infix . ' ' : '') . $ticket->lastname); ?>
                                        <?php else: ?>
                                            <span class="text-muted">Not assigned</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <span class="badge bg-<?php 
                                            echo $ticket->status === 'booked' ? 'success' : 
                                                 ($ticket->status === 'reserved' ? 'info' : 
                                                 ($ticket->status === 'cancelled' ? 'danger' : 'secondary'));
                                        ?>">
                                            <?php echo ucfirst($ticket->status); ?>
                                        </span>
                                    </td>
                                    <td>€<?php echo number_format($ticket->price, 2); ?></td>
                                    <td>
                                        <?php echo $ticket->booking_date ? date('d M Y H:i', strtotime($ticket->booking_date)) : '-'; ?>
                                    </td>
                                    <td>
                                        <a href="/admintickets/performanceDetails/<?php echo $ticket->performance_id; ?>" 
                                           class="btn btn-sm btn-info">View Performance</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php endif; ?>
    <?php else: ?>
        <div class="alert alert-info">
            <p class="mb-0">Enter search criteria above to find tickets by ID or customer name.</p>
        </div>
    <?php endif; ?>
</div>

<?php require APPROOT . '/views/includes/footer.php'; ?>
