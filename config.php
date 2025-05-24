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
                } elseif ($login['usertype_id'] == 2) {
                    header("Location:index.php?nav=my-files");
                } else {
                    header("Location:index.php?nav=my-files");
                } exit();
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

    // ADD CATEGORY ==========
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_category'])) {
        $name = trim($_POST['category_name']);
        // Check for duplicate
        $check = $conn->prepare("SELECT id FROM category WHERE name = ?");
        $check->bind_param("s", $name);
        $check->execute();
        $check_result = $check->get_result();
        if ($check_result->num_rows > 0) {
            $_SESSION['category_error'] = "Category already exists.";
            header("Location: index.php?nav=manage-categories");
            exit();
        }
        // Insert if not duplicate
        $insert = $conn->prepare("INSERT INTO category (name) VALUES (?)");
        $insert->bind_param("s", $name);
        $insert->execute();
        $_SESSION['category_success'] = "Category added successfully.";
        header("Location: index.php?nav=manage-categories");
        exit();
    }

    if (isset($_POST['edit_category_id']) && isset($_POST['new_name'])) {
        $id = $_POST['edit_category_id'];
        $new_name = trim($_POST['new_name']);
        $stmt = $conn->prepare("UPDATE category SET name = ? WHERE id = ?");
        $stmt->bind_param("si", $new_name, $id);
        $stmt->execute();
        $_SESSION['category_success'] = "Category updated successfully.";
        header("Location: index.php?nav=manage-categories");
        exit();
    }
    
    if (isset($_POST['delete_category_id'])) {
        $id = $_POST['delete_category_id'];
        // Check for existing files
        $check = $conn->prepare("SELECT COUNT(*) FROM file WHERE category_id = ?");
        $check->bind_param("i", $id);
        $check->execute();
        $check->bind_result($file_count);
        $check->fetch();
        $check->close();
        if ($file_count > 0) {
            $_SESSION['category_error'] = "Cannot delete a category with files.";
        } else {
            $del = $conn->prepare("DELETE FROM category WHERE id = ?");
            $del->bind_param("i", $id);
            $del->execute();
            $_SESSION['category_success'] = "Category deleted.";
        }
        header("Location: index.php?nav=manage-categories");
        exit();
    }

    // FILE UPLOAD ========== 
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['upload'])) {
        $name = trim($_POST['name']);
        $description = trim($_POST['description']);
        $category_id = intval($_POST['category_id']);
        $user_id = $_SESSION['user_id'];
        $upload_dir = "uploads/";
        // Basic validation
        if (!isset($_FILES['pdf_file']) || $_FILES['pdf_file']['error'] !== UPLOAD_ERR_OK) {
            $_SESSION['upload_error'] = "Error uploading the file.";
            header("Location: index.php?nav=upload");
            exit();
        }
        // File type validation
        $file_type = mime_content_type($_FILES['pdf_file']['tmp_name']);
        if ($file_type !== 'application/pdf') {
            $_SESSION['upload_error'] = "Only PDF files are allowed.";
            header("Location: index.php?nav=upload");
            exit();
        }
        // Ensure uploads directory exists
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }
        // Generate unique file name
        $filename = time() . "_" . basename($_FILES['pdf_file']['name']);
        $target_path = $upload_dir . $filename;
        if (!move_uploaded_file($_FILES['pdf_file']['tmp_name'], $target_path)) {
            $_SESSION['upload_error'] = "Failed to save the uploaded file.";
            header("Location: index.php?nav=upload");
            exit();
        }
        // Insert file into database
        $stmt = $conn->prepare("INSERT INTO file (name, stored_name, description, upload_date, category_id, status_id, login_id) VALUES (?, ?, ?, NOW(), ?, ?, ?)");
        $status_id = 1; // default status
        $stmt->bind_param("sssiii", $name, $filename, $description, $category_id, $status_id, $user_id);

        if ($stmt->execute()) {
            $_SESSION['upload_success'] = "File uploaded successfully!";
        } else {
            $_SESSION['upload_error'] = "Database error. Please try again.";
        }
        header("Location: index.php?nav=file-status");
        exit();
    }

    if (isset($_POST['delete_file_id'])) {
        $file_id = $_POST['delete_file_id'];
        $user_id = $_SESSION['user_id'];
    
        // Get the actual file name first
        $query = $conn->prepare("SELECT stored_name FROM file WHERE id = ? AND login_id = ?");
        $query->bind_param("ii", $file_id, $user_id);
        $query->execute();
        $result = $query->get_result();
    
        if ($file = $result->fetch_assoc()) {
            $file_path = "uploads/" . $file['stored_name'];
            if (file_exists($file_path)) {
                unlink($file_path);
            }
    
            // Now delete from the database
            $conn->query("DELETE FROM file WHERE id = $file_id AND login_id = $user_id");
            $_SESSION['delete_temp_success'] = "File deleted successfully.";
        } else {
            $_SESSION['delete_temp_success'] = "File not found or permission denied.";
        }
    
        header("Location: index.php?nav=file-status");
        exit();
    }
    
    // REVIEW FILES =============
    if (isset($_POST['review_file_id']) && isset($_POST['approve_file'])) {
        $file_id = $_POST['review_file_id'];
        $stmt = $conn->prepare("UPDATE file SET status_id = 2 WHERE id = ?");
        $stmt->bind_param("i", $file_id);
        $stmt->execute();
        $_SESSION['review_success'] = "File approved successfully.";
        header("Location: index.php?nav=review-uploads");
        exit();
    }

    if (isset($_POST['review_file_id']) && isset($_POST['reject_file']) && isset($_POST['rejection_remark'])) {
        $file_id = $_POST['review_file_id'];
        $remark = trim($_POST['rejection_remark']);
        $stmt = $conn->prepare("UPDATE file SET status_id = 3, description = CONCAT(description, ' | Rejection: ', ?) WHERE id = ?");
        $stmt->bind_param("si", $remark, $file_id);
        $stmt->execute();
        $_SESSION['review_success'] = "File rejected with remarks.";
        header("Location: index.php?nav=review-uploads");
        exit();
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_file'])) {
        $file_id = intval($_POST['file_id']);
        $new_name = trim($_POST['new_name']);
        $new_desc = trim($_POST['new_description']);
        $new_cat = intval($_POST['new_category_id']);
        $user_id = $_SESSION['user_id'];
    
        $stmt = $conn->prepare("UPDATE file SET name = ?, description = ?, category_id = ?, status_id = 1 WHERE id = ? AND login_id = ?");
        $stmt->bind_param("ssiii", $new_name, $new_desc, $new_cat, $file_id, $user_id);
        $stmt->execute();
    
        $_SESSION['delete_temp_success'] = "File updated successfully. Status reset to pending.";
        header("Location: index.php?nav=file-status");
        exit();
    }    
?>