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
                                <option value="<?php echo $perf->id; ?>" 
                                    <?php echo isset($data['selected_performance']) && $data['selected_performance'] == $perf->id ? 'selected' : ''; ?>>
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
                        <li>The barcode will appear in the input field</li>
                        <li>Press Enter to validate and scan</li>
                        <li>Look for success confirmation</li>
                        <li>Scan next ticket</li>
                    </ol>
                </div>
            </div>

            <div class="card">
                <div class="card-header bg-warning text-dark">
                    <h6 class="mb-0">⚠️ Important</h6>
                </div>
                <div class="card-body small">
                    <ul class="mb-0">
                        <li>Each ticket can only be scanned once</li>
                        <li>Verify performance matches ticket</li>
                        <li>Report duplicate scans immediately</li>
                        <li>Check customer name carefully</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    #barcode_input {
        font-size: 1.5rem;
        font-family: monospace;
        font-weight: bold;
    }

    #barcode_input:focus {
        border-color: #0d6efd;
        box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
    }

    .alert-success {
        background-color: #d4edda;
        border-color: #c3e6cb;
        color: #155724;
    }

    .alert-danger {
        background-color: #f8d7da;
        border-color: #f5c6cb;
        color: #721c24;
    }

    .table-hover tbody tr:hover {
        background-color: #f5f5f5;
    }
</style>

<script>
    const URLROOT = '<?php echo URLROOT; ?>';
    let recentScans = [];

    // Performance changed
    function performanceChanged() {
        const performanceId = document.getElementById('performance_id').value;
        if (!performanceId) {
            resetStats();
            return;
        }
        loadStats(performanceId);
        document.getElementById('barcode_input').focus();
    }

    // Load statistics
    async function loadStats(performanceId) {
        try {
            const response = await fetch(URLROOT + '/ticketscanning/stats', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ performance_id: performanceId })
            });

            const data = await response.json();
            if (data.success) {
                document.getElementById('stat_total').textContent = data.total_tickets;
                document.getElementById('stat_scanned').textContent = data.scanned_tickets;
                document.getElementById('stat_remaining').textContent = data.remaining_tickets;
            }
        } catch (error) {
            console.error('Error loading stats:', error);
        }
    }

    // Reset statistics
    function resetStats() {
        document.getElementById('stat_total').textContent = '-';
        document.getElementById('stat_scanned').textContent = '-';
        document.getElementById('stat_remaining').textContent = '-';
    }

    // Handle barcode scan
    document.getElementById('barcode_input').addEventListener('keypress', async function (e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            await scanTicket();
        }
    });

    async function scanTicket() {
        const performanceId = document.getElementById('performance_id').value;
        const barcode = document.getElementById('barcode_input').value.trim();

        if (!performanceId) {
            showResult(false, 'Please select a performance first');
            return;
        }

        if (!barcode) {
            showResult(false, 'Barcode cannot be empty');
            return;
        }

        try {
            const response = await fetch(URLROOT + '/ticketscanning/validate', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    barcode: barcode,
                    performance_id: parseInt(performanceId)
                })
            });

            const data = await response.json();

            if (data.success) {
                showResult(true, data.message, data);
                addRecentScan(data.ticket_owner, data.ticket_number, 'success');
                document.getElementById('barcode_input').value = '';
                document.getElementById('barcode_input').focus();
                // Refresh stats
                loadStats(performanceId);
            } else {
                showResult(false, data.message, data);
                addRecentScan(data.ticket_owner || 'Unknown', '-', 'error');
                document.getElementById('barcode_input').value = '';
                document.getElementById('barcode_input').focus();
            }
        } catch (error) {
            showResult(false, 'Error: ' + error.message);
            document.getElementById('barcode_input').focus();
        }
    }

    function showResult(success, message, data = {}) {
        const resultDiv = document.getElementById('scan_result');
        const alertDiv = document.getElementById('result_alert');
        const messageDiv = document.getElementById('result_message');
        const detailsDiv = document.getElementById('result_details');

        alertDiv.className = 'alert ' + (success ? 'alert-success' : 'alert-danger');
        messageDiv.innerHTML = '<strong>' + (success ? '✓ Success!' : '✗ Failed') + '</strong> ' + message;

        let detailsHtml = '';
        if (success && data.ticket_owner) {
            detailsHtml = `
                <div>
                    <strong>Customer:</strong> ${data.ticket_owner}<br>
                    <strong>Ticket #:</strong> ${data.ticket_number}<br>
                    <strong>Price:</strong> €${data.ticket_price}
                </div>
            `;
        } else if (!success && data.ticket_owner) {
            detailsHtml = `<div><strong>Customer:</strong> ${data.ticket_owner}</div>`;
        }
        detailsDiv.innerHTML = detailsHtml;

        resultDiv.style.display = 'block';

        // Auto-hide success message after 3 seconds
        if (success) {
            setTimeout(() => {
                resultDiv.style.display = 'none';
            }, 3000);
        }
    }

    function addRecentScan(customer, ticketNumber, status) {
        const now = new Date();
        const time = now.toLocaleTimeString();

        recentScans.unshift({
            time: time,
            ticket: ticketNumber,
            customer: customer,
            status: status
        });

        // Keep only last 20 scans
        if (recentScans.length > 20) {
            recentScans.pop();
        }

        updateScansTable();
    }

    function updateScansTable() {
        const tbody = document.getElementById('scans_tbody');

        if (recentScans.length === 0) {
            tbody.innerHTML = '<tr><td colspan="4" class="text-center text-muted">No scans yet</td></tr>';
            return;
        }

        tbody.innerHTML = recentScans.map(scan => `
            <tr>
                <td><small>${scan.time}</small></td>
                <td><code>${scan.ticket}</code></td>
                <td>${scan.customer}</td>
                <td>
                    <span class="badge ${scan.status === 'success' ? 'bg-success' : 'bg-danger'}">
                        ${scan.status === 'success' ? 'Scanned' : 'Error'}
                    </span>
                </td>
            </tr>
        `).join('');
    }

    // Focus on input on page load
    window.addEventListener('load', () => {
        document.getElementById('barcode_input').focus();
    });
</script>

<?php require APPROOT . '/views/includes/footer.php'; ?>
