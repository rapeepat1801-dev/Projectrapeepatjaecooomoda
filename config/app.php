<?php
declare(strict_types=1);

const APP_NAME = 'OMODA & JAECOO Trang';
const APP_TIMEZONE = 'Asia/Bangkok';
date_default_timezone_set(APP_TIMEZONE);

function base_url(string $path = ''): string
{
    $configured = getenv('APP_BASE_URL');
    if ($configured !== false && $configured !== '') {
        return rtrim($configured, '/') . '/' . ltrim($path, '/');
    }
    $script = str_replace('\\', '/', $_SERVER['SCRIPT_NAME'] ?? '');
    $root = preg_replace('#/(admin|api|database)/.*$#', '', $script) ?: '';
    return rtrim($root, '/') . '/' . ltrim($path, '/');
}

function e(?string $value): string
{
    return htmlspecialchars($value ?? '', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function thai_date(?string $date): string
{
    if (!$date) return '-';
    $months = [1=>'ม.ค.',2=>'ก.พ.',3=>'มี.ค.',4=>'เม.ย.',5=>'พ.ค.',6=>'มิ.ย.',7=>'ก.ค.',8=>'ส.ค.',9=>'ก.ย.',10=>'ต.ค.',11=>'พ.ย.',12=>'ธ.ค.'];
    $time = strtotime($date);
    return date('j', $time) . ' ' . $months[(int)date('n', $time)] . ' ' . ((int)date('Y', $time) + 543);
}

function status_label(string $status): string
{
    return ['new'=>'ใหม่','contacted'=>'ติดต่อแล้ว','confirmed'=>'จองแล้ว','completed'=>'ขายแล้ว','cancelled'=>'ยกเลิก'][$status] ?? $status;
}

function start_secure_session(): void
{
    if (session_status() === PHP_SESSION_ACTIVE) return;
    session_set_cookie_params([
        'httponly' => true,
        'secure' => !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
        'samesite' => 'Lax',
        'path' => '/',
    ]);
    session_start();
}
