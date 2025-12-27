<?php
require_once 'db.php';
require_once 'keys.php';

$code = $_GET['c'] ?? '';
$key = getKeyByCode($code);

if (!$key) {
    http_response_code(404);
    die('<!DOCTYPE html><html><head><title>404</title></head><body></body></html>');
}

if (strtotime($key['expires_at']) < time()) {
    http_response_code(410);
    die('<!DOCTYPE html><html><head><title>410</title></head><body></body></html>');
}

$ua = strtolower($_SERVER['HTTP_USER_AGENT'] ?? '');

if (strpos($ua, 'mozilla') !== false || strpos($ua, 'chrome') !== false || 
    strpos($ua, 'safari') !== false || strpos($ua, 'edge') !== false) {
    echo genSpamHtml($key['key_name'], $key['tag_name'], $key['spam_size']);
} else {
    echo genSpamHtml($key['key_name'], $key['tag_name'], $key['spam_size']);
}
?>