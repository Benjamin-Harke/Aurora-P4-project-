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
                        <label for="genre_id" class="form-label">Genre</label>
                        <select class="form-select" id="genre_id" name="genre_id">
                            <option value="">All Genres</option>
                            <?php foreach ($data['genres'] as $genre): ?>
                                <option value="<?php echo $genre->id; ?>" 
                                    <?php echo isset($data['selected_genre']) && $data['selected_genre'] == $genre->id ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($genre->name); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Sort -->
                    <div class="col-md-3">
                        <label for="sort" class="form-label">Sort By</label>
                        <select class="form-select" id="sort" name="sort">
                            <option value="date" <?php echo $data['sort_by'] === 'date' ? 'selected' : ''; ?>>Date (Earliest)</option>
                            <option value="price_asc" <?php echo $data['sort_by'] === 'price_asc' ? 'selected' : ''; ?>>Price (Low to High)</option>
                            <option value="price_desc" <?php echo $data['sort_by'] === 'price_desc' ? 'selected' : ''; ?>>Price (High to Low)</option>
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
                            <h5 class="card-title"><?php echo htmlspecialchars($perf->show_title); ?></h5>
                            <p class="card-text text-muted"><?php echo htmlspecialchars($perf->genre_name ?? 'Unknown'); ?></p>

                            <div class="performance-info mb-3">
                                <p class="mb-2">
                                    <strong>Date & Time:</strong><br>
                                    <?php echo date('d M Y', strtotime($perf->performance_date)); ?> at 
                                    <?php echo date('H:i', strtotime($perf->performance_time)); ?>
                                </p>
                                <p class="mb-2">
                                    <strong>Venue:</strong><br>
                                    <?php echo htmlspecialchars($perf->venue); ?>
                                </p>
                                <p class="mb-2">
                                    <strong>Price:</strong><br>
                                    €<?php echo number_format($perf->price ?? 0, 2); ?>
                                </p>
                            </div>

                            <!-- Availability -->
                            <div class="mb-3">
                                <?php 
                                $availableSeats = $perf->available_seats;
                                $totalSeats = $perf->total_seats;
                                $percentage = $totalSeats > 0 ? ($availableSeats / $totalSeats) * 100 : 0;
                                ?>
                                <small class="text-muted">Available Seats: <?php echo $availableSeats; ?>/<?php echo $totalSeats; ?></small>
                                <div class="progress" style="height: 20px;">
                                    <div class="progress-bar bg-<?php echo $percentage > 50 ? 'success' : ($percentage > 20 ? 'warning' : 'danger'); ?>" 
                                         role="progressbar" 
                                         style="width: <?php echo $percentage; ?>%" 
                                         aria-valuenow="<?php echo $percentage; ?>" 
                                         aria-valuemin="0" 
                                         aria-valuemax="100">
                                        <?php echo round($percentage); ?>%
                                    </div>
                                </div>
                            </div>

                            <!-- Status Badge -->
                            <div class="mb-3">
                                <?php if ($availableSeats === 0): ?>
                                    <span class="badge bg-danger">SOLD OUT</span>
                                <?php elseif ($percentage <= 20): ?>
                                    <span class="badge bg-warning">LIMITED SEATS</span>
                                <?php elseif ($perf->status === 'cancelled'): ?>
                                    <span class="badge bg-secondary">CANCELLED</span>
                                <?php else: ?>
                                    <span class="badge bg-success">ON SALE</span>
                                <?php endif; ?>
                            </div>

                            <a href="/publictickets/performance/<?php echo $perf->id; ?>" class="btn btn-primary btn-sm w-100">
                                View Details
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
