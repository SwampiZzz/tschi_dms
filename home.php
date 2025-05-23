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
<?php if (isset($_SESSION['login_error'])): ?>
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const loginModal = new bootstrap.Modal(document.getElementById('login-pop-up'));
        loginModal.show();
    });
    </script>
<?php unset($_SESSION['login_error']); ?>
<?php endif; ?>
<?php if (isset($_SESSION['register_error']) || isset($_SESSION['register_success'])): ?>
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const signUpModal = new bootstrap.Modal(document.getElementById('sign-up-pop-up'));
        signUpModal.show();
    });
    </script>
<?php unset($_SESSION['register_error']); 
unset($_SESSION['register_success']);?>
<?php endif; ?>
