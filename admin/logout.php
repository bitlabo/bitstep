<?php
session_start();

// unset や destroy が効かない場合がある場合があるので
$_SESSION['admin_id'] = 0;
$_SESSION['admin_username'] = '';

session_unset();  // セッション変数をすべて削除
session_destroy(); // セッションを破棄

header('Location: login.php'); // ログイン画面へリダイレクト
exit;
