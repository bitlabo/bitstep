<?php
// エラー表示を有効にする
ini_set('display_errors', 1);
error_reporting(E_ALL);

require '../config/db.php'; // PDOインスタンス $pdo を取得

// 登録するユーザー一覧
$users = [
    ['username' => '勝利', 'loginid' => 'katsutoshi', 'password' => 'katsutoshi3'],
    ['username' => '藤岡', 'loginid' => 'fujioka', 'password' => 'fujioka3'],
];

// 登録処理
$stmt = $pdo->prepare("INSERT INTO users (loginid, username, password) VALUES (:loginid, :username, :password)");

foreach ($users as $user) {
    $hashed_password = password_hash($user['password'], PASSWORD_DEFAULT);
    $stmt->execute([
        'loginid' => $user['loginid'],
        'username' => $user['username'],
        'password' => $hashed_password,
    ]);
}

echo count($users) . "人のユーザーを追加しました！";
