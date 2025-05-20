<?php
    error_reporting(1);
    session_start();
    function nav(){
        ?>
        <nav class="navbar shadow-sm">
            <div class="container">
                <a class="navbar-brand" href="?nav=home">
                    <img src="components/logo.png" alt="TSCHI" width="40" height="40">
                </a>
                <div>
                    <a class="btn login-btn text-white" type="button" data-bs-toggle="modal" data-bs-target="#login-pop-up">Login</a>
                    <button class="btn sign-up-btn me-2" type="button">Sign-Up</button>
                </div>
            </div>
        </nav>
        <?php
    }
?>