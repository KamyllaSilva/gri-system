<?php
session_start();

if (isset($_SESSION['usuario_id'])) {
    header("Location: frontend/pages/dashboard.php");
    exit();
} 

header("Location: frontend/pages/login.php");
exit();
