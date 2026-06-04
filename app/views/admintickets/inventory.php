<?php 
/** @var array $data */
require APPROOT . '/views/includes/header.php'; ?>

<div class="container-fluid py-5">
    <a href="/admintickets/dashboard" class="btn btn-secondary mb-4">← Back to Dashboard</a>

    <h1 class="mb-4">Inventory Management</h1>

    <div class="card">
        <div class="card-header bg-dark text-white">
            <h5 class="mb-0">Ticket Inventory by Performance</h5>
        </div>
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Show Title</th>
                        <th>Date & Time</th>
                        <th>Venue</th>
                        <th>Total Seats</th>
                        <th>Available</th>
                        <th>Booked</th>
                        <th>Capacity %</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($data['inventory'] as $item): 
                        // Use -> because these are objects
                        $perf = $item->performance; 
                    ?>
                        <tr <?php echo $item->is_oversold ? 'class="table-danger"' : ''; ?>>
                            <!-- Map English names to your Dutch ERD names -->
                            <td><strong><?php echo htmlspecialchars($perf->naam); ?></strong></td>
                            <td><?php echo date('d M Y H:i', strtotime($perf->datum . ' ' . $perf->tijd)); ?></td>
                            <td>Main Hall</td> <!-- 'venue' is not in your new ERD -->
                            <td><?php echo $perf->max_aantal_tickets; ?></td>
                            <td>
                                <span class="badge bg-success"><?php echo $item->available_seats; ?></span>
                            </td>
                            <td>
                                <span class="badge bg-info"><?php echo $item->booked_seats; ?></span>
                            </td>
                            <td>
                                <div class="progress" style="height: 20px;">
                                    <div class="progress-bar bg-<?php 
                                        echo $item->capacity_percentage >= 100 ? 'danger' : 
                                            ($item->capacity_percentage >= 80 ? 'warning' : 
                                            ($item->capacity_percentage >= 50 ? 'info' : 'success'));
                                    ?>" 
                                        style="width: <?php echo min($item->capacity_percentage, 100); ?>%">
                                        <?php echo round($item->capacity_percentage); ?>%
                                    </div>
                                </div>
                            </td>
                            <td>
                                <?php if ($item->is_oversold): ?>
                                    <span class="badge bg-danger">OVERSOLD!</span>
                                <?php elseif ($item->available_seats === 0): ?>
                                    <span class="badge bg-danger">SOLD OUT</span>
                                <?php elseif ($item->capacity_percentage >= 80): ?>
                                    <span class="badge bg-warning">NEARLY FULL</span>
                                <?php else: ?>
                                    <span class="badge bg-success">AVAILABLE</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <a href="/admintickets/performanceDetails/<?php echo $perf->id; ?>" 
                                class="btn btn-sm btn-info">Details</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Legend -->
    <div class="mt-4">
        <h6>Status Legend</h6>
        <div class="row">
            <div class="col-md-3">
                <span class="badge bg-success">AVAILABLE</span> - More than 50% seats available
            </div>
            <div class="col-md-3">
                <span class="badge bg-info">50% Capacity</span> - Half capacity reached
            </div>
            <div class="col-md-3">
                <span class="badge bg-warning">NEARLY FULL</span> - 80% or more booked
            </div>
            <div class="col-md-3">
                <span class="badge bg-danger">SOLD OUT/OVERSOLD</span> - No availability or overbooking
            </div>
        </div>
    </div>
</div>

<?php require APPROOT . '/views/includes/footer.php'; ?>
