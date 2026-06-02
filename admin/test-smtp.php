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

require_once __DIR__ . '/../vendor/autoload.php';

$mail = new PHPMailer\PHPMailer\PHPMailer(true);
$mail->isSMTP();
$mail->Host = $smtp['host'];
$mail->Port = $smtp['port'];
$mail->SMTPAuth = !empty($smtp['username']);
$mail->Username = $smtp['username'];
$mail->Password = $smtp['password'];
$mail->SMTPSecure = $smtp['encryption'] === 'none' ? '' : $smtp['encryption'];
$mail->CharSet = 'UTF-8';
$mail->setFrom($smtp['from_email'], $smtp['from_name']);
$mail->addAddress($testEmail);
$mail->Subject = 'اختبار إعدادات SMTP - Learn.Gheir';
$mail->Body = "هذا بريد اختبار من لوحة تحكم Learn.Gheir.\n\nإذا وصلتك هذه الرسالة، فإعدادات SMTP تعمل بشكل صحيح.\n\nشكراً لك.";

try {
    $mail->send();
    $_SESSION['admin_msg'] = 'تم إرسال بريد الاختبار بنجاح إلى ' . htmlspecialchars($testEmail);
    $_SESSION['admin_msg_type'] = 'success';
} catch (Exception $e) {
    $_SESSION['admin_msg'] = 'فشل إرسال البريد: ' . $mail->ErrorInfo;
    $_SESSION['admin_msg_type'] = 'error';
}

header('Location: dashboard.php?tab=smtp');
exit;
