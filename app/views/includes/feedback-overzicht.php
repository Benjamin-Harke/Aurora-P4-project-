<?php require_once APPROOT . '/views/includes/header.php'; ?>

<section class="feedback-overzicht-page">
    <div class="feedback-overzicht-container">
        <h1>Ontvangen feedback</h1>
        <p>Hier zie je alle feedbackberichten die via de contactpagina zijn verstuurd.</p>

        <?php if (empty($data['feedback'])): ?>
            <div class="feedback-empty">
                Er is nog geen feedback ontvangen.
            </div>
        <?php else: ?>
            <div class="feedback-actions">
                <a href="<?= URLROOT; ?>/contact/overzicht" class="refresh-btn"> <!--Refresh knop--->
                    <i class="bi bi-arrow-clockwise"></i>
                    Vernieuwen
                </a>
            </div>
            <table class="feedback-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>E-mail</th>
                        <th>Onderwerp</th>
                        <th>Bericht</th>
                        <th>Datum</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($data['feedback'] as $item): ?>
                        <tr>
                            <td><?= $item->id; ?></td>
                            <td><?= $item->email; ?></td>
                            <td><?= $item->onderwerp; ?></td>
                            <td><?= $item->bericht; ?></td>
                            <td><?= $item->datum_aangemaakt; ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</section>

<?php require_once APPROOT . '/views/includes/footer.php'; ?>