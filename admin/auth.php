<?php
session_start();

if (!(isset($_SESSION['admin_id']) && $_SESSION['admin_id'] != 0)) {
    header('Location: login.php');
    exit;
}
