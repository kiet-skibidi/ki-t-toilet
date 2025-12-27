<?php
require_once 'db.php';

$code = $_GET['c'] ?? '';

if (empty($code)) {
    die('Usage: debug_key.php?c=YOUR_KEY_CODE');
}

$db = getDb();
$stmt = $db->prepare('SELECT * FROM keys WHERE key_code=?');
$stmt->execute([$code]);
$key = $stmt->fetch(PDO::FETCH_ASSOC);

header('Content-Type: application/json');
echo json_encode($key, JSON_PRETTY_PRINT);
?>