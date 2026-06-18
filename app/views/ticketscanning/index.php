<?php
require_once APPROOT . '/views/includes/header.php';
require_once APPROOT . '/views/includes/messages.php';
?>

<div class="container py-5">
    <h1 class="mb-4">🎫 Ticket Scanner - Entry Control</h1>

    <!-- Performance Selection -->
    <div class="row mb-5">
        <div class="col-lg-8">
            <div class="card border-primary">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Select Performance</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label for="performance_id" class="form-label">Choose a performance:</label>
                        <select class="form-select form-select-lg" id="performance_id" onchange="performanceChanged()">
                            <option value="">-- Select Performance --</option>
                            <?php foreach ($data['performances'] as $perf): ?>
                                <option value="<?php echo $perf->id; ?>">
                                    <?php echo htmlspecialchars($perf->naam); ?> - 
                                    <?php echo date('d M Y H:i', strtotime($perf->datum . ' ' . $perf->tijd)); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistics Card -->
        <div class="col-lg-4">
            <div class="card border-success">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">Statistics</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <small class="text-muted">Total Tickets:</small>
                        <p class="h5 mb-0" id="stat_total">-</p>
                    </div>
                    <div class="mb-3">
                        <small class="text-muted">Scanned:</small>
                        <p class="h5 mb-0 text-success" id="stat_scanned">-</p>
                    </div>
                    <div>
                        <small class="text-muted">Remaining:</small>
                        <p class="h5 mb-0 text-info" id="stat_remaining">-</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scanner Section -->
    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header bg-dark text-white">
                    <h5 class="mb-0">📱 Scan Barcode</h5>
                </div>
                <div class="card-body">
                    <div class="mb-4">
                        <label for="barcode_input" class="form-label">Scan or enter barcode:</label>
                        <input type="text" class="form-control form-control-lg" id="barcode_input" 
                               placeholder="Scan a barcode or paste here..." autofocus>
                        <small class="text-muted">Press Enter to scan</small>
                    </div>

                    <!-- Scan Result Message -->
                    <div id="scan_result" class="mb-4" style="display: none;">
                        <div id="result_alert" class="alert" role="alert">
                            <div id="result_message"></div>
                            <div id="result_details" style="margin-top: 10px;"></div>
                        </div>
                    </div>

                    <!-- Recent Scans Table -->
                    <h6 class="mt-5 mb-3">Recent Scans</h6>
                    <div class="table-responsive">
                        <table class="table table-sm table-hover" id="scans_table">
                            <thead class="table-light">
                                <tr>
                                    <th>Time</th>
                                    <th>Ticket #</th>
                                    <th>Customer</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody id="scans_tbody">
                                <tr>
                                    <td colspan="4" class="text-center text-muted">No scans yet</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Help Panel -->
        <div class="col-lg-4">
            <div class="card mb-3">
                <div class="card-header bg-info text-white">
                    <h6 class="mb-0">💡 How to Use</h6>
                </div>
                <div class="card-body small">
                    <ol class="mb-0">
                        <li>Select the performance from the dropdown</li>
                        <li>Position barcode scanner over the ticket barcode</li>
                        <li>Press Enter to validate and scan</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    #barcode_input { font-size: 1.5rem; font-family: monospace; font-weight: bold; }
    #barcode_input:focus { border-color: #0d6efd; box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25); }
</style>

<script>
    const URLROOT = '<?php echo URLROOT; ?>';
    let recentScans = [];

    function performanceChanged() {
        const performanceId = document.getElementById('performance_id').value;
        if (!performanceId) { resetStats(); return; }
        loadStats(performanceId);
        document.getElementById('barcode_input').focus();
    }

    async function loadStats(performanceId) {
        try {
            const response = await fetch(URLROOT + '/ticketscanning/stats', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ performance_id: performanceId })
            });
            const data = await response.json();
            if (data.success) {
                document.getElementById('stat_total').textContent = data.total_tickets;
                document.getElementById('stat_scanned').textContent = data.scanned_tickets;
                document.getElementById('stat_remaining').textContent = data.remaining_tickets;
            }
        } catch (error) { console.error('Error loading stats:', error); }
    }

    function resetStats() {
        document.getElementById('stat_total').textContent = '-';
        document.getElementById('stat_scanned').textContent = '-';
        document.getElementById('stat_remaining').textContent = '-';
    }

    document.getElementById('barcode_input').addEventListener('keypress', async function (e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            await scanTicket();
        }
    });

    async function scanTicket() {
        const performanceId = document.getElementById('performance_id').value;
        const barcode = document.getElementById('barcode_input').value.trim();

        if (!performanceId) { showResult(false, 'Select a performance'); return; }
        if (!barcode) return;

        try {
            const response = await fetch(URLROOT + '/ticketscanning/validate', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ barcode: barcode, performance_id: parseInt(performanceId) })
            });

            const data = await response.json();
            showResult(data.success, data.message, data);
            addRecentScan(data.ticket_owner || (data.success ? 'Success' : 'Unknown'), barcode, data.success ? 'success' : 'error');
            
            document.getElementById('barcode_input').value = '';
            document.getElementById('barcode_input').focus();
            loadStats(performanceId);
        } catch (error) {
            showResult(false, 'Server Error');
        }
    }

    function showResult(success, message, data = {}) {
        const resultDiv = document.getElementById('scan_result');
        const alertDiv = document.getElementById('result_alert');
        const messageDiv = document.getElementById('result_message');
        const detailsDiv = document.getElementById('result_details');

        alertDiv.className = 'alert ' + (success ? 'alert-success' : 'alert-danger');
        messageDiv.innerHTML = '<strong>' + (success ? '✓' : '✗') + '</strong> ' + message;
        detailsDiv.innerHTML = success ? `Owner: ${data.ticket_owner}` : '';
        resultDiv.style.display = 'block';
        if (success) setTimeout(() => { resultDiv.style.display = 'none'; }, 4000);
    }

    function addRecentScan(customer, ticketNumber, status) {
        const now = new Date();
        recentScans.unshift({ time: now.toLocaleTimeString(), ticket: ticketNumber, customer: customer, status: status });
        if (recentScans.length > 10) recentScans.pop();
        updateScansTable();
    }

    function updateScansTable() {
        const tbody = document.getElementById('scans_tbody');
        tbody.innerHTML = recentScans.map(scan => `
            <tr>
                <td><small>${scan.time}</small></td>
                <td><code>${scan.ticket}</code></td>
                <td>${scan.customer}</td>
                <td><span class="badge ${scan.status === 'success' ? 'bg-success' : 'bg-danger'}">${scan.status}</span></td>
            </tr>
        `).join('');
    }
</script>

<?php require APPROOT . '/views/includes/footer.php'; ?>