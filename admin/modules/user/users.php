<?php
require_once __DIR__ . '/../../../config/config.php';
$adminBase = '/Web/BooknGo/admin'; 
// Pagination config
$limit = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;
$offset = ($page - 1) * $limit;

// Xử lý lọc + tìm kiếm
$keyword = $conn->real_escape_string($_GET['keyword'] ?? '');
$roleFilter = $_GET['role'] ?? '';

// Xử lý sắp xếp
$sortField = $_GET['sortField'] ?? 'user_id';
$sortOrder = $_GET['sortOrder'] ?? 'ASC';
$allowedFields = ['user_id', 'fullname', 'role', 'created_at'];
$allowedOrders = ['ASC', 'DESC'];
if (!in_array($sortField, $allowedFields)) $sortField = 'user_id';
if (!in_array($sortOrder, $allowedOrders)) $sortOrder = 'ASC';

// WHERE conditions
$where = [];
if (!empty($keyword)) {
    $where[] = "(fullname LIKE '%$keyword%' OR email LIKE '%$keyword%' OR phone LIKE '%$keyword%')";
}
if ($roleFilter !== '') {
    $where[] = "role = '$roleFilter'";
}
$whereClause = !empty($where) ? "WHERE " . implode(" AND ", $where) : "";

// Query data
$sql = "SELECT * FROM users $whereClause ORDER BY $sortField $sortOrder LIMIT $limit OFFSET $offset";
$result = $conn->query($sql);

// Đếm tổng số bản ghi
$totalResult = $conn->query("SELECT COUNT(*) AS total FROM users $whereClause");
$totalRecords = $totalResult->fetch_assoc()['total'];
$totalPages = ceil($totalRecords / $limit);

// Hàm icon sort
function sortIcon($field, $currentField, $currentOrder) {
    if ($field !== $currentField) return '⇅';
    return $currentOrder === 'ASC' ? '↑' : '↓';
}

$params = "keyword=" . urlencode($keyword) . "&role=" . urlencode($roleFilter);
$nextOrder = ($sortOrder == 'ASC' ? 'DESC' : 'ASC');
?>

<div id="view-users" class="view">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Quản lý Users</h2>
        <a href="modules/user/create.php" class="btn btn-primary">+ Thêm người dùng</a>
    </div>

    <!-- FORM FILTER -->
    <form method="GET" action="#view-users" class="mb-3 d-flex gap-2" style="max-width: 600px;">
        <input type="text" name="keyword" class="form-control"
               value="<?= htmlspecialchars($keyword) ?>"
               placeholder="Tên, email, số điện thoại...">

        <select name="role" class="form-select">
            <option value="">-- Vai trò --</option>
            <option value="1" <?= ($roleFilter === '1') ? 'selected' : '' ?>>Admin</option>
            <option value="0" <?= ($roleFilter === '0') ? 'selected' : '' ?>>User</option>
        </select>

        <button type="submit" class="btn btn-success">Lọc</button>

        <a href="#view-users" class="btn btn-secondary">Reset</a>
    </form>

    <div class="table-responsive">
        <table class="table table-bordered table-hover align-middle">
            <thead class="table-dark text-center">
                <tr>
                    <th>
                        <a href="?<?= $params ?>&sortField=user_id&sortOrder=<?= $nextOrder ?>#view-users"
                           class="<?= $sortField == 'user_id' ? 'text-warning' : '' ?>">
                            ID <?= sortIcon('user_id', $sortField, $sortOrder) ?>
                        </a>
                    </th>

                    <th>
                        <a href="?<?= $params ?>&sortField=fullname&sortOrder=<?= $nextOrder ?>#view-users"
                           class="<?= $sortField == 'fullname' ? 'text-warning' : '' ?>">
                            Họ tên <?= sortIcon('fullname', $sortField, $sortOrder) ?>
                        </a>
                    </th>

                    <th>Email</th>
                    <th>Số điện thoại</th>
                    <th>CMND/CCCD</th>

                    <th>
                        <a href="?<?= $params ?>&sortField=role&sortOrder=<?= $nextOrder ?>#view-users"
                           class="<?= $sortField == 'role' ? 'text-warning' : '' ?>">
                            Vai trò <?= sortIcon('role', $sortField, $sortOrder) ?>
                        </a>
                    </th>

                    <th>
                        <a href="?<?= $params ?>&sortField=created_at&sortOrder=<?= $nextOrder ?>#view-users"
                           class="<?= $sortField == 'created_at' ? 'text-warning' : '' ?>">
                            Ngày tạo <?= sortIcon('created_at', $sortField, $sortOrder) ?>
                        </a>
                    </th>

                    <th width="150">Hành động</th>
                </tr>
            </thead>

            <tbody>
            <?php if ($result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= $row['user_id'] ?? '' ?></td>
                    <td><?= htmlspecialchars($row['fullname'] ?? '') ?></td>
                    <td><?= htmlspecialchars($row['email'] ?? '') ?></td>
                    <td><?= htmlspecialchars($row['phone'] ?? '') ?></td>
                    <td><?= htmlspecialchars($row['identity_number'] ?? '') ?></td>
                    <td>
                        <span class="badge bg-<?= $row['role']==1 ? 'danger' : 'secondary' ?>">
                            <?= $row['role']==1 ? 'Admin' : 'User' ?>
                        </span>
                    </td>
                    <td><?= $row['created_at']; ?></td>
                    <td>
                       <a href="<?= $adminBase ?>/modules/user/edit.php?id=<?= $row['user_id']; ?>&<?= $params ?>&sortField=<?= $sortField ?>&sortOrder=<?= $sortOrder ?>&page=<?= $page ?>#view-users"
                        class="btn btn-sm btn-warning">Sửa</a>

                     <a href="<?= $adminBase ?>/modules/user/delete.php?id=<?= $row['user_id']; ?>&<?= $params ?>&sortField=<?= $sortField ?>&sortOrder=<?= $sortOrder ?>&page=<?= $page ?>#view-users"
                        onclick="return confirm('Xóa user này?')"
                        class="btn btn-sm btn-danger">Xóa</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="10" class="text-center">Không có dữ liệu</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- PHÂN TRANG -->
    <?php if ($totalPages > 1): ?>
        <nav>
            <ul class="pagination justify-content-center">
                <?php if ($page > 1): ?>
                    <li class="page-item">
                        <a class="page-link"
                           href="?page=<?= $page-1 ?>&<?= $params ?>&sortField=<?= $sortField ?>&sortOrder=<?= $sortOrder ?>#view-users">«</a>
                    </li>
                <?php endif; ?>

                <?php for ($i=1;$i<=$totalPages;$i++): ?>
                    <li class="page-item <?= ($i==$page?'active':'') ?>">
                        <a class="page-link"
                           href="?page=<?= $i ?>&<?= $params ?>&sortField=<?= $sortField ?>&sortOrder=<?= $sortOrder ?>#view-users"><?= $i ?></a>
                    </li>
                <?php endfor; ?>

                <?php if ($page < $totalPages): ?>
                    <li class="page-item">
                        <a class="page-link"
                           href="?page=<?= $page+1 ?>&<?= $params ?>&sortField=<?= $sortField ?>&sortOrder=<?= $sortOrder ?>#view-users">»</a>
                    </li>
                <?php endif; ?>
            </ul>
        </nav>
    <?php endif; ?>

</div>
