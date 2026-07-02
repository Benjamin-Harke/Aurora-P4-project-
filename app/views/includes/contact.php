<?php require_once APPROOT . '/views/includes/header.php'; ?>

<div class="container py-5">
    <h1>Contact</h1>
    <p>Stuur je vraag, opmerking of feedback naar het theater.</p>

    <p>
        Huidige flow:
        <strong><?= $_SESSION['feedback_flow'] ?? 'happy'; ?></strong>
    </p>

    <a href="<?= URLROOT ?>/contact/happy" class="btn btn-success">Happy</a>
    <a href="<?= URLROOT ?>/contact/unhappy" class="btn btn-danger">Unhappy</a>

    <form method="POST" action="<?= URLROOT ?>/contact/opslaan" class="mt-4">
        <div class="mb-3">
            <label>Naam</label>
            <input type="text" name="naam" class="form-control" required>
        </div>

        <div class="mb-3">
            <label>E-mail</label>
            <input type="email" name="email" class="form-control" required>
        </div>

        <div class="mb-3">
            <label>Onderwerp</label>
            <input type="text" name="onderwerp" class="form-control" required>
        </div>

        <div class="mb-3">
            <label>Bericht</label>
            <textarea name="bericht" class="form-control" maxlength="250" required></textarea>
        </div>

        <div class="mb-3">
            <label>Opmerking</label>
            <textarea name="opmerking" class="form-control" maxlength="250"></textarea>
        </div>

        <button type="submit" class="btn btn-primary">Versturen</button>
    </form>
</div>

<?php if (isset($_SESSION['feedback_succes'])): ?>
    <script>alert("<?= $_SESSION['feedback_succes']; ?>");</script>
    <?php unset($_SESSION['feedback_succes']); ?>
<?php endif; ?>

<?php if (isset($_SESSION['feedback_fout'])): ?>
    <script>alert("<?= $_SESSION['feedback_fout']; ?>");</script>
    <?php unset($_SESSION['feedback_fout']); ?>
<?php endif; ?>

<?php require_once APPROOT . '/views/includes/footer.php'; ?>