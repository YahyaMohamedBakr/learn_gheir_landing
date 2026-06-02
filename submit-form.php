<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
if (!$input) {
    $input = $_POST;
}

$fullName = trim($input['fullName'] ?? '');
$email = trim($input['email'] ?? '');
$phone = trim($input['phone'] ?? '');
$city = trim($input['city'] ?? '');
$role = trim($input['role'] ?? '');
$message = trim($input['message'] ?? '');
$agreeToTerms = !empty($input['agreeToTerms']);

if (!$fullName || !$email || !$phone || !$city || !$role) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'يرجى تعبئة جميع الحقول المطلوبة']);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'البريد الإلكتروني غير صالح']);
    exit;
}

if (!$agreeToTerms) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'يرجى الموافقة على الشروط']);
    exit;
}

$settingsFile = __DIR__ . '/data/settings.json';
$settings = json_decode(file_get_contents($settingsFile), true);
$smtpConfig = $settings['smtp'] ?? [];
$contactEmail = $settings['site']['contact_email'] ?? 'contact@learn.gheir.org';

$submissionsFile = __DIR__ . '/data/submissions.json';
$submissions = [];
if (file_exists($submissionsFile)) {
    $submissions = json_decode(file_get_contents($submissionsFile), true) ?? [];
}

$submission = [
    'id' => time(),
    'fullName' => $fullName,
    'email' => $email,
    'phone' => $phone,
    'city' => $city,
    'role' => $role,
    'message' => $message,
    'timestamp' => date('Y-m-d\TH:i:s')
];

$submissions[] = $submission;
file_put_contents($submissionsFile, json_encode($submissions, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

$emailSent = false;
if (!empty($smtpConfig['host'])) {
    $roleLabels = [
        'student' => 'طالب',
        'parent' => 'ولي أمر',
        'teacher' => 'معلم',
        'institution' => 'مؤسسة تعليمية',
        'investor' => 'مستثمر',
        'partner' => 'شريك محتمل'
    ];
    $roleLabel = $roleLabels[$role] ?? $role;

    require_once __DIR__ . '/vendor/autoload.php';
    $mail = new PHPMailer\PHPMailer\PHPMailer(true);
    $mail->isSMTP();
    $mail->Host = $smtpConfig['host'];
    $mail->Port = $smtpConfig['port'];
    $mail->SMTPAuth = !empty($smtpConfig['username']);
    $mail->Username = $smtpConfig['username'];
    $mail->Password = $smtpConfig['password'];
    $mail->SMTPSecure = $smtpConfig['encryption'] === 'none' ? '' : $smtpConfig['encryption'];
    $mail->CharSet = 'UTF-8';
    $mail->setFrom($smtpConfig['from_email'], $smtpConfig['from_name']);
    $mail->addAddress($contactEmail);
    $mail->Subject = "[$roleLabel] $fullName — طلب تسجيل مبكر";
    $mail->isHTML(true);

    $messageHtml = $message ? htmlspecialchars($message) : '<em style="color:#94a3b8">لا يوجد</em>';
    $dt = date('Y-m-d H:i:s');

    $mail->Body = <<<HTML
<!DOCTYPE html>
<html dir="rtl">
<head><meta charset="UTF-8"></head>
<body style="margin:0;padding:0;background:#f5f7fa;font-family:'Tajawal',sans-serif">
  <table role="presentation" style="width:100%;max-width:600px;margin:40px auto;background:#ffffff;border-radius:16px;overflow:hidden;box-shadow:0 4px 24px rgba(0,0,0,0.06)">
    <tr>
      <td style="background:linear-gradient(135deg,#a8d13e,#f4d35e);padding:24px 32px;text-align:center">
        <h1 style="margin:0;font-size:22px;color:#1a2e05;font-weight:800">طلب تسجيل مبكر جديد</h1>
        <p style="margin:6px 0 0;font-size:14px;color:#1a2e05;opacity:0.8">Learn.Gheir — منصة تعلم عربية تكيفية</p>
      </td>
    </tr>
    <tr>
      <td style="padding:32px">
        <table role="presentation" style="width:100%;border-collapse:collapse">
          <tr><td style="padding:10px 14px;background:#f8fafc;border-radius:8px 8px 0 0;font-size:13px;color:#64748b;font-weight:600">الاسم الكامل</td><td style="padding:10px 14px;font-size:15px;font-weight:700;color:#1e293b">$fullName</td></tr>
          <tr><td style="padding:10px 14px;border-top:1px solid #e2e8f0;font-size:13px;color:#64748b;font-weight:600">البريد الإلكتروني</td><td style="padding:10px 14px;border-top:1px solid #e2e8f0;font-size:15px;color:#1e293b;direction:ltr;text-align:right">$email</td></tr>
          <tr><td style="padding:10px 14px;border-top:1px solid #e2e8f0;font-size:13px;color:#64748b;font-weight:600">رقم الهاتف</td><td style="padding:10px 14px;border-top:1px solid #e2e8f0;font-size:15px;color:#1e293b;direction:ltr;text-align:right">$phone</td></tr>
          <tr><td style="padding:10px 14px;border-top:1px solid #e2e8f0;font-size:13px;color:#64748b;font-weight:600">المدينة / الدولة</td><td style="padding:10px 14px;border-top:1px solid #e2e8f0;font-size:15px;color:#1e293b">$city</td></tr>
          <tr><td style="padding:10px 14px;border-top:1px solid #e2e8f0;font-size:13px;color:#64748b;font-weight:600">الصفة</td><td style="padding:10px 14px;border-top:1px solid #e2e8f0"><span style="display:inline-block;padding:2px 12px;border-radius:999px;background:#a8d13e20;color:#a8d13e;font-weight:700;font-size:13px">$roleLabel</span></td></tr>
          <tr><td style="padding:10px 14px;border-top:1px solid #e2e8f0;font-size:13px;color:#64748b;font-weight:600;vertical-align:top">الرسالة</td><td style="padding:10px 14px;border-top:1px solid #e2e8f0;font-size:14px;color:#1e293b;line-height:1.7">$messageHtml</td></tr>
          <tr><td style="padding:10px 14px;border-top:1px solid #e2e8f0;font-size:13px;color:#64748b;font-weight:600">تاريخ التسجيل</td><td style="padding:10px 14px;border-top:1px solid #e2e8f0;font-size:13px;color:#64748b">$dt</td></tr>
        </table>
      </td>
    </tr>
    <tr>
      <td style="background:#f8fafc;padding:16px 32px;text-align:center;border-top:1px solid #e2e8f0">
        <p style="margin:0;font-size:12px;color:#94a3b8">هذه رسالة آلية من منصة Learn.Gheir • جميع الحقوق محفوظة</p>
      </td>
    </tr>
  </table>
</body>
</html>
HTML;

    try {
        $mail->send();
        $emailSent = true;
    } catch (Exception $e) {
        $emailSent = false;
    }
}

echo json_encode([
    'success' => true,
    'message' => 'تم التسجيل بنجاح! شكراً لانضمامك إلى المجموعة التجريبية.',
    'email_sent' => $emailSent
]);
