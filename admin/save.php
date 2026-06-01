<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('HTTP/1.0 403 Forbidden');
    echo 'غير مصرح';
    exit;
}

$settingsFile = __DIR__ . '/../data/settings.json';
$settings = json_decode(file_get_contents($settingsFile), true);
$section = $_POST['_section'] ?? '';

if ($section === 'colors') {
    foreach ($settings['colors'] as $key => &$color) {
        if (isset($_POST[$key . '_h'])) {
            $color['h'] = (int)$_POST[$key . '_h'];
            $color['s'] = (int)$_POST[$key . '_s'];
            $color['l'] = (int)$_POST[$key . '_l'];
        }
    }
    $_SESSION['admin_msg'] = 'تم حفظ الألوان بنجاح';
    $_SESSION['admin_msg_type'] = 'success';
    header('Location: dashboard.php?tab=colors');
    exit;
}

if ($section === 'content') {
    foreach ($settings['content'] as $key => $value) {
        if (isset($_POST[$key])) {
            $settings['content'][$key] = $_POST[$key];
        }
    }
    $_SESSION['admin_msg'] = 'تم حفظ المحتوى بنجاح';
    $_SESSION['admin_msg_type'] = 'success';
    header('Location: dashboard.php?tab=content');
    exit;
}

if ($section === 'smtp') {
    $settings['smtp']['host'] = $_POST['smtp_host'] ?? '';
    $settings['smtp']['port'] = (int)($_POST['smtp_port'] ?? 587);
    $settings['smtp']['username'] = $_POST['smtp_username'] ?? '';
    $settings['smtp']['encryption'] = $_POST['smtp_encryption'] ?? 'tls';
    $settings['smtp']['from_email'] = $_POST['smtp_from_email'] ?? '';
    $settings['smtp']['from_name'] = $_POST['smtp_from_name'] ?? '';
    if (!empty($_POST['smtp_password'])) {
        $settings['smtp']['password'] = $_POST['smtp_password'];
    }
    $_SESSION['admin_msg'] = 'تم حفظ إعدادات SMTP بنجاح';
    $_SESSION['admin_msg_type'] = 'success';
    header('Location: dashboard.php?tab=smtp');
    exit;
}

header('Location: dashboard.php');
