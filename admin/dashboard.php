<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: index.php');
    exit;
}

$settingsFile = __DIR__ . '/../data/settings.json';
$settings = json_decode(file_get_contents($settingsFile), true);

$activeTab = $_GET['tab'] ?? 'colors';

$msg = $_SESSION['admin_msg'] ?? '';
$msgType = $_SESSION['admin_msg_type'] ?? '';
unset($_SESSION['admin_msg'], $_SESSION['admin_msg_type']);

function hslToHex($h, $s, $l) {
    $s /= 100;
    $l /= 100;
    $c = (1 - abs(2 * $l - 1)) * $s;
    $x = $c * (1 - abs(fmod($h / 60, 2) - 1));
    $m = $l - $c / 2;
    if ($h < 60) { $r = $c; $g = $x; $b = 0; }
    elseif ($h < 120) { $r = $x; $g = $c; $b = 0; }
    elseif ($h < 180) { $r = 0; $g = $c; $b = $x; }
    elseif ($h < 240) { $r = 0; $g = $x; $b = $c; }
    elseif ($h < 300) { $r = $x; $g = 0; $b = $c; }
    else { $r = $c; $g = 0; $b = $x; }
    $r = round(($r + $m) * 255);
    $g = round(($g + $m) * 255);
    $b = round(($b + $m) * 255);
    return sprintf("#%02x%02x%02x", $r, $g, $b);
}

