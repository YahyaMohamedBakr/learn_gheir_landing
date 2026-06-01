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
            <a href="?tab=smtp" class="<?= $activeTab === 'smtp' ? 'active' : '' ?>">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="20" height="16" x="2" y="4" rx="2"/><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/></svg>
                <span>إعدادات SMTP</span>
            </a>
        </div>
        <div class="admin-main">
            <div class="section-header">
                <h2>
                    <?php if ($activeTab === 'colors'): ?>تخصيص الألوان
                    <?php elseif ($activeTab === 'content'): ?>إدارة المحتوى
                    <?php elseif ($activeTab === 'smtp'): ?>إعدادات البريد الإلكتروني (SMTP)
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
                    <?php foreach ($settings['colors'] as $key => $color): 
                        $hex = hslToHex($color['h'], $color['s'], $color['l']);
                    ?>
                    <div class="color-item">
                        <div class="color-preview" style="background:hsl(<?= $color['h'] ?>,<?= $color['s'] ?>%,<?= $color['l'] ?>%)"></div>
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
                        <input type="text" name="hero_badge" value="<?= htmlspecialchars($settings['content']['hero_badge']) ?>">
                    </div>
                    <div class="form-group">
                        <label>العنوان الرئيسي</label>
                        <input type="text" name="hero_title" value="<?= htmlspecialchars($settings['content']['hero_title']) ?>">
                    </div>
                    <div class="form-group">
                        <label>النص الفرعي</label>
                        <textarea name="hero_subtitle" rows="3"><?= htmlspecialchars($settings['content']['hero_subtitle']) ?></textarea>
                    </div>
                    <div class="form-row" style="display:grid;grid-template-columns:1fr 1fr;gap:1rem">
                        <div class="form-group">
                            <label>نص زر CTA الأساسي</label>
                            <input type="text" name="hero_cta_primary" value="<?= htmlspecialchars($settings['content']['hero_cta_primary']) ?>">
                        </div>
                        <div class="form-group">
                            <label>نص زر CTA الثانوي</label>
                            <input type="text" name="hero_cta_secondary" value="<?= htmlspecialchars($settings['content']['hero_cta_secondary']) ?>">
                        </div>
                    </div>
                </div>

                <div class="card">
                    <h3 style="margin-bottom:1rem;font-size:1.125rem">قسم المشكلة</h3>
                    <div class="form-group">
                        <label>العنوان</label>
                        <input type="text" name="problem_title" value="<?= htmlspecialchars($settings['content']['problem_title']) ?>">
                    </div>
                    <div class="form-group">
                        <label>النص الفرعي</label>
                        <textarea name="problem_subtitle" rows="2"><?= htmlspecialchars($settings['content']['problem_subtitle']) ?></textarea>
                    </div>
                    <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:1rem">
                        <div>
                            <div class="form-group">
                                <label>العنوان 1</label>
                                <input type="text" name="problem_1_title" value="<?= htmlspecialchars($settings['content']['problem_1_title']) ?>">
                            </div>
                            <div class="form-group">
                                <label>الوصف 1</label>
                                <textarea name="problem_1_desc" rows="3"><?= htmlspecialchars($settings['content']['problem_1_desc']) ?></textarea>
                            </div>
                        </div>
                        <div>
                            <div class="form-group">
                                <label>العنوان 2</label>
                                <input type="text" name="problem_2_title" value="<?= htmlspecialchars($settings['content']['problem_2_title']) ?>">
                            </div>
                            <div class="form-group">
                                <label>الوصف 2</label>
                                <textarea name="problem_2_desc" rows="3"><?= htmlspecialchars($settings['content']['problem_2_desc']) ?></textarea>
                            </div>
                        </div>
                        <div>
                            <div class="form-group">
                                <label>العنوان 3</label>
                                <input type="text" name="problem_3_title" value="<?= htmlspecialchars($settings['content']['problem_3_title']) ?>">
                            </div>
                            <div class="form-group">
                                <label>الوصف 3</label>
                                <textarea name="problem_3_desc" rows="3"><?= htmlspecialchars($settings['content']['problem_3_desc']) ?></textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <h3 style="margin-bottom:1rem;font-size:1.125rem">قسم الحل</h3>
                    <div class="form-group">
                        <label>العنوان</label>
                        <input type="text" name="solution_title" value="<?= htmlspecialchars($settings['content']['solution_title']) ?>">
                    </div>
                    <div class="form-group">
                        <label>النص الفرعي</label>
                        <textarea name="solution_subtitle" rows="3"><?= htmlspecialchars($settings['content']['solution_subtitle']) ?></textarea>
                    </div>
                </div>

                <div class="card">
                    <h3 style="margin-bottom:1rem;font-size:1.125rem">قسم المميزات</h3>
                    <div class="form-group">
                        <label>العنوان</label>
                        <input type="text" name="features_title" value="<?= htmlspecialchars($settings['content']['features_title']) ?>">
                    </div>
                    <div class="form-group">
                        <label>النص الفرعي</label>
                        <textarea name="features_subtitle" rows="2"><?= htmlspecialchars($settings['content']['features_subtitle']) ?></textarea>
                    </div>
                    <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:1rem">
                        <?php for ($i = 1; $i <= 6; $i++): ?>
                        <div>
                            <div class="form-group">
                                <label>العنوان <?= $i ?></label>
                                <input type="text" name="feature_<?= $i ?>_title" value="<?= htmlspecialchars($settings['content']['feature_' . $i . '_title']) ?>">
                            </div>
                            <div class="form-group">
                                <label>الوصف <?= $i ?></label>
                                <textarea name="feature_<?= $i ?>_desc" rows="3"><?= htmlspecialchars($settings['content']['feature_' . $i . '_desc']) ?></textarea>
                            </div>
                        </div>
                        <?php endfor; ?>
                    </div>
                </div>

                <div class="card">
                    <h3 style="margin-bottom:1rem;font-size:1.125rem">قسم رؤية 2030</h3>
                    <div class="form-group">
                        <label>العنوان</label>
                        <input type="text" name="vision_title" value="<?= htmlspecialchars($settings['content']['vision_title']) ?>">
                    </div>
                    <div class="form-group">
                        <label>النص الفرعي</label>
                        <textarea name="vision_subtitle" rows="2"><?= htmlspecialchars($settings['content']['vision_subtitle']) ?></textarea>
                    </div>
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem">
                        <?php for ($i = 1; $i <= 4; $i++): ?>
                        <div>
                            <div class="form-group">
                                <label>العنوان <?= $i ?></label>
                                <input type="text" name="vision_<?= $i ?>_title" value="<?= htmlspecialchars($settings['content']['vision_' . $i . '_title']) ?>">
                            </div>
                            <div class="form-group">
                                <label>الوصف <?= $i ?></label>
                                <textarea name="vision_<?= $i ?>_desc" rows="2"><?= htmlspecialchars($settings['content']['vision_' . $i . '_desc']) ?></textarea>
                            </div>
                        </div>
                        <?php endfor; ?>
                    </div>
                </div>

                <div class="card">
                    <h3 style="margin-bottom:1rem;font-size:1.125rem">قسم الفريق</h3>
                    <div class="form-group">
                        <label>العنوان</label>
                        <input type="text" name="team_title" value="<?= htmlspecialchars($settings['content']['team_title']) ?>">
                    </div>
                    <div class="form-group">
                        <label>النص الفرعي</label>
                        <textarea name="team_subtitle" rows="2"><?= htmlspecialchars($settings['content']['team_subtitle']) ?></textarea>
                    </div>
                </div>

                <div class="card">
                    <h3 style="margin-bottom:1rem;font-size:1.125rem">قسم الفورم (CTA)</h3>
                    <div class="form-group">
                        <label>عنوان الفورم</label>
                        <input type="text" name="form_title" value="<?= htmlspecialchars($settings['content']['form_title']) ?>">
                    </div>
                    <div class="form-group">
                        <label>نص الفورم الفرعي</label>
                        <textarea name="form_subtitle" rows="2"><?= htmlspecialchars($settings['content']['form_subtitle']) ?></textarea>
                    </div>
                </div>

                <div class="card">
                    <h3 style="margin-bottom:1rem;font-size:1.125rem">قسم CTA السفلي</h3>
                    <div class="form-group">
                        <label>العنوان</label>
                        <input type="text" name="cta_title" value="<?= htmlspecialchars($settings['content']['cta_title']) ?>">
                    </div>
                    <div class="form-group">
                        <label>النص الفرعي</label>
                        <textarea name="cta_subtitle" rows="2"><?= htmlspecialchars($settings['content']['cta_subtitle']) ?></textarea>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary">حفظ المحتوى</button>
            </form>
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
