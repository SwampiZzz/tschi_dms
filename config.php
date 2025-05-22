<?php
    error_reporting(1);
    session_start();
    $conn = mysqli_connect('localhost', 'root', '', 'tschi_dms');
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
        $email = $_POST['email'];
        $password = $_POST['password'];

        $stmt = $conn->prepare("SELECT * FROM login WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($login = $result->fetch_assoc()) {
            if (password_verify($password, $login['password'])) {
                // Store session data
                $_SESSION['user_id'] = $login['id'];
                $_SESSION['role'] = $login['usertype_id']; // assuming this stores 'admin', 'moderator', or 'user'

                // Fetch first name from profile
                $profile = mysqli_fetch_assoc(mysqli_query($conn, "SELECT first_name FROM profile WHERE login_id = {$login['id']}"));
                $_SESSION['first_name'] = $profile['first_name'] ?? 'User';

                // Role-based redirect
                if ($login['usertype_id'] == 1) { // admin
                    header("Location: admin-dashboard.php");
                } elseif ($login['usertype_id'] == 2) { // moderator
                    header("Location: my-files.php");
                } else {
                    header("Location: my-files.php"); // user
                }            
                exit();
            } else {
                $_SESSION['login_error'] = "Incorrect password.";
            }                       
        } else {
            $_SESSION['login_error'] = "Email not found.";
        } 
        header("Location: index.php");
        exit();
    }
?>