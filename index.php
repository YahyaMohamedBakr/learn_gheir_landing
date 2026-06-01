<?php
$settingsFile = __DIR__ . '/data/settings.json';
$settings = json_decode(file_get_contents($settingsFile), true);
$c = $settings['colors'];
$content = $settings['content'];
$site = $settings['site'] ?? [];
$menuItems = $settings['menu']['items'] ?? [];
$sections = $settings['sections'] ?? [];
$team = $settings['team'] ?? [];
$social = $settings['social'] ?? [];

function sectionEnabled($key) {
    global $sections;
    return !isset($sections[$key]) || $sections[$key]['enabled'];
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title><?= htmlspecialchars($site['title'] ?? 'Learn.Gheir') ?> - <?= htmlspecialchars($content['hero_title']) ?></title>
  <meta name="description" content="<?= htmlspecialchars($site['description'] ?? '') ?>" />
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@300;400;500;700;800&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="style.css" />
  <style>
    :root {
      --background: <?= $c['background']['h'] ?> <?= $c['background']['s'] ?>% <?= $c['background']['l'] ?>%;
      --foreground: <?= $c['foreground']['h'] ?> <?= $c['foreground']['s'] ?>% <?= $c['foreground']['l'] ?>%;
      --primary: <?= $c['primary']['h'] ?> <?= $c['primary']['s'] ?>% <?= $c['primary']['l'] ?>%;
      --primary-foreground: <?= $c['primary-foreground']['h'] ?> <?= $c['primary-foreground']['s'] ?>% <?= $c['primary-foreground']['l'] ?>%;
      --secondary: <?= $c['secondary']['h'] ?> <?= $c['secondary']['s'] ?>% <?= $c['secondary']['l'] ?>%;
      --secondary-foreground: <?= $c['secondary-foreground']['h'] ?> <?= $c['secondary-foreground']['s'] ?>% <?= $c['secondary-foreground']['l'] ?>%;
      --accent: <?= $c['accent']['h'] ?> <?= $c['accent']['s'] ?>% <?= $c['accent']['l'] ?>%;
      --accent-foreground: <?= $c['accent-foreground']['h'] ?> <?= $c['accent-foreground']['s'] ?>% <?= $c['accent-foreground']['l'] ?>%;
      --muted: <?= $c['muted']['h'] ?> <?= $c['muted']['s'] ?>% <?= $c['muted']['l'] ?>%;
      --muted-foreground: <?= $c['muted-foreground']['h'] ?> <?= $c['muted-foreground']['s'] ?>% <?= $c['muted-foreground']['l'] ?>%;
      --card: <?= $c['card']['h'] ?> <?= $c['card']['s'] ?>% <?= $c['card']['l'] ?>%;
      --card-foreground: <?= $c['card-foreground']['h'] ?> <?= $c['card-foreground']['s'] ?>% <?= $c['card-foreground']['l'] ?>%;
      --border: <?= $c['border']['h'] ?> <?= $c['border']['s'] ?>% <?= $c['border']['l'] ?>%;
      --ring: <?= $c['ring']['h'] ?> <?= $c['ring']['s'] ?>% <?= $c['ring']['l'] ?>%;
      --destructive: <?= $c['destructive']['h'] ?> <?= $c['destructive']['s'] ?>% <?= $c['destructive']['l'] ?>%;
      --destructive-foreground: <?= $c['destructive-foreground']['h'] ?> <?= $c['destructive-foreground']['s'] ?>% <?= $c['destructive-foreground']['l'] ?>%;
    }
  </style>
</head>
<body>
  <svg xmlns="http://www.w3.org/2000/svg" style="display:none">
    <symbol id="icon-graduation-cap" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 10v6M2 10l10-5 10 5-10 5z"/><path d="M6 12v5c3 3 9 3 12 0v-5"/></symbol>
    <symbol id="icon-menu" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="4" x2="20" y1="12" y2="12"/><line x1="4" x2="20" y1="6" y2="6"/><line x1="4" x2="20" y1="18" y2="18"/></symbol>
    <symbol id="icon-x" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></symbol>
    <symbol id="icon-alert-circle" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" x2="12" y1="8" y2="12"/><line x1="12" x2="12.01" y1="16" y2="16"/></symbol>
    <symbol id="icon-user-x" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><line x1="17" x2="22" y1="8" y2="13"/><line x1="22" x2="17" y1="8" y2="13"/></symbol>
    <symbol id="icon-trending-down" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="22 17 18.5 14.5 15 18 8 11 2 17"/><polyline points="16 17 22 17 22 11"/></symbol>
    <symbol id="icon-target" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><circle cx="12" cy="12" r="6"/><circle cx="12" cy="12" r="2"/></symbol>
    <symbol id="icon-sparkles" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3a6 6 0 0 0 9 9 9 9 0 1 1-9-9Z"/><path d="M20 3v4"/><path d="M22 5h-4"/></symbol>
    <symbol id="icon-book-open" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/></symbol>
    <symbol id="icon-users" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></symbol>
    <symbol id="icon-bar-chart-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 3v18h18"/><path d="M18 17V9"/><path d="M13 17V5"/><path d="M8 17v-3"/></symbol>
    <symbol id="icon-trophy" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 9H4.5a2.5 2.5 0 0 1 0-5H6"/><path d="M18 9h1.5a2.5 2.5 0 0 0 0-5H18"/><path d="M4 22h16"/><path d="M10 14.66V17c0 .55-.47.98-.97 1.21C7.85 18.75 7 20.24 7 22"/><path d="M14 14.66V17c0 .55.47.98.97 1.21C16.15 18.75 17 20.24 17 22"/><path d="M18 2H6v7a6 6 0 0 0 12 0V2Z"/></symbol>
    <symbol id="icon-brain-circuit" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 4.5a2.5 2.5 0 0 0-4.96-.46 2.5 2.5 0 0 0-1.98 3 2.5 2.5 0 0 0-1.32 4.24 3 3 0 0 0 .34 5.58 2.5 2.5 0 0 0 2.96 3.08A2.5 2.5 0 0 0 12 19.5a2.5 2.5 0 0 0 4.96.44 2.5 2.5 0 0 0 2.96-3.08 3 3 0 0 0 .34-5.58 2.5 2.5 0 0 0-1.32-4.24 2.5 2.5 0 0 0-1.98-3A2.5 2.5 0 0 0 12 4.5"/><path d="M9.5 9.5 12 7l2.5 2.5"/><path d="M9.5 14.5 12 17l2.5-2.5"/><line x1="12" x2="12" y1="11" y2="13"/></symbol>
    <symbol id="icon-activity" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></symbol>
    <symbol id="icon-route" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="6" cy="19" r="3"/><path d="M9 19h8.5a3.5 3.5 0 0 0 0-7h-11a3.5 3.5 0 0 1 0-7H15"/><circle cx="18" cy="5" r="3"/></symbol>
    <symbol id="icon-refresh-cw" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 12a9 9 0 0 1 9-9 9.75 9.75 0 0 1 6.74 2.74L21 8"/><path d="M21 3v5h-5"/><path d="M21 12a9 9 0 0 1-9 9 9.75 9.75 0 0 1-6.74-2.74L3 16"/><path d="M3 21v-5h5"/></symbol>
    <symbol id="icon-clipboard-check" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="8" height="4" x="8" y="2" rx="1"/><path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"/><path d="m9 14 2 2 4-4"/></symbol>
    <symbol id="icon-user-round" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="8" r="5"/><path d="M20 21a8 8 0 0 0-16 0"/></symbol>
    <symbol id="icon-check-circle-2" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22c5.523 0 10-4.477 10-10S17.523 2 12 2 2 6.477 2 12s4.477 10 10 10z"/><path d="m9 12 2 2 4-4"/></symbol>
    <symbol id="icon-arrow-left" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m12 19-7-7 7-7"/><path d="M19 12H5"/></symbol>
    <symbol id="icon-mail" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="20" height="16" x="2" y="4" rx="2"/><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/></symbol>
    <symbol id="icon-linkedin" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 8a6 6 0 0 1 6 6v7h-4v-7a2 2 0 0 0-2-2 2 2 0 0 0-2 2v7h-4v-7a6 6 0 0 1 6-6z"/><rect width="4" height="12" x="2" y="9"/><circle cx="4" cy="4" r="2"/></symbol>
    <symbol id="icon-twitter" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 4s-.7 2.1-2 3.4c1.6 10-9.4 17.3-18 11.6 2.2.1 4.4-.6 6-2C3 15.5.5 9.6 3 5c2.2 2.6 5.6 4.1 9 4-.9-4.2 4-6.6 7-3.8 1.1 0 3-1.2 3-1.2z"/></symbol>
    <symbol id="icon-instagram" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="20" height="20" x="2" y="2" rx="5" ry="5"/><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"/><line x1="17.5" x2="17.51" y1="6.5" y2="6.5"/></symbol>
    <symbol id="icon-gamepad-2" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="6" x2="10" y1="11" y2="11"/><line x1="8" x2="8" y1="9" y2="13"/><line x1="15" x2="15.01" y1="12" y2="12"/><line x1="18" x2="18.01" y1="10" y2="10"/><path d="M17.32 5H6.68a4 4 0 0 0-3.978 3.59c-.006.052-.01.101-.017.152C2.604 9.416 2 14.456 2 16a3 3 0 0 0 3 3c1 0 1.5-.5 2-1l1.414-1.414A2 2 0 0 1 9.828 16h4.344a2 2 0 0 1 1.414.586L17 18c.5.5 1 1 2 1a3 3 0 0 0 3-3c0-1.545-.604-6.584-.685-7.258-.007-.05-.011-.1-.017-.151A4 4 0 0 0 17.32 5z"/></symbol>
    <symbol id="icon-code" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="16 18 22 12 16 6"/><polyline points="8 6 2 12 8 18"/></symbol>
    <symbol id="icon-zap" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/></symbol>
    <symbol id="icon-list-checks" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m3 17 2 2 4-4"/><path d="m3 7 2 2 4-4"/><path d="M13 6h8"/><path d="M13 12h8"/><path d="M13 18h8"/></symbol>
    <symbol id="icon-lightbulb" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M15 14c.2-1 .7-1.7 1.5-2.5 1-.9 1.5-2.2 1.5-3.5A6 6 0 0 0 6 8c0 1 .2 2.2 1.5 3.5C7.7 12.8 8 14.5 8 16"/><path d="M9 18h6"/><path d="M10 22h4"/></symbol>
  </svg>

  <div id="toastContainer" class="toast-container"></div>

  <?php
  $logoType = $site['logo_type'] ?? 'text';
  $logoText = $site['logo_text'] ?? 'Learn.Gheir';
  $logoImage = $site['logo_image'] ?? '';
  $logoWidth = $site['logo_width'] ?? 40;
  $logoHeight = $site['logo_height'] ?? 40;
  $menuPos = $site['menu_position'] ?? 'center';
  ?>
  <header id="header" class="header">
    <div class="section-container" style="width:100%">
      <div class="header-inner">
        <div class="header-section header-right">
          <a href="#home" class="logo">
            <?php if ($logoType === 'image' && !empty($logoImage)): ?>
            <img src="<?= htmlspecialchars($logoImage) ?>" alt="<?= htmlspecialchars($logoText) ?>" style="width:<?= (int)$logoWidth ?>px;height:<?= (int)$logoHeight ?>px;object-fit:contain">
            <?php else: ?>
            <svg class="logo-icon"><use href="#icon-graduation-cap"/></svg>
            <span class="logo-text gradient-text"><?= htmlspecialchars($logoText) ?></span>
            <?php endif; ?>
          </a>
        </div>
        <nav class="nav-items" id="navItems" data-position="<?= htmlspecialchars($menuPos) ?>">
          <?php foreach ($menuItems as $item): ?>
            <a href="<?= htmlspecialchars($item['href']) ?>" class="nav-link mobile-nav-link"><?= htmlspecialchars($item['label']) ?></a>
          <?php endforeach; ?>
          <a href="#early-access" class="nav-cta mobile-nav-link"><?= htmlspecialchars($content['hero_cta_primary']) ?></a>
        </nav>
        <div class="header-section header-left">
          <button id="mobileMenuBtn" class="mobile-menu-btn" aria-label="Toggle menu">
            <svg width="24" height="24" viewBox="0 0 24 24"><use href="#icon-menu"/></svg>
          </button>
        </div>
      </div>
    </div>
  </header>

  <div class="flex flex-col min-h-screen">
    <main class="flex-1">

      <!-- Hero -->
      <?php if (sectionEnabled('hero')): ?>
      <section id="home" class="hero">
        <div class="hero-bg"><div class="hero-gradient"></div><div class="hero-grid"></div></div>
        <div class="section-container hero-content">
          <div class="hero-text" data-anim="fade-up">
            <span class="hero-badge"><?= htmlspecialchars($content['hero_badge']) ?></span>
            <h1 class="hero-title"><?= htmlspecialchars($content['hero_title']) ?></h1>
            <p class="hero-subtitle"><?= htmlspecialchars($content['hero_subtitle']) ?></p>
            <div class="hero-actions">
              <a href="#early-access" class="btn-primary"><?= htmlspecialchars($content['hero_cta_primary']) ?></a>
              <a href="#solution" class="btn-outline"><?= htmlspecialchars($content['hero_cta_secondary']) ?></a>
            </div>
          </div>
          <div class="hero-mockup-wrapper" data-anim="fade-scale" data-delay="200">
            <div class="mockup-glow"></div>
            <div class="mockup-card">
              <div class="mockup-dots">
                <span class="mockup-dot red"></span>
                <span class="mockup-dot yellow"></span>
                <span class="mockup-dot green"></span>
                <span style="margin-right:auto"><span class="mockup-badge">Learn.Gheir POC</span></span>
              </div>
              <div class="mockup-inner">
                <div class="mockup-header">
                  <div><p class="mockup-label">ملف المتعلم</p><h3 class="mockup-title">رحلة تعلم مخصصة</h3></div>
                  <div class="mockup-icon-box"><svg width="24" height="24"><use href="#icon-user-round"/></svg></div>
                </div>
                <div class="mockup-signals">
                  <div class="mockup-signal"><div class="mockup-signal-header"><span class="mockup-signal-label">مستوى المعرفة</span><span class="mockup-signal-value">متوسط</span></div><div class="mockup-progress"><div class="mockup-progress-bar w-62" data-progress></div></div></div>
                  <div class="mockup-signal"><div class="mockup-signal-header"><span class="mockup-signal-label">نمط التفاعل</span><span class="mockup-signal-value">عملي</span></div><div class="mockup-progress"><div class="mockup-progress-bar w-78" data-progress></div></div></div>
                  <div class="mockup-signal"><div class="mockup-signal-header"><span class="mockup-signal-label">سرعة التعلم</span><span class="mockup-signal-value">سريعة</span></div><div class="mockup-progress"><div class="mockup-progress-bar w-70" data-progress></div></div></div>
                </div>
                <div class="mockup-grid">
                  <div class="mockup-grid-item blue"><svg><use href="#icon-brain-circuit"/></svg>تحليل</div>
                  <div class="mockup-grid-item violet"><svg><use href="#icon-sparkles"/></svg>توصية</div>
                  <div class="mockup-grid-item emerald"><svg><use href="#icon-route"/></svg>مسار</div>
                </div>
                <div class="mockup-recommendation">
                  <div class="mockup-rec-header"><svg width="20" height="20"><use href="#icon-check-circle-2"/></svg>التوصية الحالية</div>
                  <p class="mockup-rec-text">ابدأ بمسار عملي سريع: تحديات قصيرة + تطبيق مباشر + اختبار فوري بعد كل خطوة.</p>
                </div>
              </div>
              <div class="mockup-footer">
                <span class="mockup-footer-label"><svg width="16" height="16" color="hsl(var(--primary))"><use href="#icon-bar-chart-3"/></svg>مؤشرات التعلم</span>
                <span class="mockup-footer-value">+38% تفاعل متوقع</span>
              </div>
            </div>
          </div>
        </div>
      </section>
      <?php endif; ?>

      <!-- Problem -->
      <?php if (sectionEnabled('problem')): ?>
      <section class="py-20" style="background:hsl(var(--background))">
        <div class="section-container">
          <div class="text-center mb-16" data-anim="fade-up">
            <h2 class="section-title"><?= htmlspecialchars($content['problem_title']) ?></h2>
            <p class="section-subtitle"><?= htmlspecialchars($content['problem_subtitle']) ?></p>
          </div>
          <div class="grid-1 grid-md-2 grid-lg-3" style="gap:1.5rem">
            <div class="problem-card" data-anim="fade-up">
              <div class="problem-icon"><svg width="28" height="28"><use href="#icon-alert-circle"/></svg></div>
              <h3 class="card-title"><?= htmlspecialchars($content['problem_1_title']) ?></h3>
              <p class="card-desc"><?= htmlspecialchars($content['problem_1_desc']) ?></p>
            </div>
            <div class="problem-card" data-anim="fade-up" data-delay="100">
              <div class="problem-icon"><svg width="28" height="28"><use href="#icon-user-x"/></svg></div>
              <h3 class="card-title"><?= htmlspecialchars($content['problem_2_title']) ?></h3>
              <p class="card-desc"><?= htmlspecialchars($content['problem_2_desc']) ?></p>
            </div>
            <div class="problem-card" data-anim="fade-up" data-delay="200">
              <div class="problem-icon"><svg width="28" height="28"><use href="#icon-trending-down"/></svg></div>
              <h3 class="card-title"><?= htmlspecialchars($content['problem_3_title']) ?></h3>
              <p class="card-desc"><?= htmlspecialchars($content['problem_3_desc']) ?></p>
            </div>
          </div>
        </div>
      </section>
      <?php endif; ?>

      <!-- Solution -->
      <?php if (sectionEnabled('solution')): ?>
      <section id="solution" class="py-20 solution-section">
        <div class="section-container">
          <div class="text-center mb-12" data-anim="fade-up">
            <h2 class="section-title"><?= htmlspecialchars($content['solution_title']) ?></h2>
            <p class="section-subtitle" style="max-width:56rem"><?= htmlspecialchars($content['solution_subtitle']) ?></p>
          </div>
          <div class="flow-diagram">
            <div class="flow-step" data-anim="fade-scale"><div class="flow-icon-box bg-blue"><svg><use href="#icon-clipboard-check"/></svg></div><p class="flow-label">التقييم الأولي</p></div>
            <div class="flow-arrow"><svg><use href="#icon-arrow-left"/></svg></div><div class="flow-arrow-mobile"><svg><use href="#icon-arrow-left"/></svg></div>
            <div class="flow-step" data-anim="fade-scale" data-delay="100"><div class="flow-icon-box bg-indigo"><svg><use href="#icon-user-round"/></svg></div><p class="flow-label">ملف المتعلم</p></div>
            <div class="flow-arrow"><svg><use href="#icon-arrow-left"/></svg></div><div class="flow-arrow-mobile"><svg><use href="#icon-arrow-left"/></svg></div>
            <div class="flow-step" data-anim="fade-scale" data-delay="200"><div class="flow-icon-box bg-purple"><svg><use href="#icon-brain-circuit"/></svg></div><p class="flow-label">محرك الذكاء الاصطناعي</p></div>
            <div class="flow-arrow"><svg><use href="#icon-arrow-left"/></svg></div><div class="flow-arrow-mobile"><svg><use href="#icon-arrow-left"/></svg></div>
            <div class="flow-step" data-anim="fade-scale" data-delay="300"><div class="flow-icon-box bg-primary"><svg><use href="#icon-route"/></svg></div><p class="flow-label">مسار تعلم مخصص</p></div>
          </div>
          <div class="result-card" data-anim="fade-up" data-delay="300">
            <h3 class="result-title">النتيجة</h3>
            <p class="result-desc">الهدف في نسخة الـPOC ليس بناء منصة ضخمة، بل إثبات أن نفس الهدف التعليمي يمكن تقديمه عبر مسارات مختلفة بناءً على بيانات المتعلم.</p>
          </div>
        </div>
      </section>
      <?php endif; ?>

      <!-- Learning Paths -->
      <?php if (sectionEnabled('learning_paths')): ?>
      <section class="learning-paths-section">
        <div class="particles" id="particles"></div>
        <div class="section-container" style="position:relative;z-index:10">
          <div class="text-center" style="max-width:48rem;margin:0 auto 4rem">
            <h2 class="section-title gradient-text-ai mb-6" data-anim="fade-up">رحلات تعلم مخصصة لكل متعلم</h2>
            <p class="section-subtitle" data-anim="fade-up" data-delay="100">نفس الهدف التعليمي… لكن كل متعلم يحصل على تجربة تناسب طريقته في الفهم والتفاعل.</p>
          </div>
          <div class="goal-banner" data-anim="fade-scale">
            <div class="glass-panel goal-badge"><svg><use href="#icon-target"/></svg><span>الهدف التعليمي: تعلم أساسيات البرمجة</span></div>
          </div>
          <div class="split-layout">
            <div class="split-side">
              <div class="learner-card interactive glass-panel" data-anim="fade-up" data-delay="200">
                <div class="learner-card-topbar" style="background:var(--gradient-interactive)"></div>
                <div class="learner-card-blur" style="background:var(--gradient-interactive)"></div>
                <div class="learner-card-content">
                  <div class="learner-card-header">
                    <div><p class="learner-type-label">نوع المتعلم</p><h3 class="learner-name"><span class="learner-avatar" style="background:var(--gradient-interactive)"><svg><use href="#icon-user-round"/></svg></span>متعلم تفاعلي</h3></div>
                    <span class="learner-badge">مسار مختلف</span>
                  </div>
                  <div class="learner-tags"><span class="learner-tag">يحب التجربة</span><span class="learner-tag">سريع الملل</span><span class="learner-tag">يتعلم بالممارسة</span></div>
                  <div class="learner-experience">
                    <div class="learner-exp-header"><svg><use href="#icon-check-circle-2"/></svg>التجربة المقترحة</div>
                    <div class="learner-exp-items">
                      <div class="learner-exp-item" data-anim="fade-left" data-delay="400"><div class="learner-exp-icon" style="background:var(--gradient-interactive)"><svg><use href="#icon-gamepad-2"/></svg></div><div class="learner-exp-content"><div class="learner-exp-progress"><div class="learner-exp-bar" data-progress style="background:var(--gradient-interactive);width:75%"></div></div><span class="learner-exp-label">تحدي برمجي: بناء آلة حاسبة</span></div></div>
                      <div class="learner-exp-item" data-anim="fade-left" data-delay="500"><div class="learner-exp-icon" style="background:var(--gradient-interactive)"><svg><use href="#icon-trophy"/></svg></div><div class="learner-exp-content"><div class="learner-exp-progress"><div class="learner-exp-bar" data-progress style="background:var(--gradient-interactive);width:100%"></div></div><span class="learner-exp-label">مكافأة إتمام المهمة</span></div></div>
                      <div class="learner-exp-item" data-anim="fade-left" data-delay="600"><div class="learner-exp-icon" style="background:var(--gradient-interactive)"><svg><use href="#icon-zap"/></svg></div><div class="learner-exp-content"><div class="learner-exp-progress"><div class="learner-exp-bar" data-progress style="background:var(--gradient-interactive);width:60%"></div></div><span class="learner-exp-label">تطبيق عملي مباشر</span></div></div>
                    </div>
                  </div>
                  <div class="learner-labels"><span class="learner-label">تحديات يومية</span><span class="learner-label">نقاط ومكافآت</span><span class="learner-label">مهام تفاعلية</span><span class="learner-label">تطبيق عملي مباشر</span></div>
                  <div class="learner-outcome"><p class="learner-outcome-label">مخرج المسار</p><p class="learner-outcome-text">يفهم المفاهيم عبر تحديات قصيرة ونتائج فورية تحافظ على انتباهه.</p></div>
                </div>
              </div>
            </div>
            <div class="split-center">
              <div class="flex flex-col lg-flex-row items-center justify-center relative">
                <div class="hidden lg:block arrow-connector-right"><div class="animated-arrow"><svg viewBox="0 0 100 20" preserveAspectRatio="none"><path d="M 0 10 L 90 10" class="arrow-dashed"/><path d="M 0 10 L 90 10" class="arrow-solid"/><path d="M 80 5 L 90 10 L 80 15" class="arrow-head"/></svg></div></div>
                <div class="ai-engine-wrapper" data-anim="fade-scale" data-delay="300">
                  <div class="ai-engine-outer"><div class="ai-engine-glow"></div><div class="ai-engine-ring"></div><div class="ai-engine-core"><svg><use href="#icon-brain-circuit"/></svg><span>محرك التخصيص<br/>الذكي</span></div></div>
                  <div class="ai-engine-labels">
                    <div class="ai-engine-label accent"><svg><use href="#icon-activity"/></svg>تحليل سلوك المتعلم</div>
                    <div class="ai-engine-connector"></div>
                    <div class="ai-engine-label primary"><svg><use href="#icon-sparkles"/></svg>تخصيص المسار المناسب</div>
                  </div>
                </div>
                <div class="hidden lg:block arrow-connector-left"><div class="animated-arrow"><svg viewBox="0 0 100 20" preserveAspectRatio="none"><path d="M 100 10 L 10 10" class="arrow-dashed"/><path d="M 100 10 L 10 10" class="arrow-solid"/><path d="M 20 5 L 10 10 L 20 15" class="arrow-head"/></svg></div></div>
              </div>
            </div>
            <div class="split-side">
              <div class="learner-card structured glass-panel" data-anim="fade-up" data-delay="400">
                <div class="learner-card-topbar" style="background:var(--gradient-structured)"></div>
                <div class="learner-card-blur" style="background:var(--gradient-structured)"></div>
                <div class="learner-card-content">
                  <div class="learner-card-header">
                    <div><p class="learner-type-label">نوع المتعلم</p><h3 class="learner-name"><span class="learner-avatar" style="background:var(--gradient-structured)"><svg><use href="#icon-user-round"/></svg></span>متعلم منهجي</h3></div>
                    <span class="learner-badge">مسار مختلف</span>
                  </div>
                  <div class="learner-tags"><span class="learner-tag">يفضل التدرج</span><span class="learner-tag">يحتاج وضوحاً</span><span class="learner-tag">يهتم بالفهم العميق</span></div>
                  <div class="learner-experience">
                    <div class="learner-exp-header"><svg><use href="#icon-check-circle-2"/></svg>التجربة المقترحة</div>
                    <div class="learner-exp-items">
                      <div class="learner-exp-item" data-anim="fade-right" data-delay="600"><div class="learner-exp-icon" style="background:var(--gradient-structured)"><svg><use href="#icon-book-open"/></svg></div><div class="learner-exp-content"><div class="learner-exp-progress"><div class="learner-exp-bar" data-progress style="background:var(--gradient-structured);width:100%"></div></div><span class="learner-exp-label">الوحدة الأولى: المفاهيم الأساسية</span></div></div>
                      <div class="learner-exp-item" data-anim="fade-right" data-delay="700"><div class="learner-exp-icon" style="background:var(--gradient-structured)"><svg><use href="#icon-list-checks"/></svg></div><div class="learner-exp-content"><div class="learner-exp-progress"><div class="learner-exp-bar" data-progress style="background:var(--gradient-structured);width:80%"></div></div><span class="learner-exp-label">تمرين موجه: المتغيرات</span></div></div>
                      <div class="learner-exp-item" data-anim="fade-right" data-delay="800"><div class="learner-exp-icon" style="background:var(--gradient-structured)"><svg><use href="#icon-lightbulb"/></svg></div><div class="learner-exp-content"><div class="learner-exp-progress"><div class="learner-exp-bar" data-progress style="background:var(--gradient-structured);width:40%"></div></div><span class="learner-exp-label">مراجعة ذكية للمفاهيم</span></div></div>
                    </div>
                  </div>
                  <div class="learner-labels"><span class="learner-label">شرح تدريجي</span><span class="learner-label">تمارين منظمة</span><span class="learner-label">مراجعات ذكية</span><span class="learner-label">تغذية راجعة تفصيلية</span></div>
                  <div class="learner-outcome"><p class="learner-outcome-label">مخرج المسار</p><p class="learner-outcome-text">يبني الفهم عبر شرح تدريجي وتمارين منظمة ونقاط مراجعة واضحة.</p></div>
                </div>
              </div>
            </div>
          </div>
          <div class="conclusion-panel" data-anim="fade-up" data-delay="600">
            <div class="glass-panel conclusion-inner"><div class="conclusion-bg"></div><svg class="conclusion-icon"><use href="#icon-check-circle-2"/></svg><p class="conclusion-text">لأن كل متعلم يتعلم بطريقة مختلفة، يقوم Learn.Gheir ببناء رحلة تعلم تتكيف مع احتياجاته وسرعته وأسلوبه.</p></div>
          </div>
        </div>
      </section>
      <?php endif; ?>

      <!-- Decision Logic -->
      <?php if (sectionEnabled('decision_logic')): ?>
      <section class="py-20" style="background:hsl(var(--background))">
        <div class="section-container">
          <div class="text-center mb-14" style="max-width:56rem;margin-inline:auto" data-anim="fade-up">
            <span class="hero-badge" style="border-color:hsl(var(--primary)/0.2);background:hsl(var(--primary)/0.1);color:hsl(var(--primary));margin-bottom:1rem;display:inline-flex">طبقة إثبات التخصيص</span>
            <h2 class="section-title mb-5">كيف يتخذ النظام قراره؟</h2>
            <p class="section-subtitle">لا نعرض مجرد محتوى تعليمي. نحن نختبر منطق توجيه المتعلم إلى المسار الأنسب بناءً على بيانات واضحة وسلوك قابل للقياس.</p>
          </div>
          <div class="grid-5">
            <div class="step-card" data-anim="fade-up"><div class="step-icon-box"><svg><use href="#icon-clipboard-check"/></svg></div><h3 class="step-title">تقييم تشخيصي</h3><p class="step-desc">أسئلة قصيرة تقيس المستوى الحالي والهدف التعليمي.</p><span class="card-number">01</span></div>
            <div class="step-card" data-anim="fade-up" data-delay="80"><div class="step-icon-box"><svg><use href="#icon-activity"/></svg></div><h3 class="step-title">إشارات سلوكية</h3><p class="step-desc">تحليل السرعة، التفاعل، الإكمال، ونقاط التعثر.</p><span class="card-number">02</span></div>
            <div class="step-card" data-anim="fade-up" data-delay="160"><div class="step-icon-box"><svg><use href="#icon-brain-circuit"/></svg></div><h3 class="step-title">منطق التوصية</h3><p class="step-desc">قواعد ذكية أولية قابلة للتطوير لاحقًا إلى نماذج AI أعمق.</p><span class="card-number">03</span></div>
            <div class="step-card" data-anim="fade-up" data-delay="240"><div class="step-icon-box"><svg><use href="#icon-route"/></svg></div><h3 class="step-title">اختيار المسار</h3><p class="step-desc">توجيه المتعلم إلى تجربة عملية أو منهجية حسب بياناته.</p><span class="card-number">04</span></div>
            <div class="step-card" data-anim="fade-up" data-delay="320"><div class="step-icon-box"><svg><use href="#icon-refresh-cw"/></svg></div><h3 class="step-title">تحسين مستمر</h3><p class="step-desc">تعديل الرحلة حسب الأداء والتفاعل داخل التجربة.</p><span class="card-number">05</span></div>
          </div>
          <div class="decision-box" data-anim="fade-up" data-delay="300"><div class="decision-grid"><div><p class="decision-label">المدخلات</p><p class="decision-value">اختبار + سلوك + هدف</p></div><div><p class="decision-label">المعالجة</p><p class="decision-value">Decision Engine قابل للتطوير</p></div><div><p class="decision-label">المخرج</p><p class="decision-value">مسار تعلم مخصص وقابل للقياس</p></div></div></div>
        </div>
      </section>
      <?php endif; ?>

      <!-- Features -->
      <?php if (sectionEnabled('features')): ?>
      <section id="features" class="py-20" style="background:hsl(var(--background))">
        <div class="section-container">
          <div class="text-center mb-16" data-anim="fade-up">
            <h2 class="section-title mb-4"><?= htmlspecialchars($content['features_title']) ?></h2>
            <p class="section-subtitle"><?= htmlspecialchars($content['features_subtitle']) ?></p>
          </div>
          <div class="features-grid">
            <div class="card" data-anim="fade-up"><div class="card-icon"><svg><use href="#icon-target"/></svg></div><h3 class="card-title"><?= htmlspecialchars($content['feature_1_title']) ?></h3><p class="card-desc"><?= htmlspecialchars($content['feature_1_desc']) ?></p></div>
            <div class="card" data-anim="fade-up" data-delay="100"><div class="card-icon"><svg><use href="#icon-sparkles"/></svg></div><h3 class="card-title"><?= htmlspecialchars($content['feature_2_title']) ?></h3><p class="card-desc"><?= htmlspecialchars($content['feature_2_desc']) ?></p></div>
            <div class="card" data-anim="fade-up" data-delay="200"><div class="card-icon"><svg><use href="#icon-book-open"/></svg></div><h3 class="card-title"><?= htmlspecialchars($content['feature_3_title']) ?></h3><p class="card-desc"><?= htmlspecialchars($content['feature_3_desc']) ?></p></div>
            <div class="card" data-anim="fade-up" data-delay="300"><div class="card-icon"><svg><use href="#icon-users"/></svg></div><h3 class="card-title"><?= htmlspecialchars($content['feature_4_title']) ?></h3><p class="card-desc"><?= htmlspecialchars($content['feature_4_desc']) ?></p></div>
            <div class="card" data-anim="fade-up" data-delay="400"><div class="card-icon"><svg><use href="#icon-bar-chart-3"/></svg></div><h3 class="card-title"><?= htmlspecialchars($content['feature_5_title']) ?></h3><p class="card-desc"><?= htmlspecialchars($content['feature_5_desc']) ?></p></div>
            <div class="card" data-anim="fade-up" data-delay="500"><div class="card-icon"><svg><use href="#icon-trophy"/></svg></div><h3 class="card-title"><?= htmlspecialchars($content['feature_6_title']) ?></h3><p class="card-desc"><?= htmlspecialchars($content['feature_6_desc']) ?></p></div>
          </div>
        </div>
      </section>
      <?php endif; ?>

      <!-- Vision -->
      <?php if (sectionEnabled('vision')): ?>
      <section class="py-20 vision-section">
        <div class="section-container">
          <div class="text-center" style="max-width:56rem;margin:0 auto" data-anim="fade-up">
            <h2 class="section-title mb-6"><?= htmlspecialchars($content['vision_title']) ?></h2>
            <div><p style="font-size:1.125rem;line-height:1.7;margin-bottom:2rem;opacity:0.9"><?= htmlspecialchars($content['vision_subtitle']) ?></p>
              <div class="vision-grid">
                <div class="vision-card" data-anim="fade-up" data-delay="100"><h3 class="vision-card-title"><?= htmlspecialchars($content['vision_1_title']) ?></h3><p class="vision-card-desc"><?= htmlspecialchars($content['vision_1_desc']) ?></p></div>
                <div class="vision-card" data-anim="fade-up" data-delay="200"><h3 class="vision-card-title"><?= htmlspecialchars($content['vision_2_title']) ?></h3><p class="vision-card-desc"><?= htmlspecialchars($content['vision_2_desc']) ?></p></div>
                <div class="vision-card" data-anim="fade-up" data-delay="300"><h3 class="vision-card-title"><?= htmlspecialchars($content['vision_3_title']) ?></h3><p class="vision-card-desc"><?= htmlspecialchars($content['vision_3_desc']) ?></p></div>
                <div class="vision-card" data-anim="fade-up" data-delay="400"><h3 class="vision-card-title"><?= htmlspecialchars($content['vision_4_title']) ?></h3><p class="vision-card-desc"><?= htmlspecialchars($content['vision_4_desc']) ?></p></div>
              </div>
            </div>
          </div>
        </div>
      </section>
      <?php endif; ?>

      <!-- Team -->
      <?php if (sectionEnabled('team')): ?>
      <section id="team" class="py-20" style="background:hsl(var(--background))">
        <div class="section-container">
          <div class="text-center mb-16" data-anim="fade-up">
            <h2 class="section-title mb-4"><?= htmlspecialchars($content['team_title']) ?></h2>
            <p class="section-subtitle"><?= htmlspecialchars($content['team_subtitle']) ?></p>
          </div>
          <div class="team-grid">
            <?php foreach ($team as $t): ?>
            <div class="founder-card" data-anim="fade-scale">
              <div class="founder-image-wrap">
                <?php if (!empty($t['image'])): ?>
                <img src="<?= htmlspecialchars($t['image']) ?>" alt="<?= htmlspecialchars($t['name']) ?>" style="object-position:center 35%" loading="lazy"/>
                <?php else: ?>
                <div style="width:100%;height:100%;background:hsl(var(--muted));display:flex;align-items:center;justify-content:center;font-size:3rem;color:hsl(var(--muted-foreground))">?</div>
                <?php endif; ?>
                <div class="founder-image-gradient"></div>
                <?php if (!empty($t['linkedin'])): ?>
                <a href="<?= htmlspecialchars($t['linkedin']) ?>" target="_blank" rel="noopener noreferrer" class="founder-linkedin"><svg><use href="#icon-linkedin"/></svg></a>
                <?php endif; ?>
                <div class="founder-info">
                  <h3 class="founder-name"><?= htmlspecialchars($t['name']) ?></h3>
                  <p class="founder-role"><?= htmlspecialchars($t['role']) ?></p>
                </div>
              </div>
              <div class="founder-bio"><p><?= htmlspecialchars($t['bio']) ?></p></div>
            </div>
            <?php endforeach; ?>
          </div>
        </div>
      </section>
      <?php endif; ?>

      <!-- Early Access Form -->
      <?php if (sectionEnabled('early_access')): ?>
      <section id="early-access" class="py-20 form-section">
        <div class="section-container">
          <div class="form-card" data-anim="fade-up">
            <div class="text-center mb-12">
              <h2 class="section-title text-center" style="color:hsl(var(--foreground))"><?= htmlspecialchars($content['form_title']) ?></h2>
              <p class="section-subtitle"><?= htmlspecialchars($content['form_subtitle']) ?></p>
            </div>
            <form id="earlyAccessForm" class="form-card-inner">
              <div class="form-group"><label for="fullName" class="form-label">الاسم الكامل *</label><input type="text" id="fullName" name="fullName" required class="form-input" placeholder="أدخل اسمك الكامل"/></div>
              <div class="form-row">
                <div class="form-group"><label for="email" class="form-label">البريد الإلكتروني *</label><input type="email" id="email" name="email" required class="form-input" placeholder="example@email.com"/></div>
                <div class="form-group"><label for="phone" class="form-label">رقم الهاتف *</label><input type="tel" id="phone" name="phone" required class="form-input" placeholder="+966 5X XXX XXXX"/></div>
              </div>
              <div class="form-row">
                <div class="form-group"><label for="city" class="form-label">المدينة / الدولة *</label><input type="text" id="city" name="city" required class="form-input" placeholder="الرياض، السعودية"/></div>
                <div class="form-group"><label for="role" class="form-label">الصفة *</label>
                  <select id="role" name="role" required class="form-select">
                    <option value="">اختر الصفة</option>
                    <option value="student">طالب</option>
                    <option value="parent">ولي أمر</option>
                    <option value="teacher">معلم</option>
                    <option value="institution">مؤسسة تعليمية</option>
                    <option value="investor">مستثمر</option>
                    <option value="partner">شريك محتمل</option>
                  </select>
                </div>
              </div>
              <div class="form-group"><label for="message" class="form-label">رسالة (اختياري)</label><textarea id="message" name="message" rows="4" class="form-textarea" placeholder="أخبرنا عن اهتمامك بالمنصة أو أي استفسارات لديك"></textarea></div>
              <div class="form-checkbox-group">
                <input type="checkbox" id="agreeToTerms" name="agreeToTerms" class="form-checkbox"/>
                <label for="agreeToTerms" class="form-checkbox-label">أوافق على الانضمام للمجموعة التجريبية وأن يتم التواصل معي بخصوص المنصة</label>
              </div>
              <button type="submit" class="btn-primary" style="width:100%">إرسال الطلب</button>
            </form>
          </div>
        </div>
      </section>
      <?php endif; ?>

      <!-- CTA -->
      <?php if (sectionEnabled('cta')): ?>
      <section id="contact" class="py-20 cta-section">
        <div class="section-container">
          <div class="text-center" style="max-width:56rem;margin:0 auto" data-anim="fade-up">
            <h2 class="section-title mb-6" style="font-size:1.875rem;letter-spacing:-0.02em;line-height:1.2"><?= htmlspecialchars($content['cta_title']) ?></h2>
            <p class="section-subtitle" style="color:inherit;opacity:0.9;margin-bottom:2.5rem;font-size:1.25rem"><?= htmlspecialchars($content['cta_subtitle']) ?></p>
            <div class="hero-actions" style="justify-content:center">
              <a href="mailto:<?= htmlspecialchars($site['contact_email'] ?? 'info@learn.gheir.com') ?>" class="btn-white">تواصل معنا</a>
              <a href="#early-access" class="btn-outline"><?= htmlspecialchars($content['hero_cta_primary']) ?></a>
            </div>
          </div>
        </div>
      </section>
      <?php endif; ?>
    </main>

    <!-- Footer -->
    <footer class="footer">
      <div class="section-container footer-inner">
        <div class="footer-brand">
          <a href="#home" class="footer-logo">
            <?php if ($logoType === 'image' && !empty($logoImage)): ?>
            <img src="<?= htmlspecialchars($logoImage) ?>" alt="<?= htmlspecialchars($logoText) ?>" style="width:<?= (int)$logoWidth ?>px;height:<?= (int)$logoHeight ?>px;object-fit:contain">
            <?php else: ?>
            <svg><use href="#icon-graduation-cap"/></svg><span class="logo-text gradient-text"><?= htmlspecialchars($logoText) ?></span>
            <?php endif; ?>
          </a>
          <div class="footer-email"><svg><use href="#icon-mail"/></svg><a href="mailto:<?= htmlspecialchars($social['email'] ?? $site['contact_email'] ?? 'info@learn.gheir.com') ?>"><?= htmlspecialchars($social['email'] ?? $site['contact_email'] ?? 'info@learn.gheir.com') ?></a></div>
        </div>
        <div class="footer-social">
          <div class="social-links">
            <?php if (!empty($social['linkedin'])): ?>
            <a href="<?= htmlspecialchars($social['linkedin']) ?>" target="_blank" rel="noopener noreferrer" class="social-link" aria-label="LinkedIn"><svg><use href="#icon-linkedin"/></svg></a>
            <?php endif; ?>
            <?php if (!empty($social['twitter'])): ?>
            <a href="<?= htmlspecialchars($social['twitter']) ?>" target="_blank" rel="noopener noreferrer" class="social-link" aria-label="Twitter"><svg><use href="#icon-twitter"/></svg></a>
            <?php endif; ?>
            <?php if (!empty($social['instagram'])): ?>
            <a href="<?= htmlspecialchars($social['instagram']) ?>" target="_blank" rel="noopener noreferrer" class="social-link" aria-label="Instagram"><svg><use href="#icon-instagram"/></svg></a>
            <?php endif; ?>
          </div>
          <p class="footer-copy">© <span id="footerYear"></span> <?= htmlspecialchars($site['title'] ?? 'Learn.Gheir') ?>. <?= htmlspecialchars($site['footer_text'] ?? 'جميع الحقوق محفوظة') ?></p>
        </div>
      </div>
    </footer>
  </div>

  <script src="script.js"></script>
</body>
</html>
