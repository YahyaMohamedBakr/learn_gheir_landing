<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

$username = $_POST['username'] ?? '';
$password = $_POST['password'] ?? '';

$usersFile = __DIR__ . '/../data/users.json';
if (!file_exists($usersFile)) {
    $_SESSION['login_error'] = 'خطأ في النظام. الرجاء الاتصال بالدعم.';
    header('Location: index.php');
    exit;
}

$users = json_decode(file_get_contents($usersFile), true);

if (!isset($users[$username]) || !password_verify($password, $users[$username]['password'])) {
    $_SESSION['login_error'] = 'اسم المستخدم أو كلمة المرور غير صحيحة';
    header('Location: index.php');
    exit;
}

$_SESSION['admin_logged_in'] = true;
$_SESSION['admin_name'] = $users[$username]['name'];
$_SESSION['admin_username'] = $username;
header('Location: dashboard.php');
exit;
