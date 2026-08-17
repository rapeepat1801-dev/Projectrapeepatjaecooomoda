<?php
declare(strict_types=1);
require_once __DIR__ . '/../config/database.php';
header('Content-Type: application/json; charset=utf-8');
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { http_response_code(405); echo json_encode(['ok'=>false,'message'=>'Method not allowed']); exit; }

$name = trim((string)($_POST['customer_name'] ?? ''));
$phone = preg_replace('/\s+/', '', trim((string)($_POST['phone'] ?? '')));
$email = trim((string)($_POST['email'] ?? ''));
$model = trim((string)($_POST['model'] ?? ''));
$date = trim((string)($_POST['appointment_date'] ?? ''));
$time = trim((string)($_POST['appointment_time'] ?? ''));
$message = trim((string)($_POST['message'] ?? ''));
$allowedModels = ['JAECOO 5 EV','OMODA C5 EV','JAECOO 6T EV','JAECOO 6T REEV'];
$errors = [];
if (mb_strlen($name) < 2 || mb_strlen($name) > 120) $errors[] = 'กรุณากรอกชื่อให้ถูกต้อง';
if (!preg_match('/^[0-9+\-]{9,20}$/', $phone)) $errors[] = 'กรุณากรอกเบอร์โทรให้ถูกต้อง';
if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'อีเมลไม่ถูกต้อง';
if (!in_array($model, $allowedModels, true)) $errors[] = 'กรุณาเลือกรุ่นรถ';
$dateObject = DateTime::createFromFormat('Y-m-d', $date);
if (!$dateObject || $dateObject->format('Y-m-d') !== $date || $date < date('Y-m-d')) $errors[] = 'วันที่นัดหมายไม่ถูกต้อง';
if (!preg_match('/^([01]\d|2[0-3]):[0-5]\d$/', $time)) $errors[] = 'เวลาไม่ถูกต้อง';
if (empty($_POST['consent'])) $errors[] = 'กรุณายอมรับการติดต่อกลับ';
if ($errors) { http_response_code(422); echo json_encode(['ok'=>false,'message'=>implode(' ', $errors)], JSON_UNESCAPED_UNICODE); exit; }

try {
    $reference = 'TRG-' . date('ymd') . '-' . strtoupper(bin2hex(random_bytes(3)));
    $stmt = db()->prepare('INSERT INTO leads (reference_no, customer_name, phone, email, model, appointment_date, appointment_time, message, status, source) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
    $stmt->execute([$reference,$name,$phone,$email ?: null,$model,$date,$time,$message ?: null,'new','website']);
    echo json_encode(['ok'=>true,'message'=>'รับข้อมูลเรียบร้อยแล้ว','reference'=>$reference], JSON_UNESCAPED_UNICODE);
} catch (Throwable $error) {
    error_log($error->getMessage()); http_response_code(500);
    echo json_encode(['ok'=>false,'message'=>'ไม่สามารถบันทึกข้อมูลได้ กรุณาลองใหม่'], JSON_UNESCAPED_UNICODE);
}
