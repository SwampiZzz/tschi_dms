<?php
$layout = $_SESSION['layout'] ?? 'grid';
if (isset($_GET['layout']) && in_array($_GET['layout'], ['grid', 'table'])) {
    $_SESSION['layout'] = $_GET['layout'];
    $layout = $_GET['layout'];
}
?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <div class="d-flex gap-2">
        <button class="btn <?= $layout === 'grid' ? 'btn-secondary' : 'btn-outline-secondary' ?>" onclick="setLayout('grid')">Grid</button>
        <button class="btn <?= $layout === 'table' ? 'btn-secondary' : 'btn-outline-secondary' ?>" onclick="setLayout('table')">Table</button>
    </div>

    <?php if (isset($_SESSION['delete_temp_success'])): ?>
        <div class="alert alert-success alert-dismissible fade show py-1 mb-0 d-flex align-items-center" role="alert" style="height: 38px;">
            <?= $_SESSION['delete_temp_success']; ?>
            <button type="button" class="btn-close btn-sm ms-2" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php unset($_SESSION['delete_temp_success']); ?>
    <?php endif; ?>
</div>

<?php if (mysqli_num_rows($result) === 0): ?>
    <p class="text-muted">No files found.</p>
<?php elseif ($layout === 'table'): ?>
    <div class="table-responsive">
        <table class="table table-bordered align-middle">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Category</th>
                    <th>Status</th>
                    <th>Upload Date</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
            <?php while ($file = mysqli_fetch_assoc($result)): ?>
                <tr>
                    <td><?= htmlspecialchars($file['name']) ?></td>
                    <td><?= htmlspecialchars($file['category_name']) ?></td>
                    <td>
                        <span class="badge bg-<?= 
                            $file['status_id'] == 1 ? 'warning text-dark' :
                            ($file['status_id'] == 2 ? 'success' : 'danger') ?>">
                            <?= htmlspecialchars($file['status_name']) ?>
                        </span>
                    </td>
                    <td><?= date('M d, Y', strtotime($file['upload_date'])) ?></td>
                    <td class="text-nowrap">
                    <button class="btn btn-sm btn-success" style="cursor:pointer;" onclick="loadViewFile(<?= $file['id'] ?>)">View Details</button>
                        <button class="btn btn-sm btn-primary" onclick="loadEditFile(<?= $file['id'] ?>)">Edit</button>
                        <form method="POST" action="config.php" class="d-inline" onsubmit="return confirm('Delete this file?');">
                            <input type="hidden" name="delete_file_id" value="<?= $file['id'] ?>">
                            <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                        </form>
                    </td>
                </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
    </div>
<?php else: ?>
    <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 g-4">
        <?php mysqli_data_seek($result, 0); while ($file = mysqli_fetch_assoc($result)): ?>
        <div class="col">
            <div class="card h-100 border shadow-sm">
                <div 
                    class="card-body file-card-body position-relative" 
                    style="cursor:pointer; padding-bottom: 3rem;" 
                    onclick="window.open('uploads/<?= htmlspecialchars($file['stored_name']) ?>', '_blank')"
                    title="Click to open file"
                >
                    <h5 class="card-title" title="<?= htmlspecialchars($file['name']) ?>">
                        <?= strlen($file['name']) > 30 ? htmlspecialchars(substr($file['name'], 0, 30)) . '...' : htmlspecialchars($file['name']) ?>
                    </h5>
                    <p class="card-text small mb-1">Category: <?= htmlspecialchars($file['category_name']) ?></p>
                    <p class="card-text small">Uploaded: <?= date('M d, Y', strtotime($file['upload_date'])) ?></p>
                    <span class="badge position-absolute start-0 bottom-0 m-3 bg-<?= 
                        $file['status_id'] == 1 ? 'warning text-dark' : 
                        ($file['status_id'] == 2 ? 'success' : 'danger') ?>">
                        <?= htmlspecialchars($file['status_name']) ?>
                    </span>
                </div>
                <div class="card-footer d-flex justify-content-between gap-1">
                    <!-- INFO button -->
                    <button type="button" class="btn btn-sm btn-outline-secondary" onclick="loadViewFile(<?= $file['id'] ?>)">Info</button>
                    <div class="d-flex justify-content-end gap-1">
                        <!-- EDIT button -->
                        <button type="button" class="btn btn-sm btn-primary" onclick="loadEditFile(<?= $file['id'] ?>)">Edit</button>

                        <!-- DELETE button -->
                        <form method="POST" action="config.php" onsubmit="return confirm('Are you sure you want to delete this file?');">
                            <input type="hidden" name="delete_file_id" value="<?= $file['id'] ?>">
                            <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <?php endwhile; ?>
    </div>
<?php endif; ?>

<script>
function setLayout(type) {
    const params = new URLSearchParams(window.location.search);
    params.set('layout', type);
    window.location.search = params.toString();
}
</script>
