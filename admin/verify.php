<?php
declare(strict_types=1);
require_once __DIR__ . '/includes/auth.php'; require_login(false);
if (!empty($_SESSION['verified_2fa'])) { header('Location:' . base_url('admin/dashboard.php')); exit; }
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf(); $now = time(); $attempts = $_SESSION['pin_attempts'] ?? []; $attempts = array_values(array_filter($attempts, fn($t) => $t > $now - 900));
    if (count($attempts) >= 5) $error = 'กรอกรหัสผิดหลายครั้งเกินไป กรุณาเข้าสู่ระบบใหม่';
    else {
        $pin = implode('', array_map(fn($i) => (string)($_POST['pin'][$i] ?? ''), range(0, 5))); $stmt = db()->prepare('SELECT secret_code_hash FROM staff WHERE id = ? AND is_active = 1'); $stmt->execute([$_SESSION['staff_id']]); $hash = $stmt->fetchColumn();
        if (preg_match('/^\d{6}$/', $pin) && $hash && password_verify($pin, $hash)) { session_regenerate_id(true); $_SESSION['verified_2fa'] = true; $_SESSION['pin_attempts'] = []; header('Location:' . base_url('admin/dashboard.php')); exit; }
        $_SESSION['pin_attempts'][] = $now; usleep(500000); $error = 'รหัสลับไม่ถูกต้อง';
    }
}
?><!doctype html><html lang="th"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>ยืนยันตัวตน</title><link rel="stylesheet" href="<?= e(base_url('assets/css/admin.css')) ?>"></head><body><div class="auth-page"><div class="auth-art"></div><div class="auth-wrap"><form class="auth-card" method="post"><img class="auth-logo" src="<?= e(base_url('cars/brand-logo.svg')) ?>" alt=""><span class="eyebrow">SECOND-STEP VERIFICATION</span><h1>ยืนยันตัวตนของคุณ</h1><p>กรอกรหัสลับประจำตัว 6 หลัก</p><?= csrf_field() ?><?php if ($error): ?><div class="error"><?= e($error) ?></div><?php endif ?><div class="pin-row"><?php for ($i=0;$i<6;$i++): ?><input data-pin name="pin[<?= $i ?>]" inputmode="numeric" type="password" maxlength="1" required autocomplete="off"><?php endfor ?></div><br><button class="btn full">ยืนยัน</button><a class="back" href="login.php">← กลับไปหน้าเข้าสู่ระบบ</a></form></div></div><script src="<?= e(base_url('assets/js/admin.js')) ?>"></script></body></html>

