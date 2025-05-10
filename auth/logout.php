<?php
session_start();

// セッションを全て削除してログアウト
session_unset();
session_destroy();

header("Location: ../index.php");
exit();
