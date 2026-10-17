<?php
require_once __DIR__ . '/../includes/bootstrap.php';
log_action('Déconnexion');
$_SESSION = [];
session_destroy();
header('Location: ' . base_url() . '/admin/login.php');
