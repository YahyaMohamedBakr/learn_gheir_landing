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

function saveAndExit($settings, $settingsFile, $redirect) {
    file_put_contents($settingsFile, json_encode($settings, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    $_SESSION['admin_msg'] = $_SESSION['admin_msg'] ?? 'تم الحفظ بنجاح';
    $_SESSION['admin_msg_type'] = $_SESSION['admin_msg_type'] ?? 'success';
    header('Location: ' . $redirect);
    exit;
}

if ($section === 'colors') {
    foreach ($settings['colors'] as $key => &$color) {
        if (isset($_POST[$key . '_h'])) {
            $color['h'] = (int)$_POST[$key . '_h'];
            $color['s'] = (int)$_POST[$key . '_s'];
            $color['l'] = (int)$_POST[$key . '_l'];
        }
    }
    unset($color);
    $_SESSION['admin_msg'] = 'تم حفظ الألوان بنجاح';
    saveAndExit($settings, $settingsFile, 'dashboard.php?tab=colors');
}

if ($section === 'content') {
    foreach ($settings['content'] as $key => $value) {
        if (isset($_POST[$key])) {
            $settings['content'][$key] = $_POST[$key];
        }
    }
    $_SESSION['admin_msg'] = 'تم حفظ المحتوى بنجاح';
    saveAndExit($settings, $settingsFile, 'dashboard.php?tab=content');
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
    saveAndExit($settings, $settingsFile, 'dashboard.php?tab=smtp');
}

if ($section === 'site') {
    $settings['site']['title'] = $_POST['site_title'] ?? '';
    $settings['site']['description'] = $_POST['site_description'] ?? '';
    $settings['site']['logo_type'] = $_POST['site_logo_type'] ?? 'text';
    $settings['site']['logo_text'] = $_POST['site_logo_text'] ?? '';
    $settings['site']['logo_image'] = $_POST['site_logo_image'] ?? '';
    $settings['site']['logo_width'] = (int)($_POST['site_logo_width'] ?? 40);
    $settings['site']['logo_height'] = (int)($_POST['site_logo_height'] ?? 40);
    $settings['site']['menu_alignment'] = $_POST['site_menu_alignment'] ?? 'right';
    $settings['site']['contact_email'] = $_POST['site_contact_email'] ?? '';
    $settings['site']['footer_text'] = $_POST['site_footer_text'] ?? '';
    $_SESSION['admin_msg'] = 'تم حفظ إعدادات الموقع بنجاح';
    saveAndExit($settings, $settingsFile, 'dashboard.php?tab=site');
}

if ($section === 'menu') {
    $labels = $_POST['menu_label'] ?? [];
    $hrefs = $_POST['menu_href'] ?? [];
    $items = [];
    foreach ($labels as $i => $label) {
        $label = trim($label);
        $href = trim($hrefs[$i] ?? '');
        if ($label !== '' && $href !== '') {
            $items[] = ['label' => $label, 'href' => $href];
        }
    }
    $settings['menu']['items'] = $items;
    $_SESSION['admin_msg'] = 'تم حفظ القائمة بنجاح';
    saveAndExit($settings, $settingsFile, 'dashboard.php?tab=menu');
}

if ($section === 'sections') {
    foreach ($settings['sections'] as $key => &$sec) {
        $sec['enabled'] = isset($_POST['sec_' . $key]) && $_POST['sec_' . $key] === '1';
    }
    unset($sec);
    $_SESSION['admin_msg'] = 'تم حفظ إعدادات الأقسام بنجاح';
    saveAndExit($settings, $settingsFile, 'dashboard.php?tab=sections');
}

if ($section === 'team') {
    $ids = $_POST['team_id'] ?? [];
    $names = $_POST['team_name'] ?? [];
    $roles = $_POST['team_role'] ?? [];
    $bios = $_POST['team_bio'] ?? [];
    $images = $_POST['team_image'] ?? [];
    $linkedins = $_POST['team_linkedin'] ?? [];
    $team = [];
    foreach ($ids as $i => $id) {
        $name = trim($names[$i] ?? '');
        if ($name === '') continue;
        $team[] = [
            'id' => (int)$id,
            'name' => $name,
            'role' => trim($roles[$i] ?? ''),
            'bio' => trim($bios[$i] ?? ''),
            'image' => trim($images[$i] ?? ''),
            'linkedin' => trim($linkedins[$i] ?? '')
        ];
    }
    $settings['team'] = $team;
    $_SESSION['admin_msg'] = 'تم حفظ فريق العمل بنجاح';
    saveAndExit($settings, $settingsFile, 'dashboard.php?tab=team');
}

if ($section === 'team_new') {
    $settings['team'][] = [
        'id' => time(),
        'name' => 'عضو جديد',
        'role' => '',
        'bio' => '',
        'image' => '',
        'linkedin' => ''
    ];
    $_SESSION['admin_msg'] = 'تم إضافة عضو جديد';
    saveAndExit($settings, $settingsFile, 'dashboard.php?tab=team');
}

if ($section === 'team_delete') {
    $deleteId = (int)($_POST['team_delete_id'] ?? 0);
    $settings['team'] = array_values(array_filter($settings['team'], function($m) use ($deleteId) {
        return $m['id'] !== $deleteId;
    }));
    $_SESSION['admin_msg'] = 'تم حذف العضو';
    saveAndExit($settings, $settingsFile, 'dashboard.php?tab=team');
}

if ($section === 'social') {
    $settings['social']['linkedin'] = $_POST['social_linkedin'] ?? '';
    $settings['social']['twitter'] = $_POST['social_twitter'] ?? '';
    $settings['social']['instagram'] = $_POST['social_instagram'] ?? '';
    $settings['social']['email'] = $_POST['social_email'] ?? '';
    $_SESSION['admin_msg'] = 'تم حفظ روابط التواصل بنجاح';
    saveAndExit($settings, $settingsFile, 'dashboard.php?tab=social');
}

header('Location: dashboard.php');
