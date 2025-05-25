<?php
session_start();
include('config.php');

$id = intval($_GET['id']);
$login_id = $_SESSION['user_id'];

if ($_SESSION['role'] == 1 || $_SESSION['role'] == 2) {
    // Admin or moderator: can view any file
    $query = $conn->prepare("
        SELECT file.*, 
               category.name AS category_name, 
               status.name AS status_name, 
               profile.first_name, 
               profile.last_name
        FROM file
        JOIN category ON file.category_id = category.id
        JOIN status ON file.status_id = status.id
        JOIN profile ON file.login_id = profile.login_id
        WHERE file.id = ?
    ");
    $query->bind_param("i", $id);
} else {
    // Regular user: can only view their own file
    $query = $conn->prepare("
        SELECT file.*, 
               category.name AS category_name, 
               status.name AS status_name, 
               profile.first_name, 
               profile.last_name
        FROM file
        JOIN category ON file.category_id = category.id
        JOIN status ON file.status_id = status.id
        JOIN profile ON file.login_id = profile.login_id
        WHERE file.id = ? AND file.login_id = ?
    ");
    $query->bind_param("ii", $id, $login_id);
}

$query->execute();
$result = $query->get_result();

if ($file = $result->fetch_assoc()):
?>

<div class="modal-header">
    <h5 class="modal-title">File Information</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
</div>
<div class="modal-body">
    <ul class="list-group list-group-flush">
        <li class="list-group-item"><strong>File Name:</strong> <?= htmlspecialchars($file['name']) ?></li>
        <li class="list-group-item">
            <strong>Description:</strong>
            <div style="max-height: 200px; overflow-y: auto;" class="border rounded mt-1 p-2 bg-light">
                <?= nl2br(htmlspecialchars($file['description'])) ?>
            </div>
        </li>

        <li class="list-group-item"><strong>Category:</strong> <?= htmlspecialchars($file['category_name']) ?></li>
        <li class="list-group-item"><strong>Status:</strong> 
        <span class="badge bg-<?= 
            $file['status_id'] == 1 ? 'warning text-dark' : 
            ($file['status_id'] == 2 ? 'success' : 'danger') ?>">
            <?= htmlspecialchars($file['status_name']) ?>
        </span>
        </li>
        <?php if (!empty($file['remarks'])): ?>
        <li class="list-group-item">
            <strong>Remarks:</strong><br>
            <em><?= nl2br(htmlspecialchars($file['remarks'])) ?></em>
        </li>
        <?php endif; ?>
        <li class="list-group-item"><strong>Uploader:</strong> <?= htmlspecialchars($file['first_name'] . ' ' . $file['last_name']) ?></li>
        <li class="list-group-item"><strong>Upload Date:</strong> <?= date('M d, Y h:i A', strtotime($file['upload_date'])) ?></li>
    </ul>

    <hr>
    <a href="uploads/<?= htmlspecialchars($file['stored_name']) ?>" class="btn btn-outline-primary" target="_blank">
        Open File
    </a>
</div>

<?php else: ?>
<div class="modal-body">
  <p class="text-danger">File not found or you don't have access to view this file.</p>
</div>
<?php 
echo "User ID: " . $_SESSION['user_id'] . "<br>";
echo "Role: " . $_SESSION['role'] . "<br>";
echo "File ID: " . $id . "<br>";
endif; ?>
