<?php
session_start();
include('config.php');

// Block access for admins
if ($_SESSION['role'] != 2 && $_SESSION['role'] != 3) {
    header("Location: index.php?nav=home");
    exit();
}

// Fetch available categories
$categories = mysqli_query($conn, "SELECT * FROM category");

?>

<div class="container my-5">
    <h3 class="mb-4">Upload New File</h3>

    <?php if (isset($_SESSION['upload_success'])): ?>
        <div class="alert alert-success"><?= $_SESSION['upload_success']; unset($_SESSION['upload_success']); ?></div>
    <?php endif; ?>
    <?php if (isset($_SESSION['upload_error'])): ?>
        <div class="alert alert-danger"><?= $_SESSION['upload_error']; unset($_SESSION['upload_error']); ?></div>
    <?php endif; ?>
    <form action="config.php" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="upload" value="1"> <!-- Add this hidden input to identify the upload action -->

        <div class="mb-3">
            <label for="file_name" class="form-label">File Name</label>
            <input type="text" name="name" class="form-control" id="file_name" required>
        </div>

        <div class="mb-3">
            <label for="desc" class="form-label">File Description</label>
            <textarea name="description" class="form-control" rows="3" required></textarea>
        </div>

        <div class="mb-3">
            <label for="category" class="form-label">Select Category</label>
            <select name="category_id" class="form-select" required>
                <option value="">-- Select --</option>
                <?php while ($cat = mysqli_fetch_assoc($categories)): ?>
                    <option value="<?= $cat['id'] ?>"><?= $cat['name'] ?></option>
                <?php endwhile; ?>
            </select>
        </div>

        <div class="mb-4">
            <label class="form-label">Upload PDF File</label>
            <input type="file" name="pdf_file" accept="application/pdf" class="form-control" required>
        </div>

        <button type="submit" class="btn btn-success w-100">Submit File</button>
    </form>
</div>
