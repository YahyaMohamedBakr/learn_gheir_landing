<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: index.php');
    exit;
}

$settingsFile = __DIR__ . '/../data/settings.json';
$settings = json_decode(file_get_contents($settingsFile), true);
$smtp = $settings['smtp'];
$testEmail = $_POST['test_email'] ?? '';

if (!$testEmail) {
    $_SESSION['admin_msg'] = 'الرجاء إدخال بريد إلكتروني للاختبار';
    $_SESSION['admin_msg_type'] = 'error';
    header('Location: dashboard.php?tab=smtp');
    exit;
}

require_once __DIR__ . '/../inc/smtp.php';

$smtpMailer = new SMTP($smtp);
$result = $smtpMailer->send(
    $testEmail,
    'اختبار إعدادات SMTP - Learn.Gheir',
    "هذا بريد اختبار من لوحة تحكم Learn.Gheir.\n\nإذا وصلتك هذه الرسالة، فإعدادات SMTP تعمل بشكل صحيح.\n\nشكراً لك."
);

if ($result['success']) {
    $_SESSION['admin_msg'] = 'تم إرسال بريد الاختبار بنجاح إلى ' . htmlspecialchars($testEmail);
    $_SESSION['admin_msg_type'] = 'success';
} else {
    $_SESSION['admin_msg'] = 'فشل إرسال البريد: ' . $result['message'];
    $_SESSION['admin_msg_type'] = 'error';
}

header('Location: dashboard.php?tab=smtp');
exit;
