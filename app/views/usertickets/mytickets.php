<?php 
/** @var array $data */
require APPROOT . '/views/includes/header.php'; ?>

<div class="container py-5">
    <h1 class="mb-4">My Tickets</h1>

    <!-- Flash Messages -->
    <?php require APPROOT . '/views/includes/messages.php'; ?>

    <!-- Welcome Section -->
    <div class="alert alert-info mb-4">
        <p class="mb-0">Welcome, <strong><?php echo htmlspecialchars($data['user']->firstname); ?></strong>! 
        Below are your booked theatre tickets.</p>
    </div>

    <?php if ($data['has_tickets']): ?>
        
        <!-- Filter Section -->
        <div class="card mb-4 bg-light">
            <div class="card-body">
                <h5 class="card-title mb-3">Filter Tickets</h5>
                <div class="row g-3">
                    <div class="col-md-4">
                        <label for="filterGenre" class="form-label">Genre</label>
                        <select id="filterGenre" class="form-select" onchange="filterTickets()">
                            <option value="">All Genres</option>
                            <?php 
                            $genres = array_unique(array_column($data['tickets'], 'genre_name'));
                            foreach ($genres as $genre): 
                                if ($genre):
                            ?>
                                <option value="<?php echo htmlspecialchars($genre); ?>">
                                    <?php echo htmlspecialchars($genre); ?>
                                </option>
                            <?php 
                                endif;
                            endforeach; 
                            ?>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="filterStatus" class="form-label">Status</label>
                        <select id="filterStatus" class="form-select" onchange="filterTickets()">
                            <option value="">All Statuses</option>
                            <option value="booked">Booked</option>
                            <option value="reserved">Reserved</option>
                            <option value="cancelled">Cancelled</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="filterShowTitle" class="form-label">Show Title</label>
                        <input type="text" id="filterShowTitle" class="form-control" 
                               placeholder="Search show name..." onkeyup="filterTickets()">
                    </div>
                    <div class="col-md-1 d-flex align-items-end">
                        <button class="btn btn-outline-secondary w-100" onclick="clearFilters()">
                            <i class="bi bi-arrow-counterclockwise"></i> Reset
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabs for Upcoming and Past Tickets -->
        <ul class="nav nav-tabs mb-4" id="ticketTabs" role="tablist">
            <?php if($data['has_upcoming']): ?>
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="upcoming-tab" data-bs-toggle="tab" data-bs-target="#upcoming" 
                            type="button" role="tab" aria-controls="upcoming" aria-selected="true">
                        Upcoming Shows (<span class="upcoming-count"><?php echo count($data['upcomingTickets']); ?></span>)
                    </button>
                </li>
            <?php endif; ?>
            <?php if($data['has_past']): ?>
                <li class="nav-item" role="presentation">
                    <button class="nav-link <?php echo !$data['has_upcoming'] ? 'active' : ''; ?>" 
                            id="past-tab" data-bs-toggle="tab" data-bs-target="#past" 
                            type="button" role="tab" aria-controls="past" aria-selected="false">
                        Past Shows (<span class="past-count"><?php echo count($data['pastTickets']); ?></span>)
                    </button>
                </li>
            <?php endif; ?>
        </ul>

        <!-- Tab Content -->
        <div class="tab-content" id="ticketTabContent">
            
            <!-- Upcoming Tickets Tab -->
            <?php if($data['has_upcoming']): ?>
                <div class="tab-pane fade show active" id="upcoming" role="tabpanel" aria-labelledby="upcoming-tab">
                    <div class="table-responsive">
                        <table class="table table-hover ticket-table" id="upcomingTable">
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
                                <?php foreach ($data['upcomingTickets'] as $ticket): ?>
                                    <tr class="ticket-row" data-genre="<?php echo htmlspecialchars($ticket->genre_name ?? ''); ?>" 
                                        data-show="<?php echo htmlspecialchars(strtolower($ticket->show_title)); ?>"
                                        data-status="<?php echo htmlspecialchars($ticket->status); ?>">
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
                                            <?php if($ticket->status === 'booked'): ?>
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
                        <div id="upcomingNoResults" class="alert alert-info" style="display: none;">
                            No upcoming tickets match your filters.
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Past Tickets Tab -->
            <?php if($data['has_past']): ?>
                <div class="tab-pane fade <?php echo !$data['has_upcoming'] ? 'show active' : ''; ?>" id="past" role="tabpanel" aria-labelledby="past-tab">
                    <div class="table-responsive">
                        <table class="table table-hover ticket-table" id="pastTable">
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
                                <?php foreach ($data['pastTickets'] as $ticket): ?>
                                    <tr class="ticket-row" data-genre="<?php echo htmlspecialchars($ticket->genre_name ?? ''); ?>" 
                                        data-show="<?php echo htmlspecialchars(strtolower($ticket->show_title)); ?>"
                                        data-status="<?php echo htmlspecialchars($ticket->status); ?>"
                                        style="opacity: 0.7;">
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
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                        <div id="pastNoResults" class="alert alert-info" style="display: none;">
                            No past tickets match your filters.
                        </div>
                    </div>
                </div>
            <?php endif; ?>
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

