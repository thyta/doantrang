<?php
session_start();

$USER = "admin";
$PASS = "Humankind2003@";

if (isset($_POST['username'])) {
    if ($_POST['username'] === $USER && $_POST['password'] === $PASS) {
        $_SESSION['admin'] = true;
    } else {
        $error = "Sai tài khoản hoặc mật khẩu";
    }
}

if (!isset($_SESSION['admin'])):
?>

<!DOCTYPE html>
<html>
<head>
<title>Admin Login</title>
<style>
body{
font-family:Arial;
text-align:center;
margin-top:100px;
}
input,button{
padding:12px;
margin:8px;
width:250px;
}
</style>
</head>
<body>

<h2>Admin Login</h2>

<?php if(isset($error)) echo "<p style='color:red'>$error</p>"; ?>

<form method="post">
<input name="username" placeholder="Username"><br>
<input type="password" name="password" placeholder="Password"><br>
<button type="submit">Login</button>
</form>

</body>
</html>

<?php exit; endif;

$dataFile = __DIR__ . '/storage/data.json';
$data = json_decode(file_get_contents($dataFile), true);
?>

<!DOCTYPE html>
<html>
<head>
<title>Dashboard</title>
<style>
body{
font-family:Arial;
padding:30px;
}
table{
width:100%;
border-collapse:collapse;
}
th,td{
border:1px solid #ddd;
padding:10px;
}
th{
background:#f4f4f4;
}
</style>
</head>
<body>

<h1>Dashboard</h1>

<p><b>Tổng lượt truy cập:</b> <?= $data['visits'] ?></p>
<p><b>Số lần tương tác:</b> <?= $data['button_clicks'] ?></p>

<a href="logout.php">Đăng xuất</a>

<h2>Lịch sử truy cập</h2>

<table>
<tr>
<th>Thời gian</th>
<th>IP</th>
<th>Thiết bị</th>
<th>Nguồn</th>
</tr>

<?php foreach(array_reverse($data['logs']) as $log): ?>
<tr>
<td><?= $log['time'] ?></td>
<td><?= $log['ip'] ?></td>
<td><?= htmlspecialchars($log['device']) ?></td>
<td><?= htmlspecialchars($log['referer']) ?></td>
</tr>
<?php endforeach; ?>

</table>

</body>
</html>