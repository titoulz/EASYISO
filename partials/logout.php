<?php
session_start();
require_once '../controllers/UserController.php';

use App\Controllers\UserController;

$userController = new UserController($pdo);
$userController->logout();

header("Location: /public/index.php?action=login");
exit();
?>