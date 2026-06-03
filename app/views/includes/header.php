<!doctype html>
<html lang="nl">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Aurora Theatre</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
  <link rel="stylesheet" href="<?= URLROOT; ?>/css/style.css">
</head>
<body>

<!-- Navigatie -->
<nav class="navbar navbar-expand-lg navbar-dark sticky-top">
  <div class="container">

    <!-- Logo -->
    <a class="navbar-brand" href="<?= URLROOT; ?>">
      <i class="bi bi-masks"></i> Aurora Theatre
    </a>

    <!-- Hamburger menu (mobiel) -->
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>

    <!-- Menu links -->
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav mx-auto">
        <li class="nav-item"><a class="nav-link" href="<?= URLROOT; ?>">Home</a></li>
        <li class="nav-item"><a class="nav-link" href="#">Voorstellingen</a></li>
        <li class="nav-item"><a class="nav-link" href="#">Over ons</a></li>
        <li class="nav-item"><a class="nav-link" href="#">Contact</a></li>
      </ul>

      <!-- Login en Register knoppen -->
      <div class="d-flex gap-2">
        <button class="btn btn-outline-light" data-bs-toggle="modal" data-bs-target="#loginModal">
          <i class="bi bi-box-arrow-in-right"></i> Login
        </button>
        <button class="btn btn-light text-dark" data-bs-toggle="modal" data-bs-target="#registerModal">
          <i class="bi bi-person-plus"></i> Register
        </button>
      </div>
    </div>

  </div>
</nav>