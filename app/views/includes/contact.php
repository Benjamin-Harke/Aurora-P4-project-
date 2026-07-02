<?php require_once APPROOT . '/views/includes/header.php'; ?>

<section class="contact-page">
    <div class="contact-wrapper">

        <div class="contact-left">
            <h1>Contact</h1>
            <p>Stuur je vraag of feedback naar het theater.</p>

            <p class="flow-text">Huidige flow: <strong><?= $_SESSION['feedback_flow'] ?? 'happy'; ?></strong></p>

            <div class="flow-buttons">
                <a href="<?= URLROOT ?>/contact/happy" class="btn btn-success">Happy</a>
                <a href="<?= URLROOT ?>/contact/unhappy" class="btn btn-danger">Unhappy</a>
            </div>

            <form method="POST" action="<?= URLROOT ?>/contact/opslaan">
                <div class="contact-row">
                    <input type="email" name="email" placeholder="E-mail" required>
                    <input type="text" name="onderwerp" placeholder="Onderwerp" required>
                </div>

                <textarea name="bericht" placeholder="Feedback" maxlength="250" required></textarea>

                <label class="privacy-check">
                    <input type="checkbox" required>
                    <span>Ik ga akkoord met de privacyvoorwaarden.</span>
                </label>

                <button type="submit" class="contact-btn">Verstuur</button>
            </form>
        </div>

        <div class="contact-divider"></div>

        <div class="contact-right">
            <div class="contact-item">
                <img src="<?= URLROOT ?>/img/email.png" alt="Email">
                <span>support@auroratheatre.nl</span>
            </div>

            <div class="contact-item">
                <img src="<?= URLROOT ?>/img/phone.png" alt="Telefoon">
                <span>030 - 1234567</span>
            </div>

            <div class="contact-item">
                <img src="<?= URLROOT ?>/img/location.png" alt="Locatie">
                <span>Theaterplein 1, Utrecht</span>
            </div>

            <div class="contact-item">
                <img src="<?= URLROOT ?>/img/clock.png" alt="Tijd">
                <span>Ma t/m Vr 09:00 - 17:00</span>
            </div>
        </div>

    </div>
</section>

<?php if (isset($_SESSION['feedback_succes'])): ?>
    <script>alert("<?= $_SESSION['feedback_succes']; ?>");</script>
    <?php unset($_SESSION['feedback_succes']); ?>
<?php endif; ?>

<?php if (isset($_SESSION['feedback_fout'])): ?>
    <script>alert("<?= $_SESSION['feedback_fout']; ?>");</script>
    <?php unset($_SESSION['feedback_fout']); ?>
<?php endif; ?>

<?php require_once APPROOT . '/views/includes/footer.php'; ?>