<!-- Filtering Script -->
<script>
function filterTickets() {
    const filterGenre = document.getElementById('filterGenre').value.toLowerCase();
    const filterStatus = document.getElementById('filterStatus').value.toLowerCase();
    const filterShowTitle = document.getElementById('filterShowTitle').value.toLowerCase();
    
    // Filter upcoming tickets
    const upcomingRows = document.querySelectorAll('#upcomingTable .ticket-row');
    let visibleUpcoming = 0;
    upcomingRows.forEach(row => {
        const genre = row.getAttribute('data-genre').toLowerCase();
        const status = row.getAttribute('data-status').toLowerCase();
        const show = row.getAttribute('data-show');
        
        const matchGenre = !filterGenre || genre.includes(filterGenre);
        const matchStatus = !filterStatus || status === filterStatus;
        const matchShow = !filterShowTitle || show.includes(filterShowTitle);
        
        if (matchGenre && matchStatus && matchShow) {
            row.style.display = '';
            visibleUpcoming++;
        } else {
            row.style.display = 'none';
        }
    });
    
    // Show/hide "no results" message for upcoming
    const upcomingNoResults = document.getElementById('upcomingNoResults');
    if (upcomingNoResults) {
        upcomingNoResults.style.display = visibleUpcoming === 0 ? '' : 'none';
    }
    
    // Update upcoming count
    const upcomingCountSpan = document.querySelector('.upcoming-count');
    if (upcomingCountSpan) {
        upcomingCountSpan.textContent = visibleUpcoming;
    }
    
    // Filter past tickets
    const pastRows = document.querySelectorAll('#pastTable .ticket-row');
    let visiblePast = 0;
    pastRows.forEach(row => {
        const genre = row.getAttribute('data-genre').toLowerCase();
        const status = row.getAttribute('data-status').toLowerCase();
        const show = row.getAttribute('data-show');
        
        const matchGenre = !filterGenre || genre.includes(filterGenre);
        const matchStatus = !filterStatus || status === filterStatus;
        const matchShow = !filterShowTitle || show.includes(filterShowTitle);
        
        if (matchGenre && matchStatus && matchShow) {
            row.style.display = '';
            visiblePast++;
        } else {
            row.style.display = 'none';
        }
    });
    
    // Show/hide "no results" message for past
    const pastNoResults = document.getElementById('pastNoResults');
    if (pastNoResults) {
        pastNoResults.style.display = visiblePast === 0 ? '' : 'none';
    }
    
    // Update past count
    const pastCountSpan = document.querySelector('.past-count');
    if (pastCountSpan) {
        pastCountSpan.textContent = visiblePast;
    }
}

function clearFilters() {
    document.getElementById('filterGenre').value = '';
    document.getElementById('filterStatus').value = '';
    document.getElementById('filterShowTitle').value = '';
    filterTickets();
}
</script>

<?php require APPROOT . '/views/includes/footer.php'; ?>
