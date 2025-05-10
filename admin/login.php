<?php
session_start();
require_once '../config/db.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $loginid = $_POST['loginid'] ?? '';
    $password = $_POST['password'] ?? '';

    $stmt = $pdo->prepare("SELECT * FROM administrators WHERE loginid = ?");
    $stmt->execute([$loginid]);
    $admin = $stmt->fetch();

    if ($admin && password_verify($password, $admin['password'])) {
        $_SESSION['admin_id'] = $admin['id'];
        $_SESSION['admin_username'] = $admin['username'];
        header("Location: index.php");
        exit;
    } else {
        $error = "ログインIDまたはパスワードが違います";
    }
}
?>
<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BitStep - 管理者ログイン</title>
    <link rel="icon" href="../assets/icons8-b-100.png" type="image/x-icon">
    <link rel="stylesheet" href="../assets/style.css"> <!-- 共通CSSの読み込み -->
    <style>
        input[type="text"],
        input[type="password"],
        button.btn {
            width: 100%;
            box-sizing: border-box;
            margin: 0;
        }

        h1 {
            text-align: center;
        }
    </style>
</head>

<body>
    <div class="main-container">
        <h1>管理者ログイン</h1>

        <?php if ($error): ?>
            <div class="error-message"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="post" action="">
            <div class="form-group">
                <label for="loginid">ログインID</label>
                <input type="text" name="loginid" id="loginid" required>
            </div>
            <div class="form-group">
                <label for="password">パスワード</label>
                <input type="password" name="password" id="password" required>
            </div>
            <button type="submit" class="btn btn-primary">ログイン</button>
        </form>
    </div>
</body>

</html>