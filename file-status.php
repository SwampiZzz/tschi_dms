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
    <h3 class="mb-2">My Files</h3>
    <?php include('search-filter.php'); ?>
    <hr class="mb-4">
    <?php if (isset($_SESSION['delete_temp_success'])): ?>
        <div class="alert alert-success">
            <?= $_SESSION['delete_temp_success']; unset($_SESSION['delete_temp_success']); ?>
        </div>
    <?php endif; ?>
    <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 g-4">
        <?php while ($file = mysqli_fetch_assoc($result)): ?>
            <div class="col">
                <div class="card h-100 border border-tertiary shadow-sm">
                   <div class="card-body position-relative" style="cursor: pointer; padding-bottom: 3rem;" onclick="loadViewFile(<?= $file['id'] ?>)">
                        <h5 class="card-title" title="<?= htmlspecialchars($file['name']) ?>">
                            <?= strlen($file['name']) > 30 ? htmlspecialchars(substr($file['name'], 0, 30)) . '...' : htmlspecialchars($file['name']) ?>
                        </h5>
                        <p class="card-text small mb-1">Category: <?= htmlspecialchars($file['category_name']) ?></p>
                        <p class="card-text small">Uploaded: <?= date('M d, Y', strtotime($file['upload_date'])) ?></p>
                        <span class="badge position-absolute start-0 bottom-0 m-3 bg-<?= 
                            $file['status_id'] == 1 ? 'warning text-dark' : 
                            ($file['status_id'] == 2 ? 'success' : 'danger')
                        ?>">
                            <?= htmlspecialchars($file['status_name']) ?>
                        </span>
                    </div>
                    <div class="card-footer d-flex justify-content-end gap-1">
                        <form method="POST" action="config.php" onsubmit="return confirm('Are you sure you want to delete this file?');">
                            <input type="hidden" name="delete_file_id" value="<?= $file['id'] ?>">
                            <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                        </form>
                        <button type="button" class="btn btn-sm btn-primary" onclick="loadEditFile(<?= $file['id'] ?>)">Edit</button>
                    </div>
                </div>
            </div>
        <?php endwhile; ?>
    </div>
</div>
<?php
include('file-view-edit.php');;
?>