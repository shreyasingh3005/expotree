<?php
/**
 * Event Action Handler (Approve, Reject, Publish, Unpublish, Delete)
 * Expo Tree Exhibitions
 */
require_once __DIR__ . '/includes/auth-check.php';

$action = sanitize($_GET['action'] ?? '');
$id = (int)($_GET['id'] ?? 0);
$token = $_GET['csrf_token'] ?? '';
$returnTo = sanitize($_GET['return_to'] ?? 'events.php');

if (!verify_csrf($token)) {
    setFlash('danger', 'Invalid security verification token.');
    header('Location: ' . BASE_URL . '/admin/' . $returnTo);
    exit;
}

if ($id <= 0) {
    setFlash('danger', 'Invalid exhibition ID provided.');
    header('Location: ' . BASE_URL . '/admin/' . $returnTo);
    exit;
}

$stmt = $pdo->prepare("SELECT id, title, status FROM events WHERE id = ?");
$stmt->execute([$id]);
$event = $stmt->fetch();

if (!$event) {
    setFlash('danger', 'Exhibition record not found.');
    header('Location: ' . BASE_URL . '/admin/' . $returnTo);
    exit;
}

switch ($action) {
    case 'approve':
    case 'publish':
        $upd = $pdo->prepare("UPDATE events SET status = 'published' WHERE id = ?");
        $upd->execute([$id]);
        sync_event_stalls($pdo, $id);
        setFlash('success', "Exhibition #{$id} ({$event['title']}) has been approved and published live!");
        break;

    case 'reject':
        $reason = sanitize($_GET['reason'] ?? 'Does not meet current venue clearance requirements.');
        $upd = $pdo->prepare("UPDATE events SET status = 'rejected', rejection_reason = ? WHERE id = ?");
        $upd->execute([$reason, $id]);
        setFlash('warning', "Exhibition #{$id} ({$event['title']}) was marked as Rejected.");
        break;

    case 'unpublish':
        $upd = $pdo->prepare("UPDATE events SET status = 'unpublished' WHERE id = ?");
        $upd->execute([$id]);
        setFlash('info', "Exhibition #{$id} ({$event['title']}) has been unpublished from the public website.");
        break;

    case 'toggle_status':
        $newStatus = ($event['status'] === 'published' || $event['status'] === 'active') ? 'unpublished' : 'published';
        $upd = $pdo->prepare("UPDATE events SET status = ? WHERE id = ?");
        $upd->execute([$newStatus, $id]);
        if ($newStatus === 'published') sync_event_stalls($pdo, $id);
        setFlash('success', "Exhibition #{$id} status updated to " . ucfirst($newStatus) . ".");
        break;

    case 'delete':
        $del = $pdo->prepare("DELETE FROM events WHERE id = ?");
        $del->execute([$id]);
        setFlash('success', "Exhibition #{$id} ({$event['title']}) and its stalls have been permanently deleted.");
        break;

    default:
        setFlash('danger', 'Unrecognized action request.');
        break;
}

header('Location: ' . BASE_URL . '/admin/' . $returnTo);
exit;
