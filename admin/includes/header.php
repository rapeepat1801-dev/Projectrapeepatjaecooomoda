<?php $pageTitle = $pageTitle ?? 'Dashboard'; ?>
<!doctype html><html lang="th"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title><?= e($pageTitle) ?> · Dealer Console</title><link rel="stylesheet" href="<?= e(base_url('assets/css/admin.css')) ?>"></head>
<body><button class="menu-toggle" data-menu aria-label="เปิดเมนู">☰</button><div class="admin-shell">
<?php require __DIR__ . '/sidebar.php'; ?><main class="admin-main"><header class="topbar"><div><span class="eyebrow">TRANG · DEALER CONSOLE</span><h1><?= e($pageTitle) ?></h1></div><div class="user-chip"><?= e($_SESSION['staff_name'] ?? '') ?></div></header>

