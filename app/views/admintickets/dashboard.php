<?php require APPROOT . '/views/includes/header.php'; ?>

<div class="container py-5">
    <!-- Header Section -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="text-white">Ticket Management Dashboard</h1>
        <a href="/publictickets" class="btn btn-outline-light">Back to Shows</a>
    </div>

    <!-- Flash Messages -->
    <?php require APPROOT . '/views/includes/messages.php'; ?>

    <!-- Summary Stats -->
    <div class="row mb-5">
        <div class="col-md-4">
            <div class="card bg-primary text-white shadow">
                <div class="card-body text-center">
                    <h6>Total Tickets Sold</h6>
                    <h2 class="display-4"><?php echo $data['total_tickets']; ?></h2>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-success text-white shadow">
                <div class="card-body text-center">
                    <h6>Total Revenue</h6>
                    <h2 class="display-4">€<?php echo number_format($data['total_revenue'], 2); ?></h2>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-info text-white shadow">
                <div class="card-body text-center">
                    <h6>Tickets Scanned</h6>
                    <h2 class="display-4"><?php echo $data['scanned_count']; ?></h2>
                </div>
            </div>
        </div>
    </div>

    <!-- Tickets Table -->
    <div class="card shadow-lg bg-dark text-white border-secondary">
        <div class="card-header border-secondary d-flex justify-content-between align-items-center">
            <h5 class="mb-0">All Booked Tickets</h5>
            <span class="badge bg-secondary">System Real-time Data</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-dark table-hover mb-0">
                    <thead>
                        <tr class="text-muted">
                            <th class="ps-4">ID</th>
                            <th>Customer Name</th>
                            <th>Performance</th>
                            <th>Price</th>
                            <th>Barcode</th>
                            <th>Status</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($data['tickets'] as $ticket): ?>
                            <tr>
                                <td class="ps-4">#<?php echo $ticket->id; ?></td>
                                <td>
                                    <span class="fw-bold"><?php echo htmlspecialchars($ticket->customer_name); ?></span>
                                </td>
                                <td><?php echo htmlspecialchars($ticket->show_title); ?></td>
                                <td>€<?php echo number_format($ticket->tarief, 2); ?></td>
                                <td><code><?php echo $ticket->barcode; ?></code></td>
                                <td>
                                    <?php 
                                        $badgeColor = ($ticket->status == 'Gescand') ? 'info' : 'success';
                                        if ($ticket->status == 'cancelled' || $ticket->status == 'invalid') $badgeColor = 'danger';
                                    ?>
                                    <span class="badge bg-<?php echo $badgeColor; ?>">
                                        <?php echo ucfirst($ticket->status); ?>
                                    </span>
                                </td>
                                <td class="text-center">
                                    <div class="btn-group">
                                        <a href="/usertickets/viewTicket/<?php echo $ticket->id; ?>" class="btn btn-sm btn-outline-info" title="View Details">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="<?php echo URLROOT; ?>/admintickets/delete/<?php echo $ticket->id; ?>" 
                                           class="btn btn-sm btn-danger" 
                                           onclick="return confirm('WARNING: Are you sure you want to PERMANENTLY delete this ticket? This cannot be undone.');"
                                           title="Delete Ticket">
                                            <i class="bi bi-trash"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>

                        <?php if(empty($data['tickets'])): ?>
                            <tr>
                                <td colspan="7" class="text-center py-5 text-muted">
                                    No tickets found in the database.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<style>
    body { background-color: #000; }
    .table-hover tbody tr:hover { background-color: rgba(255, 255, 255, 0.05); }
    .card { border-radius: 15px; overflow: hidden; }
    .btn-group .btn { padding: 0.25rem 0.5rem; }
</style>

<?php require APPROOT . '/views/includes/footer.php'; ?>