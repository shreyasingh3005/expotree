<?php
/**
 * Admin Authentication Gatekeeper
 * Expo Tree Exhibitions
 */
require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/functions.php';

if (!isAdminLoggedIn()) {
    setFlash('danger', 'Please log in to access the administrator panel.');
    header('Location: ' . BASE_URL . '/admin/login.php');
    exit;
}
