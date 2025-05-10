<?php
$is_local = in_array($_SERVER['REMOTE_ADDR'], ['127.0.0.1', '::1']);

if ($is_local) {
    $host = 'localhost';  // DBサーバー
    $dbname = 'bitlabor_bstep';  // データベース名
    $username = 'root';   // DBユーザー名
    $password = '';       // DBパスワード（ローカル開発時など）
} else {
    $host = 'localhost';  // DBサーバー
    $dbname = 'bitlabor_bstep';  // データベース名
    $username = 'bitlabor_bstep';   // DBユーザー名
    $password = 'Naoko0418';       // DBパスワード（ローカル開発時など）
}

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die('接続失敗: ' . $e->getMessage());
}
