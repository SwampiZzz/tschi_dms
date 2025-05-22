<?php
    error_reporting(1);
    session_start();
    function nav(){
        ?>
        <nav class="navbar shadow-sm">
            <div class="container">
                <a class="navbar-brand" href="?nav=home">
                    <img src="elems/logo.png" alt="TSCHI" width="40" height="40">
                </a>
                <div>
                    <button class="btn login-btn btn-outline-light me-2" type="button" data-bs-toggle="modal" data-bs-target="#login-pop-up">Login</button>
                    <button class="btn sign-up-btn me-2" type="button" data-bs-toggle="modal" data-bs-target="#sign-up-pop-up">Sign-Up</button>
                </div>
            </div>
        </nav>
        <?php
    }
?>