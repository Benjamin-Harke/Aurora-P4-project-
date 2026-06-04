<?php 
/** @var array $data */
require APPROOT . '/views/includes/header.php'; ?>

<div class="container py-5">
    <h1 class="mb-4">Available Shows & Tickets</h1>

    <!-- Filters and Search Section -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="get" action="/publictickets" id="filterForm">
                <div class="row g-3">
                    <!-- Search -->
                    <div class="col-md-6">
                        <label for="search" class="form-label">Search Show Title</label>
                        <input type="text" class="form-control" id="search" name="search" 
                               placeholder="Search shows..." value="<?php echo htmlspecialchars($data['search_query'] ?? ''); ?>">
                    </div>

                    <!-- Genre Filter -->
                    <div class="col-md-3">
                        <label for="genre_id" class="form-label">Sort By</label>
                        <select class="form-select" id="sort" name="sort">
                            <option value="date">Date (Earliest)</option>
                        </select>
                    </div>

                    <!-- Date Range -->
                    <div class="col-md-6">
                        <label for="start_date" class="form-label">From Date</label>
                        <input type="date" class="form-control" id="start_date" name="start_date" 
                               value="<?php echo htmlspecialchars($data['start_date']); ?>">
                    </div>

                    <div class="col-md-6">
                        <label for="end_date" class="form-label">To Date</label>
                        <input type="date" class="form-control" id="end_date" name="end_date" 
                               value="<?php echo htmlspecialchars($data['end_date']); ?>">
                    </div>

                    <!-- Submit Button -->
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary me-2">Search</button>
                        <a href="/publictickets" class="btn btn-secondary">Reset Filters</a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Results Section -->
    <?php if (empty($data['performances'])): ?>
        <div class="alert alert-info">
            <p>No performances found matching your criteria. Try adjusting your filters.</p>
        </div>
    <?php else: ?>
        <div class="row g-4">
            <?php foreach ($data['performances'] as $perf): ?>
                <div class="col-md-6 col-lg-4">
                    <div class="card h-100">
                        <div class="card-body">
                            <h5 class="card-title"><?php echo htmlspecialchars($perf->naam); ?></h5>
                            <p class="card-text text-muted">Theatre Performance</p>

                            <div class="performance-info mb-3">
                                <p class="mb-2">
                                    <strong>Date & Time:</strong><br>
                                    <?php echo date('d M Y', strtotime($perf->datum)); ?> at 
                                    <?php echo date('H:i', strtotime($perf->tijd)); ?>
                                </p>
                                <p class="mb-2">
                                    <strong>Venue:</strong><br>
                                    Aurora Theatre
                                </p>
                                <p class="mb-2">
                                    <strong>Available Seats:</strong><br>
                                    <span class="badge bg-success"><?php echo $perf->max_aantal_tickets; ?> seats</span>
                                </p>
                            </div>

                            <!-- Availability -->
                            <div class="mb-3">
                                <span class="badge bg-success">ON SALE</span>
                            </div>

                            <a href="/publictickets/performance/<?php echo $perf->id; ?>" class="btn btn-primary btn-sm w-100">
                                View & Book
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="mt-4">
            <p class="text-muted">Showing <?php echo count($data['performances']); ?> performance(s)</p>
        </div>
    <?php endif; ?>
</div>

<?php require APPROOT . '/views/includes/footer.php'; ?>
