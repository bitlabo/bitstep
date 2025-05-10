<?php
session_start();
require '../config/db.php';

// ログイン処理
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $loginid = $_POST['loginid'];
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT * FROM users WHERE loginid = :loginid");
    $stmt->execute(['loginid' => $loginid]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        header("Location: ../index.php");
        exit();
    } else {
        $error_message = "ログインIDまたはパスワードが間違っています。";
    }
}
?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BitStep - ログイン</title>
    <link rel="icon" href="../assets/icons8-b-100.png" type="image/x-icon">
    <link rel="stylesheet" href="../assets/style.css">
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
        <h1>ログイン</h1>

        <?php if (isset($error_message)): ?>
            <p class="error-message"><?= htmlspecialchars($error_message) ?></p>
        <?php endif; ?>

        <form action="login.php" method="post">
            <div class="form-group">
                <label for="loginid">ログインID</label>
                <input type="text" name="loginid" id="loginid" class="input-full" required>
            </div>
            <div class="form-group">
                <label for="password">パスワード</label>
                <input type="password" name="password" id="password" class="input-full" required>
            </div>
            <button type="submit" class="btn input-full">ログイン</button>
        </form>

        <div class="guest-info">
            <p>お試しログインが可能です。以下の情報をご利用ください。</p>
            <ul>
                <li><strong>ログインID：</strong> guest</li>
                <li><strong>パスワード：</strong> password</li>
            </ul>
        </div>

    </div>

</body>

</html>