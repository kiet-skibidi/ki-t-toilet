<?php
require_once 'db.php';

$db = getDb();

$stmt = $db->query("UPDATE keys SET tag_name='kjtdzs1' WHERE tag_name IS NULL OR tag_name=''");
echo "Updated " . $stmt->rowCount() . " keys with default tag_name\n<br>";

$stmt = $db->query("UPDATE keys SET spam_size='10MB' WHERE spam_size IS NULL OR spam_size=''");
echo "Updated " . $stmt->rowCount() . " keys with default spam_size\n<br>";

echo "\nDone! All keys updated.";
?>