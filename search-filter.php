<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php?nav=home");
    exit();
}

$search = trim($_GET['search'] ?? '');
$status = $_GET['status'] ?? '';
$category = $_GET['category'] ?? '';

$filter_sql = "WHERE file.login_id = $login_id";

if ($search !== '') {
    $safe_search = mysqli_real_escape_string($conn, $search);
    $filter_sql .= " AND file.name LIKE '%$safe_search%'";
}

if (in_array($status, ['1', '2', '3'])) {
    $filter_sql .= " AND file.status_id = $status";
}

if (is_numeric($category) && $category > 0) {
    $filter_sql .= " AND file.category_id = $category";
}

$result = mysqli_query($conn, "
    SELECT file.*, category.name AS category_name, status.name AS status_name
    FROM file
    JOIN category ON file.category_id = category.id
    JOIN status ON file.status_id = status.id
    $filter_sql
    ORDER BY file.upload_date DESC
");

?>
<form method="GET" action="index.php" class="mb-3">
    <input type="hidden" name="nav" value="file-status">

    <div class="row g-2 mb-2">
        <div class="col-md-10">
            <input type="text" name="search" class="form-control" placeholder="Search" value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
        </div>
        <div class="col-md-2">
            <button class="btn btn-success w-100">Search</button>
        </div>
    </div>

    <div class="row g-2">
        <div class="col-md-6">
            <select name="category" class="form-select">
                <option value="">All Categories</option>
                <?php
                $cat_query = mysqli_query($conn, "SELECT * FROM category");
                while ($cat = mysqli_fetch_assoc($cat_query)):
                ?>
                <option value="<?= $cat['id'] ?>" <?= ($_GET['category'] ?? '') == $cat['id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($cat['name']) ?>
                </option>
                <?php endwhile; ?>
            </select>
        </div>
    
        <div class="col-md-4">
        <select name="status" class="form-select">
            <option value="">All Status</option>
            <option value="1" <?= ($_GET['status'] ?? '') == '1' ? 'selected' : '' ?>>Pending</option>
            <option value="2" <?= ($_GET['status'] ?? '') == '2' ? 'selected' : '' ?>>Approved</option>
            <option value="3" <?= ($_GET['status'] ?? '') == '3' ? 'selected' : '' ?>>Rejected</option>
        </select>
        </div>

        <div class="col-md-2">
            <button class="btn btn-primary w-100">Apply Filters</button>
        </div>
    </div>
</form>