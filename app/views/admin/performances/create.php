<?php require APPROOT . '/views/includes/header.php'; ?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">Add New Theatre Performance</h4>
                    <a href="/publictickets" class="btn btn-sm btn-outline-light">Back</a>
                </div>
                <div class="card-body">
                    <!-- This action must match your Controller Name -->
                    <form action="<?php echo URLROOT; ?>/adminperformances/create" method="POST">
                        
                        <div class="mb-3">
                            <label class="form-label fw-bold">Performance Name (naam)</label>
                            <input type="text" name="naam" class="form-control" placeholder="e.g. The Lion King" value="<?php echo isset($data['naam']) ? htmlspecialchars($data['naam']) : ''; ?>" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Description (beschrijving)</label>
                            <textarea name="beschrijving" class="form-control" rows="3" placeholder="Tell the audience about the show..."><?php echo isset($data['beschrijving']) ? htmlspecialchars($data['beschrijving']) : ''; ?></textarea>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Date (datum)</label>
                                <input type="date" name="datum" class="form-control" value="<?php echo isset($data['datum']) ? htmlspecialchars($data['datum']) : ''; ?>" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Start Time (tijd)</label>
                                <input type="time" name="tijd" class="form-control" value="<?php echo isset($data['tijd']) ? htmlspecialchars($data['tijd']) : ''; ?>" required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Total Seats (max_aantal_tickets)</label>
                                <input type="number" name="max_aantal_tickets" class="form-control" value="<?php echo isset($data['max_aantal_tickets']) ? htmlspecialchars($data['max_aantal_tickets']) : '80'; ?>" min="1" required>
                                <small class="text-muted">Maximum capacity of the hall.</small>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Price (prijs_id)</label>
                                <select name="prijs_id" class="form-select">
                                    <option value="1">Standard Rate (€25.00)</option>
                                    <option value="2">Premium Rate (€45.00)</option>
                                </select>
                            </div>
                        </div>

                        <hr>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary btn-lg">Save Performance</button>
                            <a href="/publictickets" class="btn btn-link text-muted">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require APPROOT . '/views/includes/footer.php'; ?>