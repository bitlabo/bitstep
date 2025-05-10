<?php
/*
session_start();

// セッションを破棄してドリルを終了
session_unset(); // セッション変数を解除
session_destroy(); // セッションを破棄

// 終了後、ホームページにリダイレクト（ここでは例として index.php を指定）
*/
header('Location: ..\index.php');
exit;
