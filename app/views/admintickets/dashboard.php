<?php 
/** @var array $data */
require APPROOT . '/views/includes/header.php'; ?>

<div class="container-fluid py-5">
    <h1 class="mb-4">Admin Dashboard - Ticket Management</h1>

    <!-- Key Metrics -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h6 class="text-muted">Total Revenue</h6>
                    <h3>€<?php echo number_format($data['total_revenue'], 2); ?></h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h6 class="text-muted">Total Shows</h6>
                    <h3><?php echo $data['total_shows']; ?></h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h6 class="text-muted">Total Seats Booked</h6>
                    <h3><?php echo $data['total_booked']; ?></h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h6 class="text-muted">Occupancy Rate</h6>
                    <h3><?php echo $data['occupancy_rate']; ?>%</h3>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="mb-4">
        <a href="/admintickets/inventory" class="btn btn-info">View Inventory</a>
        <a href="/admintickets/search" class="btn btn-success">Search Tickets</a>
        <a href="/admintickets/validateTicket" class="btn btn-primary"><i class="bi bi-check-circle"></i> Validate Ticket Code</a>
    </div>

    <!-- Performance Analytics Table -->
    <div class="card">
        <div class="card-header bg-dark text-white">
            <h5 class="mb-0">Performance Analytics</h5>
        </div>
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Show Title</th>
                        <th>Date & Time</th>
                        <th>Venue</th>
                        <th>Total Seats</th>
                        <th>Booked</th>
                        <th>Available</th>
                        <th>Occupancy</th>
                        <th>Revenue</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($data['analytics'])): ?>
                        <tr>
                            <td colspan="9" class="text-center text-muted py-4">No performances found</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($data['analytics'] as $perf): ?>
                            <tr>
                                <td><strong><?php echo htmlspecialchars($perf['show_title']); ?></strong></td>
                                <td><?php echo date('d M Y H:i', strtotime($perf['performance_date'] . ' ' . $perf['performance_time'])); ?></td>
                                <td><?php echo htmlspecialchars($perf['venue']); ?></td>
                                <td><?php echo $perf['total_seats']; ?></td>
                                <td>
                                    <span class="badge bg-success"><?php echo $perf['booked_seats']; ?></span>
                                </td>
                                <td>
                                    <span class="badge bg-warning"><?php echo $perf['available_seats']; ?></span>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="progress flex-grow-1 me-2" style="width: 80px;">
                                            <div class="progress-bar bg-<?php 
                                                echo $perf['occupancy_rate'] >= 100 ? 'danger' : 
                                                     ($perf['occupancy_rate'] >= 80 ? 'warning' : 'success'); 
                                            ?>" 
                                                 style="width: <?php echo min($perf['occupancy_rate'], 100); ?>%">
                                            </div>
                                        </div>
                                        <span><?php echo $perf['occupancy_rate']; ?>%</span>
                                    </div>
                                </td>
                                <td>€<?php echo number_format($perf['revenue'], 2); ?></td>
                                <td>
                                    <a href="/admintickets/performanceDetails/<?php echo $perf['id']; ?>" 
                                       class="btn btn-sm btn-info">View</a>
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
