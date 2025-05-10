<?php
$is_local = in_array($_SERVER['REMOTE_ADDR'], ['127.0.0.1', '::1']);

if ($is_local) {    // ローカル開発環境
    $host = 'xxxx';     // DBサーバー
    $dbname = 'xxxx';   // データベース名
    $username = 'xxxx'; // DBユーザー名
    $password = '';     // DBパスワード（ローカル開発時など）
} else {            // 本番環境
    $host = 'xxxx';     // DBサーバー
    $dbname = 'xxxx';   // データベース名
    $username = 'xxxx'; // DBユーザー名
    $password = 'xxxx'; // DBパスワード（ローカル開発時など）
}

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die('接続失敗: ' . $e->getMessage());
}
