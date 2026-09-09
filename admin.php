<?php

session_start();

require 'database/config.php';

/* Admin-only guard */
if (
    !isset($_SESSION['user_id']) ||
    ($_SESSION['role'] ?? '') !== 'admin'
) {
    header(
        'Location: login.php?status=error&message='
        . urlencode('Admin access only.')
    );
    exit;
}

$status  = $_GET['status'] ?? null;
$message = $_GET['message'] ?? null;

$pdo = getConnection();

$stmt = $pdo->query("
    SELECT id, username, email, number, role, created_at
    FROM `users`
    ORDER BY created_at DESC
");

$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

$totalUsers = count($users);

$totalAdmins = count(array_filter(
    $users,
    fn($u) => $u['role'] === 'admin'
));

?>
<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Admin Dashboard | Harvest Bread Co.</title>

    <link rel="stylesheet" href="style.css">

</head>

<body class="auth-page">

<header class="site-header">

    <div class="nav-container">

        <a href="index.php" class="brand">
            <img src="image/logo.svg" alt="Harvest Bread Co. Logo">
        </a>

        <nav class="main-nav">
            <a href="index.php">SITE</a>
            <a href="logout.php">LOGOUT</a>
        </nav>

    </div>

</header>

<main class="admin-container">

    <div class="admin-heading">
        <h1>ADMIN DASHBOARD</h1>
        <p class="auth-subtitle">
            Signed in as
            <strong><?= htmlspecialchars($_SESSION['username']) ?></strong>
        </p>
    </div>

    <div class="admin-subnav">
        <a href="admin.php" class="active">Users</a>
        <a href="admin_products.php">Products</a>
        <a href="admin_orders.php">Orders</a>
    </div>

    <?php if ($status === 'error'): ?>
        <div class="auth-message error">
            <?= htmlspecialchars($message) ?>
        </div>
    <?php endif; ?>

    <?php if ($status === 'success'): ?>
        <div class="auth-message success">
            <?= htmlspecialchars($message) ?>
        </div>
    <?php endif; ?>

    <div class="admin-stats">

        <div class="admin-stat-card">
            <span class="admin-stat-number"><?= $totalUsers ?></span>
            <span class="admin-stat-label">Total Users</span>
        </div>

        <div class="admin-stat-card">
            <span class="admin-stat-number"><?= $totalAdmins ?></span>
            <span class="admin-stat-label">Admins</span>
        </div>

    </div>

    <div class="admin-table-wrap">

        <table class="admin-table">

            <thead>
                <tr>
                    <th>ID</th>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Number</th>
                    <th>Role</th>
                    <th>Joined</th>
                    <th>Actions</th>
                </tr>
            </thead>

            <tbody>

                <?php foreach ($users as $user): ?>

                    <tr>
                        <td><?= htmlspecialchars($user['id']) ?></td>
                        <td><?= htmlspecialchars($user['username']) ?></td>
                        <td><?= htmlspecialchars($user['email']) ?></td>
                        <td><?= htmlspecialchars($user['number']) ?></td>
                        <td>
                            <span class="role-badge role-<?= htmlspecialchars($user['role']) ?>">
                                <?= htmlspecialchars($user['role']) ?>
                            </span>
                        </td>
                        <td><?= htmlspecialchars($user['created_at']) ?></td>
                        <td class="admin-actions">

                            <?php if ((int) $user['id'] === (int) $_SESSION['user_id']): ?>

                                <span class="admin-you">You</span>

                            <?php else: ?>

                                <form method="POST" action="admin_actions.php">
                                    <input type="hidden" name="id" value="<?= (int) $user['id'] ?>">

                                    <?php if ($user['role'] === 'admin'): ?>
                                        <input type="hidden" name="action" value="make_user">
                                        <button type="submit" class="small-button">Demote</button>
                                    <?php else: ?>
                                        <input type="hidden" name="action" value="make_admin">
                                        <button type="submit" class="small-button">Promote</button>
                                    <?php endif; ?>
                                </form>

                                <form
                                    method="POST"
                                    action="admin_actions.php"
                                    onsubmit="return confirm('Delete this user? This cannot be undone.');"
                                >
                                    <input type="hidden" name="id" value="<?= (int) $user['id'] ?>">
                                    <input type="hidden" name="action" value="delete">
                                    <button type="submit" class="small-button danger">Delete</button>
                                </form>

                            <?php endif; ?>

                        </td>
                    </tr>

                <?php endforeach; ?>

                <?php if (empty($users)): ?>
                    <tr>
                        <td colspan="7" class="admin-empty">No users yet.</td>
                    </tr>
                <?php endif; ?>

            </tbody>

        </table>

    </div>

</main>

</body>
</html>
