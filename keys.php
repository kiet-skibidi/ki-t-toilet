<?php
function genKeyCode() {
    $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    $code = '';
    for ($i = 0; $i < 10; $i++) {
        $code .= $chars[random_int(0, strlen($chars) - 1)];
    }
    return $code;
}

function getUserKeys($uid) {
    $db = getDb();
    $db->exec("DELETE FROM keys WHERE expires_at < datetime('now')");
    $stmt = $db->prepare('SELECT * FROM keys WHERE user_id=? ORDER BY id DESC');
    $stmt->execute([$uid]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function createKey($uid, $name, $exp, $tag = 'kjtdzs1', $spam = '500KB') {
    $db = getDb();
    $code = genKeyCode();
    $stmt = $db->prepare('INSERT INTO keys (user_id, key_name, key_code, expires_at, tag_name, spam_size) VALUES (?, ?, ?, ?, ?, ?)');
    $stmt->execute([$uid, $name, $code, $exp, $tag, $spam]);
    return $code;
}

function updateKey($uid, $id, $name, $exp, $tag, $spam) {
    $db = getDb();
    $stmt = $db->prepare('UPDATE keys SET key_name=?, expires_at=?, tag_name=?, spam_size=? WHERE id=? AND user_id=?');
    $stmt->execute([$name, $exp, $tag, $spam, $id, $uid]);
}

function deleteKey($uid, $id) {
    $db = getDb();
    $stmt = $db->prepare('DELETE FROM keys WHERE id=? AND user_id=?');
    $stmt->execute([$id, $uid]);
}

function getKeyByCode($code) {
    $db = getDb();
    $stmt = $db->prepare('SELECT key_name, tag_name, spam_size, expires_at FROM keys WHERE key_code=?');
    $stmt->execute([$code]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($result) {
        if (empty($result['tag_name'])) {
            $result['tag_name'] = 'kjtdzs1';
        }
        if (empty($result['spam_size'])) {
            $result['spam_size'] = '500KB';
        }
    }
    
    return $result;
}

function genSpamHtml($name, $tag = 'kjtdzs1', $spam = '6MB') {
    $sizeMap = [
        '500KB' => [3000, 5000, 2000],
        '1MB' => [5000, 8000, 3500],
        '3MB' => [8000, 12000, 6000],
        '6MB' => [12000, 18000, 10000]
    ];
    
    $sizes = isset($sizeMap[$spam]) ? $sizeMap[$spam] : $sizeMap['6MB'];
    $htmlCount = $sizes[0];
    $jsCount = $sizes[1];
    $hiddenCount = $sizes[2];
    
    $htmlPart1 = '';
    $htmlPart2 = '';
    
    for ($i = 0; $i < $htmlCount / 2; $i++) {
        $htmlPart1 .= '<div class="c' . $i . '" id="e' . $i . '" data-v="' . bin2hex(random_bytes(80)) . '">';
        $htmlPart1 .= '<span>' . base64_encode(random_bytes(60)) . '</span>';
        $htmlPart1 .= '<p style="display:none">' . bin2hex(random_bytes(90)) . '</p>';
        $htmlPart1 .= '</div>';
    }
    
    for ($i = $htmlCount / 2; $i < $htmlCount; $i++) {
        $htmlPart2 .= '<div class="c' . $i . '" id="e' . $i . '" data-v="' . bin2hex(random_bytes(80)) . '">';
        $htmlPart2 .= '<span>' . base64_encode(random_bytes(60)) . '</span>';
        $htmlPart2 .= '<p style="display:none">' . bin2hex(random_bytes(90)) . '</p>';
        $htmlPart2 .= '</div>';
    }
    
    $js = '';
    for ($i = 0; $i < $jsCount; $i++) {
        $js .= 'const a' . $i . '="' . bin2hex(random_bytes(70)) . '";';
        $js .= 'let b' . $i . '={v:"' . base64_encode(random_bytes(65)) . '",k:"' . bin2hex(random_bytes(55)) . '"};';
        $js .= 'var c' . $i . '=["' . bin2hex(random_bytes(75)) . '","' . base64_encode(random_bytes(70)) . '"];';
    }
    
    $hiddenDivs = '';
    for ($i = 0; $i < $hiddenCount; $i++) {
        $hiddenDivs .= '<div style="display:none">' . bin2hex(random_bytes(100)) . '</div>';
    }
    
    $output = '<!DOCTYPE html><html><head><title>Page</title><style>';
    $output .= 'body{font-family:Arial;padding:20px;background:#f5f5f5}';
    $output .= '.container{max-width:800px;margin:0 auto;background:white;padding:30px;border-radius:8px}';
    $output .= 'h1{color:#333;margin-bottom:20px}.content{line-height:1.6;color:#666}';
    $output .= '</style></head><body><div class="container"><h1>Welcome</h1>';
    $output .= '<div class="content"><p>This is a standard webpage with normal content. Everything looks completely normal here.</p>';
    $output .= '<p>You can browse through this page and find various information displayed in a standard format.</p></div>';
    $output .= $htmlPart1;
    $output .= '<' . htmlspecialchars($tag) . '>' . htmlspecialchars($name) . '</' . htmlspecialchars($tag) . '>';
    $output .= $htmlPart2;
    $output .= $hiddenDivs;
    $output .= '</div><script>' . $js . '</script></body></html>';
    
    return $output;
}
?>