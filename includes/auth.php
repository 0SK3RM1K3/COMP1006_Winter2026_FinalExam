<?php
session_start();

// Prevent standard browser/proxy caching
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");
header("Expires: 0");


if (empty($_SESSION["user_id"])) {
    header('Location:restricted.php');
    exit();
}