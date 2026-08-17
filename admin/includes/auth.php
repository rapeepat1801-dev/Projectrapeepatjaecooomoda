<?php
declare(strict_types=1);
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/csrf.php';
start_secure_session();

function require_login(bool $verified = true): void
{
    if (empty($_SESSION['staff_id'])) {
        header('Location: ' . base_url('admin/login.php'));
        exit;
    }
    if ($verified && empty($_SESSION['verified_2fa'])) {
        header('Location: ' . base_url('admin/verify.php'));
        exit;
    }
}

function require_admin(): void
{
    require_login();
    if (($_SESSION['role'] ?? '') !== 'admin') {
        http_response_code(403);
        exit('ไม่มีสิทธิ์เข้าถึง');
    }
}

