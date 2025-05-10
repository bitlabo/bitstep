<?php
require_once 'auth.php'; // ログインチェック（セッション確認）
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>BitStep - 管理メニュー</title>
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body>
<div class="container">
    <h1>BitStep 管理画面</h1>
    <p>ようこそ、<?= htmlspecialchars($_SESSION['admin_username']) ?> さん。</p>

    <ul>
        <li><a href="display_questions.php">問題一覧</a></li>
        <li><a href="add_question.php">問題追加</a></li>
        <li><a href="manage_categories.php">カテゴリ管理</a></li>
        <li><a href="view_users.php">ユーザー一覧</a></li>
        <li><a href="stats.php">統計表示</a></li>
        <li><a href="logout.php">ログアウト</a></li>
    </ul>
</div>
</body>
</html>
