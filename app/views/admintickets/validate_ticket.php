<?php 
/**
 * Ticket Validation Dashboard
 * Admins can use this to validate tickets by their code
 */
require APPROOT . '/views/includes/header.php'; ?>

<div class="container py-5">
    <a href="<?= URLROOT; ?>/admintickets/dashboard" class="btn btn-secondary mb-4">← Back to Dashboard</a>

    <div class="row">
        <div class="col-lg-8 mx-auto">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0"><i class="bi bi-search"></i> Ticket Validation Tool</h4>
                </div>
                <div class="card-body">
                    <p class="text-muted mb-4">Enter a ticket validation code to check if it's valid and hasn't been scanned yet.</p>

                    <!-- Validation Form -->
                    <form id="validationForm" onsubmit="validateTicket(event)">
                        <div class="input-group input-group-lg mb-3">
                            <input 
                                type="text" 
                                class="form-control" 
                                id="validationCode" 
                                placeholder="Enter ticket code (e.g., 00001)" 
                                maxlength="5"
                                pattern="[0-9]{5}"
                                required
                                autofocus
                            >
                            <button class="btn btn-primary" type="submit">
                                <i class="bi bi-check-circle"></i> Validate
                            </button>
                        </div>
                        <small class="text-muted">Format: 5-digit code (e.g., 00001, 00042)</small>
                    </form>

                    <hr>

                    <!-- Result Section -->
                    <div id="resultSection" style="display: none;">
                        <div id="resultAlert" class="alert" role="alert"></div>

                        <div id="resultDetails" class="mt-4">
                            <div class="row">
                                <div class="col-md-6">
                                    <h6><small class="text-muted">TICKET ID</small></h6>
                                    <p id="resultTicketId" class="lead">-</p>
                                </div>
                                <div class="col-md-6">
                                    <h6><small class="text-muted">VALIDATION CODE</small></h6>
                                    <p id="resultCode" class="lead" style="font-family: monospace; color: #00d9ff;">-</p>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <h6><small class="text-muted">PERFORMANCE</small></h6>
                                    <p id="resultPerformance">-</p>
                                </div>
                                <div class="col-md-6">
                                    <h6><small class="text-muted">DATE</small></h6>
                                    <p id="resultDate">-</p>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <h6><small class="text-muted">SEAT NUMBER</small></h6>
                                    <p id="resultSeat" class="lead">-</p>
                                </div>
                                <div class="col-md-6">
                                    <h6><small class="text-muted">STATUS</small></h6>
                                    <p id="resultStatus">
                                        <span id="resultStatusBadge" class="badge">-</span>
                                    </p>
                                </div>
                            </div>

                            <hr>

                            <div class="row">
                                <div class="col-12">
                                    <h6><small class="text-muted">VALIDATION MESSAGE</small></h6>
                                    <p id="resultMessage" class="alert alert-info mb-0">-</p>
                                </div>
                            </div>
                        </div>

                        <div class="mt-4">
                            <button class="btn btn-outline-primary" onclick="resetForm()">
                                <i class="bi bi-arrow-counterclockwise"></i> Check Another Ticket
                            </button>
                        </div>
                    </div>

                    <!-- Info Box -->
                    <div class="alert alert-info mt-5">
                        <h6><i class="bi bi-info-circle"></i> How it works</h6>
                        <ul class="mb-0 small">
                            <li>Each ticket has a unique 5-digit validation code</li>
                            <li>Enter the code above to check if the ticket is valid</li>
                            <li>Valid tickets show status "booked" or "reserved"</li>
                            <li>Already scanned tickets show status "Gescand"</li>
                            <li>Use this before scanning a ticket to verify its validity</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function validateTicket(event) {
    event.preventDefault();
    
    const code = document.getElementById('validationCode').value.trim();
    
    // Show loading state
    document.getElementById('resultSection').style.display = 'block';
    document.getElementById('resultAlert').innerHTML = '<div class="text-center"><div class="spinner-border spinner-border-sm" role="status"><span class="visually-hidden">Loading...</span></div></div>';
    
    // Make AJAX request
    fetch('<?= URLROOT; ?>/admintickets/validateTicket', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'code=' + encodeURIComponent(code)
    })
    .then(response => response.json())
    .then(data => {
        // Display results
        displayValidationResult(data);
    })
    .catch(error => {
        console.error('Error:', error);
        const alert = document.getElementById('resultAlert');
        alert.className = 'alert alert-danger';
        alert.innerHTML = '<i class="bi bi-exclamation-triangle"></i> Error: Could not validate ticket';
    });
}

function displayValidationResult(data) {
    const resultSection = document.getElementById('resultSection');
    const resultAlert = document.getElementById('resultAlert');
    
    // Set alert styling based on validity
    if (data.valid && !data.scanned) {
        resultAlert.className = 'alert alert-success';
        resultAlert.innerHTML = `<i class="bi bi-check-circle"></i> <strong>✓ Ticket is Valid</strong><br><small>${data.message}</small>`;
    } else if (data.scanned) {
        resultAlert.className = 'alert alert-warning';
        resultAlert.innerHTML = `<i class="bi bi-exclamation-triangle"></i> <strong>⚠ Already Scanned</strong><br><small>${data.message}</small>`;
    } else {
        resultAlert.className = 'alert alert-danger';
        resultAlert.innerHTML = `<i class="bi bi-x-circle"></i> <strong>✗ Invalid Ticket</strong><br><small>${data.message}</small>`;
    }
    
    // Populate result details
    document.getElementById('resultTicketId').textContent = data.ticket_id;
    document.getElementById('resultCode').textContent = data.validation_code;
    document.getElementById('resultPerformance').textContent = data.performance_name;
    document.getElementById('resultDate').textContent = new Date(data.performance_date).toLocaleDateString('en-US', { 
        weekday: 'short', 
        year: 'numeric', 
        month: 'short', 
        day: 'numeric' 
    });
    document.getElementById('resultSeat').textContent = data.seat_number;
    document.getElementById('resultMessage').textContent = data.message;
    
    // Set status badge
    const statusBadge = document.getElementById('resultStatusBadge');
    const statusMap = {
        'booked': 'success',
        'reserved': 'info',
        'Gescand': 'primary',
        'gescand': 'primary',
        'cancelled': 'danger',
        'invalid': 'danger'
    };
    const badgeClass = statusMap[data.status] || 'secondary';
    statusBadge.className = `badge bg-${badgeClass}`;
    statusBadge.textContent = data.status.charAt(0).toUpperCase() + data.status.slice(1);
    
    resultSection.style.display = 'block';
}

function resetForm() {
    document.getElementById('validationForm').reset();
    document.getElementById('resultSection').style.display = 'none';
    document.getElementById('validationCode').focus();
}

// Auto-format input to uppercase
document.getElementById('validationCode').addEventListener('input', function() {
    this.value = this.value.toUpperCase();
});
</script>

<?php require APPROOT . '/views/includes/footer.php'; ?>
