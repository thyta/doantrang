<?php
session_start();

/* ===== CONFIG LOGIN ===== */
$USER = "admin";
$PASS = "Humankind2003@";

/* ===== LOGIN ===== */
if (isset($_POST['username'])) {
    if ($_POST['username'] === $USER && $_POST['password'] === $PASS) {
        $_SESSION['admin'] = true;
        header("Location: admin.php");
        exit;
    } else {
        $error = "Sai tài khoản hoặc mật khẩu";
    }
}

if (!isset($_SESSION['admin'])):
?>

<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title>Admin Login</title>
<style>
body{
    font-family: Arial;
    background:#f8fafc;
    display:flex;
    justify-content:center;
    align-items:center;
    height:100vh;
}
.login-box{
    background:white;
    padding:40px;
    border-radius:16px;
    box-shadow:0 10px 30px rgba(0,0,0,.08);
    width:320px;
    text-align:center;
}
input,button{
    width:100%;
    padding:12px;
    margin-top:12px;
    border-radius:10px;
    border:1px solid #ddd;
}
button{
    background:#ff4d88;
    color:white;
    border:none;
    cursor:pointer;
}
</style>
</head>
<body>

<div class="login-box">
    <h2>Admin Login</h2>

    <?php if(isset($error)): ?>
        <p style="color:red"><?= $error ?></p>
    <?php endif; ?>

    <form method="post">
        <input name="username" placeholder="Username" required>
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit">Đăng nhập</button>
    </form>
</div>

</body>
</html>

<?php exit; endif;

/* ===== LOAD DATA ===== */
$dataFile = __DIR__ . '/storage/data.json';

$default = [
    "visits" => 0,
    "unique_visits" => 0,
    "replay_clicks" => 0,
    "exit_count" => 0,
    "logs" => [],
    "replay_logs" => [],
    "exit_logs" => []
];

if (!file_exists($dataFile)) {
    file_put_contents($dataFile, json_encode($default, JSON_PRETTY_PRINT));
}

$data = json_decode(file_get_contents($dataFile), true);

if (!$data) $data = $default;
?>

<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title>Dashboard</title>

<style>
body{
    font-family:Arial;
    padding:30px;
    background:#f8fafc;
}

h1{
    margin-bottom:20px;
}

.stats{
    display:grid;
    grid-template-columns:repeat(4,1fr);
    gap:20px;
    margin-bottom:30px;
}

.card{
    background:white;
    padding:20px;
    border-radius:14px;
    box-shadow:0 8px 20px rgba(0,0,0,.05);
}

.card h3{
    margin:0;
    font-size:15px;
    color:#666;
}

.card p{
    font-size:28px;
    margin:10px 0 0;
    font-weight:bold;
    color:#ff4d88;
}

table{
    width:100%;
    border-collapse:collapse;
    background:white;
    box-shadow:0 8px 20px rgba(0,0,0,.04);
}

th,td{
    border:1px solid #eee;
    padding:12px;
    text-align:left;
}

th{
    background:#fafafa;
}

.logout{
    display:inline-block;
    margin-bottom:25px;
    color:#ff4d88;
    text-decoration:none;
}
</style>
</head>
<body>

<h1>Dashboard</h1>

<a class="logout" href="logout.php">Đăng xuất</a>

<div class="stats">
    <div class="card">
        <h3>Tổng lượt truy cập</h3>
        <p><?= $data['visits'] ?? 0 ?></p>
    </div>

    <div class="card">
        <h3>Unique User</h3>
        <p><?= $data['unique_visits'] ?? 0 ?></p>
    </div>

    <div class="card">
        <h3>Bấm "Lần nữa nhé"</h3>
        <p><?= $data['replay_clicks'] ?? 0 ?></p>
    </div>

    <div class="card">
        <h3>Thoát trang</h3>
        <p><?= $data['exit_count'] ?? 0 ?></p>
    </div>
</div>

<h2>Lịch sử truy cập</h2>

<table>
<tr>
    <th>Thời gian</th>
    <th>IP</th>
    <th>Thiết bị</th>
    <th>Nguồn</th>
</tr>

<?php foreach(array_reverse($data['logs'] ?? []) as $log): ?>
<tr>
    <td><?= $log['time'] ?? '' ?></td>
    <td><?= $log['ip'] ?? '' ?></td>
    <td><?= htmlspecialchars($log['device'] ?? '') ?></td>
    <td><?= htmlspecialchars($log['referer'] ?? '') ?></td>
</tr>
<?php endforeach; ?>

</table>

</body>
</html>