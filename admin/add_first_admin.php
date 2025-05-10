<?php
require_once '../config/db.php';

$loginid = 'admin';
$username = '管理者';
$password = password_hash('adminpass', PASSWORD_DEFAULT);

$stmt = $pdo->prepare("INSERT INTO administrators (loginid, username, password) VALUES (?, ?, ?)");
$stmt->execute([$loginid, $username, $password]);

echo "管理者を追加しました！";
