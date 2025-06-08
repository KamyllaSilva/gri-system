<?php
require_once 'auth.php';

if ($_SESSION['tipo'] !== 'admin') {
    header('Location: /dashboard.php');
    exit;
}
