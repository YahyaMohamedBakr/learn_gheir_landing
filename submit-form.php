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
$contactEmail = $settings['site']['contact_email'] ?? 'info@learn.gheir.com';

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

    $emailBody = "طلب تسجيل مبكر جديد\n\n"
        . "الاسم: $fullName\n"
        . "البريد الإلكتروني: $email\n"
        . "رقم الهاتف: $phone\n"
        . "المدينة: $city\n"
        . "الصفة: $roleLabel\n"
        . "الرسالة: " . ($message ?: "لا يوجد") . "\n"
        . "التاريخ: " . date('Y-m-d H:i:s');

    require_once __DIR__ . '/inc/smtp.php';
    $smtp = new SMTP($smtpConfig);
    $result = $smtp->send($contactEmail, 'طلب تسجيل مبكر جديد - Learn.Gheir', $emailBody);
    $emailSent = $result['success'];
}

echo json_encode([
    'success' => true,
    'message' => 'تم التسجيل بنجاح! شكراً لانضمامك إلى المجموعة التجريبية.',
    'email_sent' => $emailSent
]);
