<?php
/** @var array $data */
require APPROOT . '/views/includes/header.php'; ?>

<div class="container-fluid py-5">
  <div class="row justify-content-center">
    <div class="col-xl-7 col-lg-9">

      <a href="<?= URLROOT; ?>/admintickets/dashboard" class="btn btn-secondary mb-4">
        <i class="bi bi-arrow-left me-1"></i> Terug naar Dashboard
      </a>

      <div class="card shadow-sm">
        <div class="card-header bg-dark text-white d-flex align-items-center gap-2">
          <i class="bi bi-ticket-detailed fs-5"></i>
          <h5 class="mb-0">Nieuw Ticket Toevoegen</h5>
        </div>

        <div class="card-body p-4">

          <?php if (!empty($data['errors'])): ?>
            <div class="alert alert-danger" role="alert" id="errorBox">
              <i class="bi bi-exclamation-triangle-fill me-2"></i>
              <?php if (count($data['errors']) === 1): ?>
                <?= htmlspecialchars($data['errors'][0]); ?>
              <?php else: ?>
                <ul class="mb-0 mt-1">
                  <?php foreach ($data['errors'] as $err): ?>
                    <li><?= htmlspecialchars($err); ?></li>
                  <?php endforeach; ?>
                </ul>
              <?php endif; ?>
            </div>
          <?php endif; ?>

          <form method="POST" action="<?= URLROOT; ?>/admintickets/create" novalidate>

            <!-- ── Voorstelling ──────────────────────────────────── -->
            <div class="mb-3">
              <label for="voorstelling_id" class="form-label fw-semibold">
                Voorstelling <span class="text-danger">*</span>
              </label>
              <select class="form-select" id="voorstelling_id" name="voorstelling_id" required>
                <option value="">— Selecteer een voorstelling —</option>
                <?php foreach ($data['performances'] as $perf): ?>
                  <option value="<?= $perf->id; ?>"
                          data-max="<?= (int)$perf->max_aantal_tickets; ?>"
                    <?= ($data['post']['voorstelling_id'] ?? '') == $perf->id ? 'selected' : ''; ?>>
                    <?= htmlspecialchars($perf->naam); ?>
                    — <?= date('d M Y', strtotime($perf->datum)); ?>
                    <?= date('H:i', strtotime($perf->tijd)); ?>
                  </option>
                <?php endforeach; ?>
              </select>
            </div>

            <!-- ── Live beschikbaarheid panel ───────────────────── -->
            <div id="seatInfoPanel" class="mb-3 d-none">
              <div class="alert alert-info py-2 mb-0 small">
                <i class="bi bi-info-circle me-1"></i>
                <strong>Stoelcapaciteit:</strong> <span id="totalSeats"></span>
                &nbsp;|&nbsp;
                <strong class="text-success">Beschikbaar:</strong>
                <span id="availableSeats" class="text-success fw-bold"></span>
                &nbsp;|&nbsp;
                <strong class="text-danger">Al geboekt:</strong>
                <span id="takenSeatNrs" class="text-danger"></span>
              </div>
            </div>

            <!-- ── Bezoeker ──────────────────────────────────────── -->
            <div class="mb-3">
              <label for="bezoeker_id" class="form-label fw-semibold">
                Bezoeker <span class="text-danger">*</span>
              </label>
              <select class="form-select" id="bezoeker_id" name="bezoeker_id" required>
                <option value="">— Selecteer een bezoeker —</option>
                <?php foreach ($data['bezoekers'] as $b): ?>
                  <option value="<?= $b->id; ?>"
                    <?= ($data['post']['bezoeker_id'] ?? '') == $b->id ? 'selected' : ''; ?>>
                    <?php
                      $naam = $b->voornaam;
                      if (!empty($b->tussenvoegsel)) $naam .= ' ' . $b->tussenvoegsel;
                      $naam .= ' ' . $b->achternaam;
                      echo htmlspecialchars($naam);
                    ?>
                    &nbsp;(Rel: <?= $b->relatienummer; ?>)
                  </option>
                <?php endforeach; ?>
              </select>
            </div>

            <!-- ── Stoelnummer ───────────────────────────────────── -->
            <div class="mb-3">
              <label for="stoelnummer" class="form-label fw-semibold">
                Stoelnummer <span class="text-danger">*</span>
              </label>
              <input type="number" class="form-control" id="stoelnummer"
                     name="stoelnummer" min="1" required
                     placeholder="bijv. 42"
                     value="<?= htmlspecialchars($data['post']['stoelnummer'] ?? ''); ?>">

              <!-- Live client-side warning (before submit) -->
              <div id="seatTakenWarning" class="alert alert-warning py-2 mt-2 small d-none">
                <i class="bi bi-exclamation-triangle me-1"></i>
                Dit stoelnummer is al geboekt voor de geselecteerde voorstelling.
              </div>
            </div>

            <!-- ── Tarief ────────────────────────────────────────── -->
            <div class="mb-4">
              <label for="prijs_id" class="form-label fw-semibold">
                Tarief <span class="text-danger">*</span>
              </label>
              <select class="form-select" id="prijs_id" name="prijs_id" required>
                <option value="">— Selecteer een tarief —</option>
                <?php foreach ($data['prijzen'] as $prijs): ?>
                  <option value="<?= $prijs->id; ?>"
                    <?= ($data['post']['prijs_id'] ?? '') == $prijs->id ? 'selected' : ''; ?>>
                    &euro;<?= number_format($prijs->tarief, 2); ?>
                  </option>
                <?php endforeach; ?>
              </select>
            </div>

            <hr>

            <div class="d-flex gap-2">
              <button type="submit" class="btn btn-success btn-lg" id="submitBtn">
                <i class="bi bi-check-circle me-1"></i> Ticket Opslaan
              </button>
              <a href="<?= URLROOT; ?>/admintickets/dashboard" class="btn btn-secondary btn-lg">
                Annuleren
              </a>
            </div>

          </form>
        </div><!-- /card-body -->
      </div><!-- /card -->

    </div><!-- /col -->
  </div><!-- /row -->
