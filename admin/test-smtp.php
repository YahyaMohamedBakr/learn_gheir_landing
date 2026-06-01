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

$to = $testEmail;
$subject = 'اختبار إعدادات SMTP - Learn.Gheir';
$message = "هذا بريد اختبار من لوحة تحكم Learn.Gheir.\n\nإذا وصلتك هذه الرسالة، فإعدادات SMTP تعمل بشكل صحيح.\n\nشكراً لك.";
$headers = 'From: ' . $smtp['from_name'] . ' <' . $smtp['from_email'] . '>' . "\r\n" . 'Reply-To: ' . $smtp['from_email'] . "\r\n" . 'X-Mailer: PHP/' . phpversion();

$success = mail($to, $subject, $message, $headers, '-f ' . $smtp['from_email']);

if ($success) {
    $_SESSION['admin_msg'] = 'تم إرسال بريد الاختبار بنجاح إلى ' . htmlspecialchars($testEmail);
    $_SESSION['admin_msg_type'] = 'success';
} else {
    $_SESSION['admin_msg'] = 'فشل إرسال البريد. تحقق من إعدادات SMTP ومن أن الخادم يدعم إرسال البريد.';
    $_SESSION['admin_msg_type'] = 'error';
}

header('Location: dashboard.php?tab=smtp');
exit;
