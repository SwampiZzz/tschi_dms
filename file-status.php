<?php
session_start();
if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'], [2, 3])) {
    header("Location: index.php?nav=home");
    exit();
}
include('config.php');
$login_id = $_SESSION['user_id'];
$result = mysqli_query($conn, "
    SELECT file.*, file.stored_name, category.name AS category_name, status.name AS status_name 
    FROM file 
    JOIN category ON file.category_id = category.id
    JOIN status ON file.status_id = status.id
    WHERE file.login_id = $login_id
    ORDER BY file.upload_date DESC
");
?>
<div class="container my-4">
    <h3 class="mb-2">File Status Tracker</h3>
    <?php if (isset($_SESSION['delete_temp_success'])): ?>
        <div class="alert alert-success">
            <?= $_SESSION['delete_temp_success']; unset($_SESSION['delete_temp_success']); ?>
        </div>
    <?php endif; ?>
    <hr class="mb-4">
    <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 g-4">
        <?php while ($file = mysqli_fetch_assoc($result)): ?>
            <div class="col">
                <div class="card h-100 border border-tertiary shadow-sm">
                    <div class="card-body position-relative" style="padding-bottom: 3rem;">
                        <h5 class="card-title" title="<?= htmlspecialchars($file['name']) ?>">
                            <?= strlen($file['name']) > 30 ? htmlspecialchars(substr($file['name'], 0, 30)) . '...' : htmlspecialchars($file['name']) ?>
                        </h5>
                        <p class="card-text small mb-1">Category: <?= htmlspecialchars($file['category_name']) ?></p>
                        <p class="card-text small">Uploaded: <?= date('M d, Y', strtotime($file['upload_date'])) ?></p>
                        <span class="badge position-absolute start-0 bottom-0 m-3 bg-<?= 
                            $file['status_id'] == 1 ? 'warning' : 
                            ($file['status_id'] == 2 ? 'success' : 'danger')
                        ?> text-dark">
                            <?= htmlspecialchars($file['status_name']) ?>
                        </span>
                    </div>
                    <div class="card-footer d-flex justify-content-between">
                        <a href="uploads/<?= htmlspecialchars($file['stored_name']) ?>" target="_blank" class="btn btn-sm btn-outline-primary">View</a>
                        <form method="POST" action="config.php" onsubmit="return confirm('Are you sure you want to delete this file?');">
                            <input type="hidden" name="delete_file_id" value="<?= $file['id'] ?>">
                            <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                        </form>
                    </div>
                </div>
            </div>
        <?php endwhile; ?>
    </div>
</div>