</div><!-- /container -->

<script>
(function () {
  const voorstellingSelect = document.getElementById('voorstelling_id');
  const stoelnummerInput   = document.getElementById('stoelnummer');
  const seatInfoPanel      = document.getElementById('seatInfoPanel');
  const seatTakenWarning   = document.getElementById('seatTakenWarning');
  const totalSeatsEl       = document.getElementById('totalSeats');
  const availableSeatsEl   = document.getElementById('availableSeats');
  const takenSeatNrsEl     = document.getElementById('takenSeatNrs');
  const submitBtn          = document.getElementById('submitBtn');

  let takenSeats  = [];
  let totalSeats  = 0;

  // ── Fetch seat info when performance changes ──────────────
  voorstellingSelect.addEventListener('change', function () {
    const id = this.value;
    if (!id) {
      seatInfoPanel.classList.add('d-none');
      takenSeats = [];
      totalSeats = 0;
      stoelnummerInput.removeAttribute('max');
      checkSeat();
      return;
    }

    fetch('<?= URLROOT; ?>/admintickets/getSeats/' + id)
      .then(r => r.json())
      .then(d => {
        takenSeats = (d.taken || []).map(Number);
        totalSeats = d.total || 0;

        totalSeatsEl.textContent     = totalSeats;
        availableSeatsEl.textContent = totalSeats - takenSeats.length;
        takenSeatNrsEl.textContent   = takenSeats.length > 0
          ? takenSeats.sort((a, b) => a - b).join(', ')
          : 'Geen';

        stoelnummerInput.max = totalSeats;
        seatInfoPanel.classList.remove('d-none');
        checkSeat();
      })
      .catch(() => seatInfoPanel.classList.add('d-none'));
  });

  // ── Live check on seat input ──────────────────────────────
  stoelnummerInput.addEventListener('input', checkSeat);

  function checkSeat() {
    const val = parseInt(stoelnummerInput.value, 10);
    if (val && takenSeats.includes(val)) {
      seatTakenWarning.classList.remove('d-none');
      submitBtn.classList.add('btn-danger');
      submitBtn.classList.remove('btn-success');
    } else {
      seatTakenWarning.classList.add('d-none');
      submitBtn.classList.add('btn-success');
      submitBtn.classList.remove('btn-danger');
    }
  }

  // ── If performance was pre-selected (error repopulation) ──
  if (voorstellingSelect.value) {
    voorstellingSelect.dispatchEvent(new Event('change'));
  }
})();
</script>

<?php require APPROOT . '/views/includes/footer.php'; ?>
