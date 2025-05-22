<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>TSCHI DMS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous">
    <link href="https://fonts.googleapis.com/css2?family=Lato&display=swap" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="styles.css">
  </head>
  <body>
    <?php
      error_reporting(1);
      session_start();
      include('nav.php');
      nav();
    ?>

    <section class="hero-section d-flex align-items-center justify-content-center">
      <div class="text-center">
        <h1 class="fw-bold display-5">Tanauan School of Craftsmanship and Home Industries<br>Document Management System</h1>
        <p class="lead">Effortlessly Store, Manage, and Retrieve School Documents.</p>
        <div class="mt-3">
          <a class="btn login-btn btn-outline-light fw-bold me-2" data-bs-toggle="modal" data-bs-target="#login-pop-up">Login now</a>
          <a class="btn sign-up-btn fw-bold" data-bs-toggle="modal" data-bs-target="#sign-up-pop-up">Sign up now</a>
        </div>
      </div>
    </section>
    <?php
      include('login.php');
      include('sign-up.php');
    ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js" integrity="sha384-j1CDi7MgGQ12Z7Qab0qlWQ/Qqz24Gc6BM0thvEMVjHnfYGF0rmFCozFSxQBxwHKO" crossorigin="anonymous"></script>
  </body>
</html>