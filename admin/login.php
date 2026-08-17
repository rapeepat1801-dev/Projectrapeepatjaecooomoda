<?php
declare(strict_types=1);
require_once __DIR__ . '/includes/auth.php';
if (!empty($_SESSION['staff_id'])) { header('Location: ' . base_url(empty($_SESSION['verified_2fa']) ? 'admin/verify.php' : 'admin/dashboard.php')); exit; }
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf(); $now = time(); $attempts = $_SESSION['login_attempts'] ?? [];
    $attempts = array_values(array_filter($attempts, fn($t) => $t > $now - 900));
    if (count($attempts) >= 5) $error = 'ลองเข้าสู่ระบบหลายครั้งเกินไป กรุณารอ 15 นาที';
    else {
        $code = strtoupper(trim((string)($_POST['staff_code'] ?? ''))); $password = (string)($_POST['password'] ?? '');
        $stmt = db()->prepare('SELECT id, name, password_hash, role, is_active FROM staff WHERE staff_code = ? LIMIT 1'); $stmt->execute([$code]); $staff = $stmt->fetch();
        if ($staff && $staff['is_active'] && password_verify($password, $staff['password_hash'])) {
            session_regenerate_id(true); $_SESSION['staff_id'] = (int)$staff['id']; $_SESSION['staff_name'] = $staff['name']; $_SESSION['role'] = $staff['role']; $_SESSION['verified_2fa'] = false; $_SESSION['login_attempts'] = [];
            $touch = db()->prepare('UPDATE staff SET last_login_at = NOW() WHERE id = ?'); $touch->execute([$staff['id']]);
            header('Location: ' . base_url('admin/verify.php')); exit;
        }
        $_SESSION['login_attempts'][] = $now; usleep(500000); $error = 'รหัสเจ้าหน้าที่หรือรหัสผ่านไม่ถูกต้อง';
    }
}
?><!doctype html><html lang="th"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>เข้าสู่ระบบเจ้าหน้าที่</title><link rel="stylesheet" href="<?= e(base_url('assets/css/admin.css')) ?>"></head><body><div class="auth-page"><div class="auth-art"></div><div class="auth-wrap"><form class="auth-card" method="post"><img class="auth-logo" src="<?= e(base_url('cars/brand-logo.svg')) ?>" alt="OMODA & JAECOO"><span class="eyebrow">TRANG · DEALER CONSOLE</span><h1>เข้าสู่ระบบเจ้าหน้าที่</h1><p>สำหรับเจ้าหน้าที่ที่ได้รับอนุญาตเท่านั้น</p><?= csrf_field() ?><?php if ($error): ?><div class="error"><?= e($error) ?></div><?php endif ?><label class="field">รหัสเจ้าหน้าที่<input name="staff_code" required autocomplete="username" maxlength="30" placeholder="STF001"></label><label class="field">รหัสผ่าน<input name="password" type="password" required autocomplete="current-password"></label><button class="btn full">เข้าสู่ระบบ</button><a class="back" href="<?= e(base_url()) ?>">← กลับหน้าเว็บไซต์หลัก</a></form></div></div></body></html>
