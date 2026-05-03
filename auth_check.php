<?php
session_start();

require_once __DIR__ . '/utils/config.php';

$conn = get_server_db();
$conn->select_db("village_bank");

function requireRole($roles)
{
    global $conn;

    // Not logged in
    if (!isset($_SESSION['user_id'])) {
        header("Location: ../../auth/login.php");
        exit();
    }

    if (!is_array($roles)) {
        $roles = [$roles];
    }

    $roles = array_map('strtolower', $roles);

    $stmt = $conn->prepare("
        SELECT r.role_name
        FROM users u
        JOIN roles r ON u.role_id = r.role_id
        WHERE u.id = ?
    ");

    if (!$stmt) {
        die("DB Error: " . $conn->error);
    }

    $stmt->bind_param("i", $_SESSION['user_id']);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows !== 1) {
        session_destroy();
        header("Location: ../../auth/login.php");
        exit();
    }

    $user = $result->fetch_assoc();
    $db_role = strtolower(trim($user['role_name']));

    if (!in_array($db_role, $roles)) {
        header("Location: ../../auth/unauthorized.php");
        exit();
    }
}