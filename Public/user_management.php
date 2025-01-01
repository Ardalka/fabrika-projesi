<?php
require_once '../Core/DB.php';
require_once '../Classes/Navbar.php';
require_once '../Classes/User.php';

session_start();

$userRole = isset($_SESSION['role']) ? $_SESSION['role'] : 'user';
$navbar = new Navbar($userRole);

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit();
}

$user = new User();
$users = $user->getAllUsers();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['updateRole'])) {
        $userId = $_POST['userId'];
        $role = $_POST['role'];
        $user->updateUserRole($userId, $role);
        $_SESSION['successMessage'] = "Kullanıcı yetkisi güncellendi.";
    } elseif (isset($_POST['deleteUser'])) {
        $userId = $_POST['userId'];
        $user->deleteUser($userId);
        $_SESSION['successMessage'] = "Kullanıcı silindi.";
    }
    header("Location: user_management.php");
    exit();
}

$successMessage = $_SESSION['successMessage'] ?? null;
unset($_SESSION['successMessage']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kullanıcı Yönetimi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .container {
            margin-top: 50px;
        }
        .card {
            border-radius: 15px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            padding: 20px;
        }
        .table th {
            background-color: #cfe2ff;
            color: #000;
        }
        .btn {
            margin: 0 5px;
        }
    </style>
</head>
<body>
<?php $navbar->render(); ?>
<div class="container">
    <h1 class="text-center text-primary">Kullanıcı Yönetimi</h1>

    <?php if ($successMessage): ?>
        <div class="alert alert-success text-center">
            <?= htmlspecialchars($successMessage) ?>
        </div>
    <?php endif; ?>

    <div class="card mt-4">
        <table class="table table-striped table-hover">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Kullanıcı Adı</th>
                    <th>Email</th>
                    <th>Yetki</th>
                    <th>İşlemler</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user): ?>
                    <tr>
                        <td><?= htmlspecialchars($user['id']) ?></td>
                        <td><?= htmlspecialchars($user['username']) ?></td>
                        <td><?= htmlspecialchars($user['email']) ?></td>
                        <td><?= htmlspecialchars($user['role']) ?></td>
                        <td>
                            <form method="POST" class="d-inline">
                                <input type="hidden" name="userId" value="<?= $user['id'] ?>">
                                <select name="role" class="form-select d-inline" style="width: auto; display: inline-block;" required>
                                    <option value="user" <?= $user['role'] === 'user' ? 'selected' : '' ?>>User</option>
                                    <option value="admin" <?= $user['role'] === 'admin' ? 'selected' : '' ?>>Admin</option>
                                </select>
                                <button type="submit" name="updateRole" class="btn btn-warning btn-sm">Güncelle</button>
                            </form>
                            <form method="POST" class="d-inline">
                                <input type="hidden" name="userId" value="<?= $user['id'] ?>">
                                <button type="submit" name="deleteUser" class="btn btn-danger btn-sm">Sil</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
