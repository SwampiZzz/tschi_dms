<?php
    error_reporting(1);
    session_start();
    $conn = mysqli_connect('localhost', 'root', '', 'tschi_dms');
    
    // LOGIN ==========
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
                $_SESSION['role'] = $login['usertype_id'];

                // Fetch first name from profile
                $profile = mysqli_fetch_assoc(mysqli_query($conn, "SELECT first_name FROM profile WHERE login_id = {$login['id']}"));
                $_SESSION['first_name'] = $profile['first_name'] ?? 'User';

                // Role-based redirect
                if ($login['usertype_id'] == 1) {
                    header("Location:index.php?nav=admin-dashboard");
                } 
                elseif ($login['usertype_id'] == 2) {
                    header("Location:index.php?nav=my-files");
                } 
                else {
                    header("Location:index.php?nav=my-files");
                }            
                exit();
            } else {
                $_SESSION['login_error'] = "Incorrect password.";
            }                       
        } else {
            $_SESSION['login_error'] = "Email not found.";
        } 
        header("Location:index.php?nav=home");
        exit();
    }

    // REGISTER ==========
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['register'])) {
        $first_name = trim($_POST['first_name']);
        $middle_name = trim($_POST['middle_name']);
        $last_name = trim($_POST['last_name']);
        $email = trim($_POST['email']);
        $password = $_POST['password'];
        $confirm_password = $_POST['confirm_password'];
    
        // Validation
        if ($password !== $confirm_password) {
            $_SESSION['register_error'] = "Passwords do not match.";
            header("Location: index.php?nav=home");
            exit();
        }
    
        // Check if email exists
        $check = $conn->prepare("SELECT * FROM login WHERE email = ?");
        $check->bind_param("s", $email);
        $check->execute();
        $res = $check->get_result();
    
        if ($res->num_rows > 0) {
            $_SESSION['register_error'] = "Email already registered.";
            header("Location: index.php?nav=home");
            exit();
        }
    
        // Password hash
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    
        // Insert into login
        $insert_login = $conn->prepare("INSERT INTO login (email, password, usertype_id) VALUES (?, ?, ?)");
        $usertype_id = 3; // default user
        $insert_login->bind_param("ssi", $email, $hashed_password, $usertype_id);
        $insert_login->execute();
        $login_id = $insert_login->insert_id;
    
        // Insert into profile
        $insert_profile = $conn->prepare("INSERT INTO profile (first_name, middle_name, last_name, login_id) VALUES (?, ?, ?, ?)");
        $insert_profile->bind_param("sssi", $first_name, $middle_name, $last_name, $login_id);
        $insert_profile->execute();
    
        $_SESSION['register_success'] = "Account created successfully. You can now log in.";
        header("Location: index.php?nav=home");
        exit();
    }
    
    
?>