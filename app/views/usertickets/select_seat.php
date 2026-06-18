<?php require APPROOT . '/views/includes/header.php'; ?>

<div class="container py-5 text-center">
    <h1>Choose Your Seat</h1>
    <p class="lead"><?php echo $data['performance']->naam; ?></p>
    
    <div class="stage-container mb-5 bg-dark text-white p-2 mx-auto" style="max-width: 500px; border-radius: 0 0 50px 50px;">
        STAGE
    </div>

    <form action="/usertickets/confirmBooking" method="POST">
        <input type="hidden" name="performance_id" value="<?php echo $data['performance']->id; ?>">
        
        <div class="d-flex flex-wrap justify-content-center mx-auto" style="max-width: 600px;">
            <?php for($i = 1; $i <= $data['totalSeats']; $i++): ?>
                <?php $isTaken = in_array($i, $data['takenSeats']); ?>
                
                <div class="m-1">
                    <input type="radio" class="btn-check" name="seat_number" id="seat<?php echo $i; ?>" 
                           value="<?php echo $i; ?>" <?php echo $isTaken ? 'disabled' : ''; ?> required>
                    <label class="btn <?php echo $isTaken ? 'btn-danger' : 'btn-outline-success'; ?>" 
                           for="seat<?php echo $i; ?>" style="width: 45px;">
                        <?php echo $i; ?>
                    </label>
                </div>
            <?php endfor; ?>
        </div>

        <div class="mt-5">
            <a href="/publictickets/performance/<?php echo $data['performance']->id; ?>" class="btn btn-secondary">Cancel</a>
            <button type="submit" class="btn btn-primary btn-lg">Confirm Booking</button>
        </div>
    </form>
</div>

<style>
    .btn-check:disabled + .btn {
        opacity: 0.3;
        cursor: not-allowed;
        text-decoration: line-through;
    }
</style>

<?php require APPROOT . '/views/includes/footer.php'; ?>