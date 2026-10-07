<?php
/**
 * Administrator Logout
 * Expo Tree Exhibitions
 */
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/functions.php';

unset($_SESSION['admin_logged_in']);
unset($_SESSION['admin_id']);
unset($_SESSION['admin_username']);
unset($_SESSION['admin_name']);
unset($_SESSION['admin_role']);

setFlash('info', 'You have been successfully logged out.');
header('Location: ' . BASE_URL . '/admin/login.php');
exit;