$site = $settings['site'] ?? [];
$menu = $settings['menu']['items'] ?? [];
$sections = $settings['sections'] ?? [];
$team = $settings['team'] ?? [];
$social = $settings['social'] ?? [];
$content = $settings['content'] ?? [];
$colors = $settings['colors'] ?? [];
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>لوحة التحكم - Learn.Gheir</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/admin.css">
    <style>
      .input-group { display:flex; gap:0.5rem; align-items:center; margin-bottom:0.75rem; }
      .input-group input[type="text"] { flex:1; padding:0.5rem 0.75rem; border:1px solid var(--admin-border); border-radius:0.5rem; font-size:0.875rem; font-family:inherit; }
      .input-group input:focus { outline:none; border-color:var(--admin-primary); box-shadow:0 0 0 3px rgba(59,130,246,0.15); }
      .btn-sm { padding:0.375rem 0.75rem; font-size:0.8125rem; }
      .toggle-group { display:flex; align-items:center; gap:0.75rem; padding:0.75rem; border:1px solid var(--admin-border); border-radius:0.5rem; margin-bottom:0.5rem; }
      .toggle { position:relative; display:inline-block; width:2.75rem; height:1.5rem; flex-shrink:0; }
      .toggle input { opacity:0; width:0; height:0; }
      .toggle-slider { position:absolute; cursor:pointer; top:0; left:0; right:0; bottom:0; background:#cbd5e1; border-radius:1.5rem; transition:0.3s; }
      .toggle-slider::before { content:''; position:absolute; height:1.125rem; width:1.125rem; left:0.1875rem; bottom:0.1875rem; background:#fff; border-radius:50%; transition:0.3s; }
      .toggle input:checked + .toggle-slider { background:var(--admin-primary); }
      .toggle input:checked + .toggle-slider::before { transform:translateX(1.25rem); }
      .team-card { display:grid; grid-template-columns:auto 1fr auto; gap:1rem; align-items:start; padding:1rem; border:1px solid var(--admin-border); border-radius:0.75rem; margin-bottom:1rem; background:var(--admin-card); }
      .team-card .preview { width:4rem; height:4rem; border-radius:0.5rem; object-fit:cover; border:1px solid var(--admin-border); }
      .team-card .fields { display:grid; grid-template-columns:1fr 1fr; gap:0.5rem; }
      .team-card .fields .full { grid-column:1/-1; }
      .team-card .fields input, .team-card .fields textarea { width:100%; padding:0.5rem 0.75rem; border:1px solid var(--admin-border); border-radius:0.375rem; font-size:0.8125rem; font-family:inherit; }
      .team-card .fields textarea { min-height:3rem; resize:vertical; }
      .team-card .fields input:focus, .team-card .fields textarea:focus { outline:none; border-color:var(--admin-primary); }
      .media-grid { display:grid; grid-template-columns:repeat(auto-fill,minmax(150px,1fr)); gap:0.75rem; }
      .media-item { position:relative; border:1px solid var(--admin-border); border-radius:0.5rem; overflow:hidden; aspect-ratio:1; }
      .media-item img { width:100%; height:100%; object-fit:cover; }
      .media-item .media-name { position:absolute; bottom:0; left:0; right:0; background:rgba(0,0,0,0.6); color:#fff; padding:0.25rem 0.5rem; font-size:0.6875rem; text-overflow:ellipsis; overflow:hidden; white-space:nowrap; }
      .section-badge { display:inline-flex; align-items:center; gap:0.25rem; padding:0.125rem 0.5rem; border-radius:999px; font-size:0.6875rem; font-weight:600; }
      .section-badge.on { background:#dcfce7; color:#15803d; }
      .section-badge.off { background:#fef2f2; color:#dc2626; }
      .inline-flex { display:inline-flex; align-items:center; gap:0.5rem; }
      .gap-2 { gap:0.5rem; }
      .gap-4 { gap:1rem; }
      .mt-4 { margin-top:1rem; }
      .mb-4 { margin-bottom:1rem; }
      .flex-wrap { flex-wrap:wrap; }

      /* Modal styles */
      .modal-overlay { position:fixed; inset:0; background:rgba(0,0,0,0.5); display:flex; align-items:center; justify-content:center; z-index:200; padding:1rem; }
      .modal-box { background:#fff; border-radius:1rem; padding:2rem; max-width:32rem; width:100%; max-height:90vh; overflow-y:auto; }
      .modal-actions { display:flex; gap:0.75rem; justify-content:flex-end; margin-top:1.5rem; }
      .dropzone { border:2px dashed var(--admin-border); border-radius:0.75rem; padding:2rem; text-align:center; cursor:pointer; transition:border-color 0.2s, background 0.2s; }
      .dropzone:hover, .dropzone.dragover { border-color:var(--admin-primary); background:rgba(59,130,246,0.05); }
      .dropzone-text { color:var(--admin-text-muted); font-size:0.875rem; }
      .dropzone-text strong { color:var(--admin-primary); }
      .submissions-table { width:100%; border-collapse:collapse; font-size:0.875rem; }
      .submissions-table th { text-align:right; padding:0.625rem 0.75rem; background:var(--admin-bg); border-bottom:2px solid var(--admin-border); font-weight:600; color:var(--admin-text); white-space:nowrap; }
      .submissions-table td { padding:0.5rem 0.75rem; border-bottom:1px solid var(--admin-border); }
      .submissions-table tbody tr:hover { background:rgba(59,130,246,0.04); }
      .submissions-table .btn-danger { background:#ef4444; color:#fff; border:none; border-radius:0.375rem; cursor:pointer; }
      .submissions-table .btn-danger:hover { background:#dc2626; }
    </style>
</head>
<body>
    <div class="admin-header">
        <h1>لوحة التحكم — Learn.Gheir</h1>
        <div style="display:flex;align-items:center;gap:1rem">
            <span style="font-size:0.875rem;color:rgba(255,255,255,0.7)"><?= htmlspecialchars($_SESSION['admin_name']) ?></span>
            <a href="../">عرض الموقع</a>
            <a href="logout.php">تسجيل الخروج</a>
        </div>
    </div>
    <div class="admin-layout">
        <div class="admin-sidebar">
            <a href="?tab=colors" class="<?= $activeTab === 'colors' ? 'active' : '' ?>">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="4"/><path d="M12 2v2"/><path d="M12 20v2"/><path d="m4.93 4.93 1.41 1.41"/><path d="m17.66 17.66 1.41 1.41"/><path d="M2 12h2"/><path d="M20 12h2"/><path d="m6.34 17.66-1.41 1.41"/><path d="m19.07 4.93-1.41 1.41"/></svg>
                <span>الألوان</span>
            </a>
            <a href="?tab=content" class="<?= $activeTab === 'content' ? 'active' : '' ?>">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                <span>المحتوى</span>
            </a>
            <a href="?tab=site" class="<?= $activeTab === 'site' ? 'active' : '' ?>">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2a10 10 0 1 0 10 10 4 4 0 0 1-5-5 4 4 0 0 1-5-5"/><path d="M8.5 8.5a4 4 0 0 0 4 4"/></svg>
                <span>الموقع</span>
            </a>
            <a href="?tab=menu" class="<?= $activeTab === 'menu' ? 'active' : '' ?>">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="4" x2="20" y1="12" y2="12"/><line x1="4" x2="20" y1="6" y2="6"/><line x1="4" x2="20" y1="18" y2="18"/></svg>
                <span>القائمة</span>
            </a>
            <a href="?tab=sections" class="<?= $activeTab === 'sections' ? 'active' : '' ?>">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="18" x="3" y="3" rx="2"/><line x1="3" x2="21" y1="9" y2="9"/><line x1="9" x2="9" y1="21" y2="9"/></svg>
                <span>الأقسام</span>
            </a>
            <a href="?tab=team" class="<?= $activeTab === 'team' ? 'active' : '' ?>">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                <span>الفريق</span>
            </a>
            <a href="?tab=media" class="<?= $activeTab === 'media' ? 'active' : '' ?>">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="18" x="3" y="3" rx="2" ry="2"/><circle cx="9" cy="9" r="2"/><path d="m21 15-3.086-3.086a2 2 0 0 0-2.828 0L6 21"/></svg>
                <span>الوسائط</span>
            </a>
            <a href="?tab=social" class="<?= $activeTab === 'social' ? 'active' : '' ?>">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 8a6 6 0 0 1 6 6v7h-4v-7a2 2 0 0 0-2-2 2 2 0 0 0-2 2v7h-4v-7a6 6 0 0 1 6-6z"/><rect width="4" height="12" x="2" y="9"/><circle cx="4" cy="4" r="2"/></svg>
                <span>التواصل</span>
            </a>
            <a href="?tab=smtp" class="<?= $activeTab === 'smtp' ? 'active' : '' ?>">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="20" height="16" x="2" y="4" rx="2"/><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/></svg>
                <span>إعدادات SMTP</span>
            </a>
            <a href="?tab=submissions" class="<?= $activeTab === 'submissions' ? 'active' : '' ?>">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" x2="8" y1="13" y2="13"/><line x1="16" x2="8" y1="17" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
                <span>الطلبات</span>
            </a>
        </div>
        <div class="admin-main">
            <div class="section-header">
                <h2>
                    <?php if ($activeTab === 'colors'): ?>تخصيص الألوان
                    <?php elseif ($activeTab === 'content'): ?>إدارة المحتوى
                    <?php elseif ($activeTab === 'site'): ?>إعدادات الموقع
                    <?php elseif ($activeTab === 'menu'): ?>إدارة القائمة
                    <?php elseif ($activeTab === 'sections'): ?>إظهار/إخفاء الأقسام
                    <?php elseif ($activeTab === 'team'): ?>إدارة فريق العمل
                    <?php elseif ($activeTab === 'media'): ?>مكتبة الوسائط
                    <?php elseif ($activeTab === 'social'): ?>روابط التواصل الاجتماعي
                    <?php elseif ($activeTab === 'smtp'): ?>إعدادات البريد الإلكتروني (SMTP)
                    <?php elseif ($activeTab === 'submissions'): ?>طلبات التسجيل المبكر
                    <?php endif; ?>
                </h2>
                <a href="../" target="_blank" class="btn btn-outline">عرض الموقع</a>
            </div>

            <?php if ($msg): ?>
                <div class="msg msg-<?= $msgType ?>"><?= htmlspecialchars($msg) ?></div>
            <?php endif; ?>

            <!-- Colors Tab -->
            <?php if ($activeTab === 'colors'): ?>
            <form method="POST" action="save.php">
                <input type="hidden" name="_section" value="colors">
                <div class="color-grid">
                    <?php foreach ($colors as $key => $color):
                        $hex = hslToHex($color['h'], $color['s'], $color['l']);
                    ?>
                    <div class="color-item">
                        <div class="color-preview preview" style="background:hsl(<?= $color['h'] ?>,<?= $color['s'] ?>%,<?= $color['l'] ?>%)"></div>
                        <div style="flex:1">
                            <label><?= htmlspecialchars($color['label']) ?></label>
                            <div style="display:flex;gap:0.25rem;margin-top:0.25rem">
                                <input type="color" value="<?= $hex ?>"
                                       onchange="this.closest('.color-item').querySelector('.h').value=Math.round(hslFromHex(this.value).h);
                                                this.closest('.color-item').querySelector('.s').value=Math.round(hslFromHex(this.value).s);
                                                this.closest('.color-item').querySelector('.l').value=Math.round(hslFromHex(this.value).l);
                                                this.closest('.color-item').querySelector('.preview').style.background=this.value;" style="width:2.5rem;height:2.25rem">
                                <input type="number" class="h" name="<?= $key ?>_h" value="<?= $color['h'] ?>" min="0" max="360" step="1" style="width:3.5rem;padding:0.25rem;text-align:center;font-size:0.75rem;border:1px solid var(--admin-border);border-radius:0.25rem" title="Hue">
                                <input type="number" class="s" name="<?= $key ?>_s" value="<?= $color['s'] ?>" min="0" max="100" step="1" style="width:3rem;padding:0.25rem;text-align:center;font-size:0.75rem;border:1px solid var(--admin-border);border-radius:0.25rem" title="Saturation">
                                <input type="number" class="l" name="<?= $key ?>_l" value="<?= $color['l'] ?>" min="0" max="100" step="1" style="width:3rem;padding:0.25rem;text-align:center;font-size:0.75rem;border:1px solid var(--admin-border);border-radius:0.25rem" title="Lightness">
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <div style="margin-top:1.5rem">
                    <button type="submit" class="btn btn-primary">حفظ الألوان</button>
                </div>
            </form>
            <?php endif; ?>

            <!-- Content Tab -->
            <?php if ($activeTab === 'content'): ?>
            <form method="POST" action="save.php">
                <input type="hidden" name="_section" value="content">
                <div class="card">
                    <h3 style="margin-bottom:1rem;font-size:1.125rem">قسم الهيرو (Hero)</h3>
                    <div class="form-group">
                        <label>النص الصغير (Badge)</label>
                        <input type="text" name="hero_badge" value="<?= htmlspecialchars($content['hero_badge']) ?>">
                    </div>
                    <div class="form-group">
                        <label>العنوان الرئيسي</label>
                        <input type="text" name="hero_title" value="<?= htmlspecialchars($content['hero_title']) ?>">
                    </div>
                    <div class="form-group">
                        <label>النص الفرعي</label>
                        <textarea name="hero_subtitle" rows="3"><?= htmlspecialchars($content['hero_subtitle']) ?></textarea>
                    </div>
                    <div class="form-row" style="display:grid;grid-template-columns:1fr 1fr;gap:1rem">
                        <div class="form-group">
                            <label>نص زر CTA الأساسي</label>
                            <input type="text" name="hero_cta_primary" value="<?= htmlspecialchars($content['hero_cta_primary']) ?>">
                        </div>
                        <div class="form-group">
                            <label>نص زر CTA الثانوي</label>
                            <input type="text" name="hero_cta_secondary" value="<?= htmlspecialchars($content['hero_cta_secondary']) ?>">
                        </div>
                    </div>
                </div>

                <div class="card">
                    <h3 style="margin-bottom:1rem;font-size:1.125rem">قسم المشكلة</h3>
                    <div class="form-group">
                        <label>العنوان</label>
                        <input type="text" name="problem_title" value="<?= htmlspecialchars($content['problem_title']) ?>">
                    </div>
                    <div class="form-group">
                        <label>النص الفرعي</label>
                        <textarea name="problem_subtitle" rows="2"><?= htmlspecialchars($content['problem_subtitle']) ?></textarea>
                    </div>
                    <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:1rem">
                        <div>
                            <div class="form-group">
                                <label>العنوان 1</label>
                                <input type="text" name="problem_1_title" value="<?= htmlspecialchars($content['problem_1_title']) ?>">
                            </div>
                            <div class="form-group">
                                <label>الوصف 1</label>
                                <textarea name="problem_1_desc" rows="3"><?= htmlspecialchars($content['problem_1_desc']) ?></textarea>
                            </div>
                        </div>
                        <div>
                            <div class="form-group">
                                <label>العنوان 2</label>
                                <input type="text" name="problem_2_title" value="<?= htmlspecialchars($content['problem_2_title']) ?>">
                            </div>
                            <div class="form-group">
                                <label>الوصف 2</label>
                                <textarea name="problem_2_desc" rows="3"><?= htmlspecialchars($content['problem_2_desc']) ?></textarea>
                            </div>
                        </div>
                        <div>
                            <div class="form-group">
                                <label>العنوان 3</label>
                                <input type="text" name="problem_3_title" value="<?= htmlspecialchars($content['problem_3_title']) ?>">
                            </div>
                            <div class="form-group">
                                <label>الوصف 3</label>
                                <textarea name="problem_3_desc" rows="3"><?= htmlspecialchars($content['problem_3_desc']) ?></textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <h3 style="margin-bottom:1rem;font-size:1.125rem">قسم الحل</h3>
                    <div class="form-group">
                        <label>العنوان</label>
                        <input type="text" name="solution_title" value="<?= htmlspecialchars($content['solution_title']) ?>">
                    </div>
                    <div class="form-group">
                        <label>النص الفرعي</label>
                        <textarea name="solution_subtitle" rows="3"><?= htmlspecialchars($content['solution_subtitle']) ?></textarea>
                    </div>
                </div>

                <div class="card">
                    <h3 style="margin-bottom:1rem;font-size:1.125rem">قسم المميزات</h3>
                    <div class="form-group">
                        <label>العنوان</label>
                        <input type="text" name="features_title" value="<?= htmlspecialchars($content['features_title']) ?>">
                    </div>
                    <div class="form-group">
                        <label>النص الفرعي</label>
                        <textarea name="features_subtitle" rows="2"><?= htmlspecialchars($content['features_subtitle']) ?></textarea>
                    </div>
                    <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:1rem">
                        <?php for ($i = 1; $i <= 6; $i++): ?>
                        <div>
                            <div class="form-group">
                                <label>العنوان <?= $i ?></label>
                                <input type="text" name="feature_<?= $i ?>_title" value="<?= htmlspecialchars($content['feature_' . $i . '_title']) ?>">
                            </div>
                            <div class="form-group">
                                <label>الوصف <?= $i ?></label>
                                <textarea name="feature_<?= $i ?>_desc" rows="3"><?= htmlspecialchars($content['feature_' . $i . '_desc']) ?></textarea>
                            </div>
                        </div>
                        <?php endfor; ?>
                    </div>
                </div>

                <div class="card">
                    <h3 style="margin-bottom:1rem;font-size:1.125rem">قسم رؤية 2030</h3>
                    <div class="form-group">
                        <label>العنوان</label>
                        <input type="text" name="vision_title" value="<?= htmlspecialchars($content['vision_title']) ?>">
                    </div>
                    <div class="form-group">
                        <label>النص الفرعي</label>
                        <textarea name="vision_subtitle" rows="2"><?= htmlspecialchars($content['vision_subtitle']) ?></textarea>
                    </div>
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem">
                        <?php for ($i = 1; $i <= 4; $i++): ?>
                        <div>
                            <div class="form-group">
                                <label>العنوان <?= $i ?></label>
                                <input type="text" name="vision_<?= $i ?>_title" value="<?= htmlspecialchars($content['vision_' . $i . '_title']) ?>">
                            </div>
                            <div class="form-group">
                                <label>الوصف <?= $i ?></label>
                                <textarea name="vision_<?= $i ?>_desc" rows="2"><?= htmlspecialchars($content['vision_' . $i . '_desc']) ?></textarea>
                            </div>
                        </div>
                        <?php endfor; ?>
                    </div>
                </div>

                <div class="card">
                    <h3 style="margin-bottom:1rem;font-size:1.125rem">قسم الفريق</h3>
                    <div class="form-group">
                        <label>العنوان</label>
                        <input type="text" name="team_title" value="<?= htmlspecialchars($content['team_title']) ?>">
                    </div>
                    <div class="form-group">
                        <label>النص الفرعي</label>
                        <textarea name="team_subtitle" rows="2"><?= htmlspecialchars($content['team_subtitle']) ?></textarea>
                    </div>
                </div>

                <div class="card">
                    <h3 style="margin-bottom:1rem;font-size:1.125rem">قسم الفورم (CTA)</h3>
                    <div class="form-group">
                        <label>عنوان الفورم</label>
                        <input type="text" name="form_title" value="<?= htmlspecialchars($content['form_title']) ?>">
                    </div>
                    <div class="form-group">
                        <label>نص الفورم الفرعي</label>
                        <textarea name="form_subtitle" rows="2"><?= htmlspecialchars($content['form_subtitle']) ?></textarea>
                    </div>
                </div>

                <div class="card">
                    <h3 style="margin-bottom:1rem;font-size:1.125rem">قسم CTA السفلي</h3>
                    <div class="form-group">
                        <label>العنوان</label>
                        <input type="text" name="cta_title" value="<?= htmlspecialchars($content['cta_title']) ?>">
                    </div>
                    <div class="form-group">
                        <label>النص الفرعي</label>
                        <textarea name="cta_subtitle" rows="2"><?= htmlspecialchars($content['cta_subtitle']) ?></textarea>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary">حفظ المحتوى</button>
            </form>
            <?php endif; ?>

            <!-- Site Tab -->
            <?php if ($activeTab === 'site'): ?>
            <div class="card">
                <form method="POST" action="save.php">
                    <input type="hidden" name="_section" value="site">
                    <h3 style="margin-bottom:1rem;font-size:1.125rem">المعلومات الأساسية</h3>
                    <div class="form-group">
                        <label>عنوان الموقع (Site Title)</label>
                        <input type="text" name="site_title" value="<?= htmlspecialchars($site['title'] ?? '') ?>">
                    </div>
                    <div class="form-group">
                        <label>وصف الموقع (Meta Description)</label>
                        <textarea name="site_description" rows="2"><?= htmlspecialchars($site['description'] ?? '') ?></textarea>
                    </div>
                    <div class="form-group">
                        <label>بريد التواصل</label>
                        <input type="email" name="site_contact_email" value="<?= htmlspecialchars($site['contact_email'] ?? '') ?>">
                    </div>
                    <div class="form-group">
                        <label>نص التذييل (Footer)</label>
                        <input type="text" name="site_footer_text" value="<?= htmlspecialchars($site['footer_text'] ?? '') ?>">
                    </div>

                    <h3 style="margin:1.5rem 0 1rem;font-size:1.125rem">إعدادات الشعار (Logo)</h3>
                    <div class="form-group">
                        <label>نوع الشعار</label>
                        <select name="site_logo_type" onchange="toggleLogoType(this.value)">
                            <option value="text" <?= ($site['logo_type'] ?? 'text') === 'text' ? 'selected' : '' ?>>نص</option>
                            <option value="image" <?= ($site['logo_type'] ?? 'text') === 'image' ? 'selected' : '' ?>>صورة</option>
                        </select>
                    </div>
                    <div id="logoTextField" style="display:<?= ($site['logo_type'] ?? 'text') === 'text' ? 'block' : 'none' ?>">
                        <div class="form-group">
                            <label>نص الشعار</label>
                            <input type="text" name="site_logo_text" value="<?= htmlspecialchars($site['logo_text'] ?? '') ?>">
                        </div>
                    </div>
                    <div id="logoImageField" style="display:<?= ($site['logo_type'] ?? 'text') === 'image' ? 'block' : 'none' ?>">
                        <div class="form-group">
                            <label>رابط صورة الشعار</label>
                            <div style="display:flex;gap:0.5rem">
                                <input type="text" name="site_logo_image" id="logoImageInput" value="<?= htmlspecialchars($site['logo_image'] ?? '') ?>" placeholder="assets/img/logo.png">
                                <button type="button" class="btn btn-sm btn-outline" onclick="document.getElementById('logoFileInput').click()">اختر</button>
                                <input type="file" id="logoFileInput" accept="image/*" style="display:none" onchange="uploadLogo(this)">
                            </div>
                            <?php if (!empty($site['logo_image'])): ?>
                            <div style="margin-top:0.5rem"><img src="../<?= htmlspecialchars($site['logo_image']) ?>" style="max-width:120px;max-height:60px;border:1px solid var(--admin-border);border-radius:0.375rem;padding:0.25rem"></div>
                            <?php endif; ?>
                        </div>
                        <div style="display:grid;grid-template-columns:1fr 1fr;gap:0.75rem">
                            <div class="form-group">
                                <label>العرض (px)</label>
                                <input type="number" name="site_logo_width" value="<?= $site['logo_width'] ?? 40 ?>" min="16" max="500">
                            </div>
                            <div class="form-group">
                                <label>الارتفاع (px)</label>
                                <input type="number" name="site_logo_height" value="<?= $site['logo_height'] ?? 40 ?>" min="16" max="500">
                            </div>
                        </div>
                    </div>

                    <h3 style="margin:1.5rem 0 1rem;font-size:1.125rem">وضعية القائمة</h3>
                    <div class="form-group">
                        <label>محاذاة القائمة في الهيدر</label>
                        <select name="site_menu_position">
                            <option value="right" <?= ($site['menu_position'] ?? 'center') === 'right' ? 'selected' : '' ?>>على اليمين (بجوار اللوجو)</option>
                            <option value="center" <?= ($site['menu_position'] ?? 'center') === 'center' ? 'selected' : '' ?>>في المنتصف</option>
                            <option value="left" <?= ($site['menu_position'] ?? 'center') === 'left' ? 'selected' : '' ?>>على الشمال (قبل زر CTA)</option>
                        </select>
                    </div>

                    <button type="submit" class="btn btn-primary">حفظ الإعدادات</button>
                </form>
            </div>
            <script>
            function toggleLogoType(val) {
                document.getElementById('logoTextField').style.display = val === 'text' ? 'block' : 'none';
                document.getElementById('logoImageField').style.display = val === 'image' ? 'block' : 'none';
            }
            function uploadLogo(input) {
                var file = input.files[0];
                if (!file) return;
                var formData = new FormData();
                formData.append('file', file);
                var btn = input.previousElementSibling;
                btn.textContent = 'جاري...';
                fetch('upload.php', { method: 'POST', body: formData })
                    .then(function(r) { return r.json(); })
                    .then(function(data) {
                        btn.textContent = 'اختر';
                        if (data.success) {
                            document.getElementById('logoImageInput').value = data.url;
                        } else {
                            alert(data.error);
                        }
                    })
                    .catch(function() { btn.textContent = 'اختر'; alert('فشل الرفع'); });
            }
            </script>
            <?php endif; ?>

            <!-- Menu Tab -->
            <?php if ($activeTab === 'menu'): ?>
            <div class="card">
                <p style="color:var(--admin-text-muted);margin-bottom:1rem;font-size:0.875rem">قم بإدارة عناصر القائمة العلوية. يتم ترتيب العناصر حسب الترتيب الظاهر أدناه.</p>
                <form method="POST" action="save.php" id="menuForm">
                    <input type="hidden" name="_section" value="menu">
                    <div id="menuItems">
                        <?php foreach ($menu as $i => $item): ?>
                        <div class="input-group" data-index="<?= $i ?>">
                            <input type="text" name="menu_label[]" value="<?= htmlspecialchars($item['label']) ?>" placeholder="النص" style="flex:2">
                            <input type="text" name="menu_href[]" value="<?= htmlspecialchars($item['href']) ?>" placeholder="الرابط (مثال: #home)" style="flex:3">
                            <button type="button" class="btn btn-sm btn-outline" onclick="this.closest('.input-group').remove()">✕</button>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <button type="button" class="btn btn-sm btn-outline" onclick="addMenuItem()" style="margin-top:0.5rem">+ إضافة عنصر</button>
                    <div style="margin-top:1rem">
                        <button type="submit" class="btn btn-primary">حفظ القائمة</button>
                    </div>
                </form>
            </div>
            <script>
            function addMenuItem() {
                var div = document.createElement('div');
                div.className = 'input-group';
                div.innerHTML = '<input type="text" name="menu_label[]" placeholder="النص" style="flex:2">' +
                    '<input type="text" name="menu_href[]" placeholder="الرابط (مثال: #home)" style="flex:3">' +
                    '<button type="button" class="btn btn-sm btn-outline" onclick="this.parentElement.remove()">✕</button>';
                document.getElementById('menuItems').appendChild(div);
            }
            </script>
            <?php endif; ?>

            <!-- Sections Tab -->
            <?php if ($activeTab === 'sections'): ?>
            <div class="card">
                <p style="color:var(--admin-text-muted);margin-bottom:1rem;font-size:0.875rem">تحكم في إظهار أو إخفاء أقسام الصفحة الرئيسية.</p>
                <form method="POST" action="save.php">
                    <input type="hidden" name="_section" value="sections">
                    <?php foreach ($sections as $key => $sec): ?>
                    <div class="toggle-group">
                        <label class="toggle">
                            <input type="hidden" name="sec_<?= $key ?>" value="0">
                            <input type="checkbox" name="sec_<?= $key ?>" value="1" <?= $sec['enabled'] ? 'checked' : '' ?>>
                            <span class="toggle-slider"></span>
                        </label>
                        <div style="flex:1">
                            <strong style="font-size:0.9375rem"><?= htmlspecialchars($sec['label']) ?></strong>
                        </div>
                        <span class="section-badge <?= $sec['enabled'] ? 'on' : 'off' ?>">
                            <?= $sec['enabled'] ? 'ظاهر' : 'مخفي' ?>
                        </span>
                    </div>
                    <?php endforeach; ?>
                    <button type="submit" class="btn btn-primary" style="margin-top:1rem">حفظ إعدادات الأقسام</button>
                </form>
            </div>
            <?php endif; ?>

            <!-- Team Tab -->
            <?php if ($activeTab === 'team'): ?>
            <div class="card">
                <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1rem">
                    <p style="color:var(--admin-text-muted);font-size:0.875rem">إدارة أعضاء الفريق: الأسماء، الصور، المناصب، وروابط LinkedIn.</p>
                    <form method="POST" action="save.php" style="display:inline">
                        <input type="hidden" name="_section" value="team_new">
                        <button type="submit" class="btn btn-sm btn-primary">+ إضافة عضو</button>
                    </form>
                </div>
                <form method="POST" action="save.php" id="teamForm">
                    <input type="hidden" name="_section" value="team">
                    <?php foreach ($team as $member): ?>
                    <div class="team-card">
                        <img src="../<?= htmlspecialchars($member['image'] ?? '') ?>" alt="" class="preview" onerror="this.src='data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 100 100%22><rect fill=%22%23e2e8f0%22 width=%22100%22 height=%22100%22/><text x=%2250%22 y=%2255%22 text-anchor=%22middle%22 fill=%22%2394a3b8%22 font-size=%2230%22 font-family=%22sans-serif%22>?</text></svg>'">
                        <div class="fields">
                            <input type="hidden" name="team_id[]" value="<?= $member['id'] ?>">
                            <div class="full" style="display:grid;grid-template-columns:1fr 1fr;gap:0.5rem">
                                <input type="text" name="team_name[]" value="<?= htmlspecialchars($member['name']) ?>" placeholder="الاسم">
                                <input type="text" name="team_role[]" value="<?= htmlspecialchars($member['role']) ?>" placeholder="المنصب">
                            </div>
                            <div class="full"><textarea name="team_bio[]" placeholder="نبذة تعريفية"><?= htmlspecialchars($member['bio']) ?></textarea></div>
                            <input type="text" name="team_image[]" value="<?= htmlspecialchars($member['image']) ?>" placeholder="رابط الصورة (مثال: assets/img/photo.jpg)" style="grid-column:1/-1">
                            <input type="text" name="team_linkedin[]" value="<?= htmlspecialchars($member['linkedin'] ?? '') ?>" placeholder="رابط LinkedIn" style="grid-column:1/-1">
                        </div>
                        <div>
                            <button type="button" class="btn btn-sm btn-danger" onclick="deleteTeamMember(<?= $member['id'] ?>)">✕</button>
                        </div>
                    </div>
                    <?php endforeach; ?>
                    <button type="submit" class="btn btn-primary">حفظ فريق العمل</button>
                </form>
            </div>
            <form method="POST" action="save.php" id="deleteTeamForm" style="display:none">
                <input type="hidden" name="_section" value="team_delete">
                <input type="hidden" name="team_delete_id" id="teamDeleteId" value="">
            </form>
            <script>
            function deleteTeamMember(id) {
                if (confirm('هل أنت متأكد من حذف هذا العضو؟')) {
                    document.getElementById('teamDeleteId').value = id;
                    document.getElementById('deleteTeamForm').submit();
                }
            }

            function pickImage(input) {
                var file = input.files[0];
                if (!file) return;
                var formData = new FormData();
                formData.append('file', file);
                fetch('upload.php', { method: 'POST', body: formData })
                    .then(function(r) { return r.json(); })
                    .then(function(data) {
                        if (data.success) {
                            var field = input.closest('.team-card').querySelector('[name="team_image[]"]');
                            if (field) field.value = data.url;
                            input.closest('.team-card').querySelector('.preview').src = '../' + data.url;
                        } else {
                            alert(data.error);
                        }
                    })
                    .catch(function() { alert('فشل في رفع الصورة'); });
            }
            </script>
            <?php endif; ?>

            <!-- Media Tab -->
            <?php if ($activeTab === 'media'): ?>
            <div class="card">
                <div style="margin-bottom:1rem">
                    <button class="btn btn-primary" onclick="showUploadModal()">رفع ملف جديد</button>
                </div>
                <?php
                $imgDir = __DIR__ . '/../assets/img/';
                $images = is_dir($imgDir) ? array_diff(scandir($imgDir), ['.', '..']) : [];
                ?>
                <?php if (count($images) > 0): ?>
                <div class="media-grid">
                    <?php foreach ($images as $img): ?>
                    <div class="media-item">
                        <img src="../assets/img/<?= rawurlencode($img) ?>" alt="<?= htmlspecialchars($img) ?>">
                        <div class="media-name"><?= htmlspecialchars($img) ?></div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php else: ?>
                <p style="color:var(--admin-text-muted);font-size:0.875rem;text-align:center;padding:2rem">لا توجد صور مرفوعة بعد</p>
                <?php endif; ?>
            </div>

            <div id="uploadModal" class="modal-overlay" style="display:none" onclick="if(event.target===this)hideUploadModal()">
                <div class="modal-box">
                    <h3 style="margin-bottom:1rem;font-size:1.125rem">رفع ملف</h3>
                    <div class="dropzone" id="dropzone">
                        <div class="dropzone-text">
                            <strong>اختر ملفاً</strong> أو اسحب وأفلت الصورة هنا<br>
                            <small style="color:var(--admin-text-muted)">JPG, PNG, WebP, GIF, SVG — حد أقصى 5MB</small>
                        </div>
                    </div>
                    <input type="file" id="fileInput" accept="image/*" style="display:none">
                    <div id="uploadPreview" style="display:none;margin-top:1rem;text-align:center">
                        <img id="previewImg" style="max-width:200px;max-height:200px;border-radius:0.5rem">
                        <p id="uploadStatus" style="margin-top:0.5rem;font-size:0.875rem;color:var(--admin-text-muted)"></p>
                    </div>
                    <div class="modal-actions">
                        <button class="btn btn-outline" onclick="hideUploadModal()">إلغاء</button>
                    </div>
                </div>
            </div>
            <script>
            function showUploadModal() {
                document.getElementById('uploadModal').style.display = 'flex';
            }
            function hideUploadModal() {
                document.getElementById('uploadModal').style.display = 'none';
                document.getElementById('uploadPreview').style.display = 'none';
                document.getElementById('fileInput').value = '';
            }
            var dropzone = document.getElementById('dropzone');
            var fileInput = document.getElementById('fileInput');
            dropzone.addEventListener('click', function() { fileInput.click(); });
            dropzone.addEventListener('dragover', function(e) { e.preventDefault(); this.classList.add('dragover'); });
            dropzone.addEventListener('dragleave', function() { this.classList.remove('dragover'); });
            dropzone.addEventListener('drop', function(e) {
                e.preventDefault();
                this.classList.remove('dragover');
                if (e.dataTransfer.files.length > 0) {
                    fileInput.files = e.dataTransfer.files;
                    uploadFile(fileInput.files[0]);
                }
            });
            fileInput.addEventListener('change', function() {
                if (this.files.length > 0) uploadFile(this.files[0]);
            });
            function uploadFile(file) {
                var preview = document.getElementById('uploadPreview');
                document.getElementById('previewImg').src = URL.createObjectURL(file);
                document.getElementById('uploadStatus').textContent = 'جاري الرفع...';
                preview.style.display = 'block';
                var formData = new FormData();
                formData.append('file', file);
                fetch('upload.php', { method: 'POST', body: formData })
                    .then(function(r) { return r.json(); })
                    .then(function(data) {
                        if (data.success) {
                            document.getElementById('uploadStatus').textContent = 'تم الرفع بنجاح ✓';
                            setTimeout(function() { location.reload(); }, 1000);
                        } else {
                            document.getElementById('uploadStatus').textContent = 'خطأ: ' + data.error;
                        }
                    })
                    .catch(function() {
                        document.getElementById('uploadStatus').textContent = 'فشل الاتصال';
                    });
            }
            </script>
            <?php endif; ?>

            <!-- Social Tab -->
            <?php if ($activeTab === 'social'): ?>
            <div class="card">
                <p style="color:var(--admin-text-muted);margin-bottom:1.5rem;font-size:0.875rem">روابط التواصل الاجتماعي التي تظهر في تذييل الصفحة.</p>
                <form method="POST" action="save.php">
                    <input type="hidden" name="_section" value="social">
                    <div class="smtp-grid">
                        <div class="form-group">
                            <label>رابط LinkedIn</label>
                            <input type="url" name="social_linkedin" value="<?= htmlspecialchars($social['linkedin'] ?? '') ?>" placeholder="https://linkedin.com/...">
                        </div>
                        <div class="form-group">
                            <label>رابط Twitter / X</label>
                            <input type="url" name="social_twitter" value="<?= htmlspecialchars($social['twitter'] ?? '') ?>" placeholder="https://twitter.com/...">
                        </div>
                        <div class="form-group">
                            <label>رابط Instagram</label>
                            <input type="url" name="social_instagram" value="<?= htmlspecialchars($social['instagram'] ?? '') ?>" placeholder="https://instagram.com/...">
                        </div>
                        <div class="form-group">
                            <label>البريد الإلكتروني</label>
                            <input type="email" name="social_email" value="<?= htmlspecialchars($social['email'] ?? '') ?>" placeholder="info@example.com">
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary">حفظ الروابط</button>
                </form>
            </div>
            <?php endif; ?>

            <!-- SMTP Tab -->
            <?php if ($activeTab === 'smtp'): ?>
            <div class="card">
                <p style="color:var(--admin-text-muted);margin-bottom:1.5rem;font-size:0.9375rem">قم بإعداد خادم البريد الإلكتروني لإرسال الإشعارات وتنبيهات التسجيل.</p>
                <form method="POST" action="save.php">
                    <input type="hidden" name="_section" value="smtp">
                    <div class="smtp-grid">
                        <div class="form-group">
                            <label>خادم SMTP *</label>
                            <input type="text" name="smtp_host" value="<?= htmlspecialchars($settings['smtp']['host']) ?>" placeholder="smtp.gmail.com">
                        </div>
                        <div class="form-group">
                            <label>المنفذ *</label>
                            <input type="number" name="smtp_port" value="<?= $settings['smtp']['port'] ?>" placeholder="587">
                        </div>
                        <div class="form-group">
                            <label>التشفير</label>
                            <select name="smtp_encryption">
                                <option value="tls" <?= $settings['smtp']['encryption'] === 'tls' ? 'selected' : '' ?>>TLS</option>
                                <option value="ssl" <?= $settings['smtp']['encryption'] === 'ssl' ? 'selected' : '' ?>>SSL</option>
                                <option value="none" <?= $settings['smtp']['encryption'] === 'none' ? 'selected' : '' ?>>بدون تشفير</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>اسم المستخدم *</label>
                            <input type="text" name="smtp_username" value="<?= htmlspecialchars($settings['smtp']['username']) ?>" placeholder="example@gmail.com">
                        </div>
                        <div class="form-group">
                            <label>كلمة المرور *</label>
                            <input type="password" name="smtp_password" value="" placeholder="أدخل كلمة المرور">
                            <small style="color:var(--admin-text-muted);font-size:0.75rem">اتركه فارغاً إذا لا تريد تغييره</small>
                        </div>
                        <div class="form-group">
                            <label>البريد الإلكتروني للمرسل *</label>
                            <input type="email" name="smtp_from_email" value="<?= htmlspecialchars($settings['smtp']['from_email']) ?>" placeholder="noreply@learn.gheir.com">
                        </div>
                        <div class="form-group">
                            <label>اسم المرسل</label>
                            <input type="text" name="smtp_from_name" value="<?= htmlspecialchars($settings['smtp']['from_name']) ?>" placeholder="Learn.Gheir">
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary">حفظ إعدادات SMTP</button>
                </form>
            </div>

            <div class="card">
                <h3 style="margin-bottom:1rem;font-size:1.125rem">اختبار إعدادات SMTP</h3>
                <p style="color:var(--admin-text-muted);margin-bottom:1rem;font-size:0.875rem">أرسل بريد اختبار للتأكد من صحة الإعدادات.</p>
                <form method="POST" action="test-smtp.php">
                    <div style="display:flex;gap:1rem;align-items:end">
                        <div class="form-group" style="flex:1;margin-bottom:0">
                            <label>أرسل إلى</label>
                            <input type="email" name="test_email" required placeholder="your@email.com">
                        </div>
                        <button type="submit" class="btn btn-outline">إرسال اختبار</button>
                    </div>
                </form>
            </div>
            <?php endif; ?>

            <!-- Submissions Tab -->
            <?php if ($activeTab === 'submissions'):
                $submissionsFile = __DIR__ . '/../data/submissions.json';
                $allSubmissions = [];
                if (file_exists($submissionsFile)) {
                    $allSubmissions = json_decode(file_get_contents($submissionsFile), true) ?? [];
                }
                $allSubmissions = array_reverse($allSubmissions);
                $roleLabels = [
                    'student' => 'طالب',
                    'parent' => 'ولي أمر',
                    'teacher' => 'معلم',
                    'institution' => 'مؤسسة تعليمية',
                    'investor' => 'مستثمر',
                    'partner' => 'شريك محتمل'
                ];
            ?>
            <div class="card">
                <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1rem;flex-wrap:wrap;gap:0.5rem">
                    <p style="color:var(--admin-text-muted);font-size:0.875rem">جميع طلبات التسجيل المبكر من الموقع.</p>
                    <?php if (count($allSubmissions) > 0): ?>
                    <form method="POST" action="save.php" onsubmit="return confirm('هل أنت متأكد من حذف جميع الطلبات؟')">
                        <input type="hidden" name="_section" value="submissions_clear">
                        <button type="submit" class="btn btn-sm btn-danger">حذف الكل</button>
                    </form>
                    <?php endif; ?>
                </div>
                <?php if (count($allSubmissions) > 0): ?>
                <div style="overflow-x:auto">
                    <table class="submissions-table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>الاسم</th>
                                <th>البريد الإلكتروني</th>
                                <th>رقم الهاتف</th>
                                <th>المدينة</th>
                                <th>الصفة</th>
                                <th>الرسالة</th>
                                <th>التاريخ</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $i = 1; foreach ($allSubmissions as $sub): ?>
                            <tr>
                                <td><?= $i++ ?></td>
                                <td><strong><?= htmlspecialchars($sub['fullName'] ?? '') ?></strong></td>
                                <td><a href="mailto:<?= htmlspecialchars($sub['email'] ?? '') ?>" style="color:var(--admin-primary);text-decoration:none"><?= htmlspecialchars($sub['email'] ?? '') ?></a></td>
                                <td dir="ltr" style="text-align:right"><?= htmlspecialchars($sub['phone'] ?? '') ?></td>
                                <td><?= htmlspecialchars($sub['city'] ?? '') ?></td>
                                <td><?= htmlspecialchars($roleLabels[$sub['role'] ?? ''] ?? $sub['role'] ?? '') ?></td>
                                <td style="max-width:200px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap" title="<?= htmlspecialchars($sub['message'] ?? '') ?>">
                                    <?= htmlspecialchars($sub['message'] ?: '-') ?>
                                </td>
                                <td style="font-size:0.8125rem;color:var(--admin-text-muted);white-space:nowrap">
                                    <?php
                                    $ts = $sub['timestamp'] ?? '';
                                    echo $ts ? date('Y-m-d H:i', strtotime($ts)) : '-';
                                    ?>
                                </td>
                                <td>
                                    <form method="POST" action="save.php" onsubmit="return confirm('حذف هذا الطلب؟')">
                                        <input type="hidden" name="_section" value="submissions_delete">
                                        <input type="hidden" name="submission_id" value="<?= $sub['id'] ?? 0 ?>">
                                        <button type="submit" class="btn btn-sm btn-danger" style="padding:0.25rem 0.5rem;font-size:0.75rem">✕</button>
                                    </form>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php else: ?>
                <p style="color:var(--admin-text-muted);font-size:0.875rem;text-align:center;padding:2rem">لا توجد طلبات حتى الآن</p>
                <?php endif; ?>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <script>
    function hslFromHex(hex) {
        hex = hex.replace('#', '');
        const r = parseInt(hex.substring(0, 2), 16) / 255;
        const g = parseInt(hex.substring(2, 4), 16) / 255;
        const b = parseInt(hex.substring(4, 6), 16) / 255;
        const max = Math.max(r, g, b), min = Math.min(r, g, b);
        let h, s, l = (max + min) / 2;
        if (max === min) { h = s = 0; }
        else {
            const d = max - min;
            s = l > 0.5 ? d / (2 - max - min) : d / (max + min);
            switch (max) {
                case r: h = ((g - b) / d + (g < b ? 6 : 0)) / 6; break;
                case g: h = ((b - r) / d + 2) / 6; break;
                case b: h = ((r - g) / d + 4) / 6; break;
            }
        }
        return { h: h * 360, s: s * 100, l: l * 100 };
    }
    </script>
</body>
</html>
