<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('HTTP/1.0 403 Forbidden');
    echo json_encode(['error' => 'غير مصرح']);
    exit;
}

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_FILES['file'])) {
    echo json_encode(['error' => 'طلب غير صالح']);
    exit;
}

$file = $_FILES['file'];
$allowedTypes = ['image/jpeg', 'image/png', 'image/webp', 'image/gif', 'image/svg+xml'];
$maxSize = 5 * 1024 * 1024; // 5MB

if ($file['error'] !== UPLOAD_ERR_OK) {
    echo json_encode(['error' => 'فشل في رفع الملف']);
    exit;
}

if (!in_array($file['type'], $allowedTypes)) {
    echo json_encode(['error' => 'نوع الملف غير مدعوم. الأنواع المسموحة: JPG, PNG, WebP, GIF, SVG']);
    exit;
}

if ($file['size'] > $maxSize) {
    echo json_encode(['error' => 'حجم الملف كبير جداً. الحد الأقصى 5MB']);
    exit;
}

$ext = pathinfo($file['name'], PATHINFO_EXTENSION);
$filename = uniqid('upload_') . '.' . $ext;
$uploadDir = __DIR__ . '/../assets/img/';

if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

$dest = $uploadDir . $filename;
if (move_uploaded_file($file['tmp_name'], $dest)) {
    echo json_encode([
        'success' => true,
        'url' => 'assets/img/' . $filename,
        'filename' => $filename
    ]);
} else {
    echo json_encode(['error' => 'فشل في حفظ الملف']);
}
