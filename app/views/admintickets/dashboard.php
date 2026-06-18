<?php require APPROOT . '/views/includes/header.php'; ?>

<div class="container py-5">
    <!-- Header Section -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="text-white">Ticket Management Dashboard</h1>
        <a href="/publictickets" class="btn btn-outline-light btn-sm">Back to Shows</a>
    </div>

    <!-- Flash Messages -->
    <?php require APPROOT . '/views/includes/messages.php'; ?>

    <!-- Summary Stats Row -->
    <div class="row mb-4 g-3">
        <div class="col-md-4">
            <div class="card bg-primary text-white shadow h-100">
                <div class="card-body text-center">
                    <h6 class="text-uppercase small opacity-75">Total Tickets Sold</h6>
                    <h2 class="display-4 mb-0"><?php echo $data['total_tickets']; ?></h2>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-success text-white shadow h-100">
                <div class="card-body text-center">
                    <h6 class="text-uppercase small opacity-75">Total Revenue</h6>
                    <h2 class="display-4 mb-0">€<?php echo number_format($data['total_revenue'], 2); ?></h2>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-info text-white shadow h-100">
                <div class="card-body text-center">
                    <h6 class="text-uppercase small opacity-75">Tickets Scanned</h6>
                    <h2 class="display-4 mb-0"><?php echo $data['scanned_count']; ?></h2>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions Bar -->
    <div class="d-flex flex-wrap gap-2 mb-4">
        <a href="<?php echo URLROOT; ?>/admintickets/create" class="btn btn-primary">
            <i class="bi bi-plus-circle me-1"></i> Nieuw ticket toevoegen
        </a>
        <a href="/admintickets/inventory" class="btn btn-outline-info">
            <i class="bi bi-box"></i> View Inventory
        </a>
        <a href="/admintickets/validateTicket" class="btn btn-outline-primary">
            <i class="bi bi-check-circle"></i> Validate Ticket Code
        </a>
        <a href="/ticketscanning" class="btn btn-primary">
            <i class="bi bi-qr-code-scan"></i> Go to Scanner Page
        </a>
    </div>

    <!-- All Booked Tickets Table -->
    <div class="card shadow-lg bg-dark text-white border-secondary">
        <div class="card-header border-secondary d-flex justify-content-between align-items-center py-3">
            <h5 class="mb-0">All Booked Tickets</h5>
            <span class="badge bg-secondary">Real-time System Data</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-dark table-hover mb-0">
                    <thead>
                        <tr class="text-muted border-secondary">
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
                        <?php if(!empty($data['tickets'])): ?>
                            <?php foreach($data['tickets'] as $ticket): ?>
                                <tr class="align-middle border-secondary">
                                    <td class="ps-4">#<?php echo $ticket->id; ?></td>
                                    <td>
                                        <span class="fw-bold text-info"><?php echo htmlspecialchars($ticket->customer_name); ?></span>
                                    </td>
                                    <td><?php echo htmlspecialchars($ticket->show_title); ?></td>
                                    <td>€<?php echo number_format($ticket->tarief, 2); ?></td>
                                    <td><code class="text-warning"><?php echo $ticket->barcode; ?></code></td>
                                    <td>
                                        <?php 
                                            $status = strtolower($ticket->status);
                                            $badgeColor = ($status == 'gescand') ? 'info' : 'success';
                                            if ($status == 'cancelled' || $status == 'invalid') $badgeColor = 'danger';
                                        ?>
                                        <span class="badge bg-<?php echo $badgeColor; ?>">
                                            <?php echo ucfirst($ticket->status); ?>
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <div class="btn-group">
                                            <a href="/usertickets/viewTicket/<?php echo $ticket->id; ?>" class="btn btn-sm btn-outline-light" title="View Ticket">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            <a href="<?php echo URLROOT; ?>/admintickets/delete/<?php echo $ticket->id; ?>" 
                                               class="btn btn-sm btn-outline-danger" 
                                               onclick="return confirm('WARNING: Are you sure you want to PERMANENTLY delete this ticket?');"
                                               title="Delete Ticket">
                                                <i class="bi bi-trash"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
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
    body { background-color: #0b0c10; }
    .card { border-radius: 12px; }
    .table-dark { --bs-table-bg: transparent; }
    .table-hover tbody tr:hover { background-color: rgba(0, 217, 255, 0.05); }
    code { font-size: 0.85em; background: rgba(255,193,7,0.1); padding: 2px 4px; border-radius: 4px; }
    .btn-outline-info:hover, .btn-outline-primary:hover { color: #fff; }
</style>

<?php require APPROOT . '/views/includes/footer.php'; ?>