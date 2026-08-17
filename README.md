# OMODA & JAECOO Trang — PHP/MySQL Educational Project

เว็บไซต์ต้นแบบเพื่อการศึกษา ประกอบด้วยหน้าแนะนำรถ ระบบจองทดลองขับ และ Dealer Console สำหรับเจ้าหน้าที่ ข้อมูลธุรกิจทั้งหมดบันทึกใน MySQL ผ่าน PDO และ Prepared Statements โดยไม่ใช้ `localStorage`

## ความต้องการ

- XAMPP (Apache, PHP 8.1+ และ MySQL/MariaDB)
- Browser รุ่นปัจจุบัน

## ติดตั้งบน XAMPP

1. ติดตั้ง XAMPP และเปิด XAMPP Control Panel
2. กด Start ที่ Apache และ MySQL
3. คัดลอกโฟลเดอร์โปรเจกต์ไปที่ `C:\xampp\htdocs\Projectrapeepatjaecooomoda`
4. เปิด `http://localhost/phpmyadmin/`
5. เลือกแท็บ Import แล้วเลือกไฟล์ `database/omoda.sql`
6. กด Import ระบบจะสร้างฐานข้อมูล `omoda_jaecoo_trang` แบบ `utf8mb4`
7. ค่าเริ่มต้นใน `config/database.php` คือ host `localhost`, port `3306`, user `root`, password ว่าง ซึ่งตรงกับ XAMPP ทั่วไป หากเครื่องใช้ค่าอื่นให้ตั้ง environment variables `DB_HOST`, `DB_PORT`, `DB_NAME`, `DB_USER`, `DB_PASS`
8. เปิด `http://localhost/Projectrapeepatjaecooomoda/database/create_admin.php`
9. สร้าง Admin คนแรก โดย Password ต้องยาวอย่างน้อย 8 ตัว และ Secret Code ต้องเป็นตัวเลข 6 หลัก
10. หลังสร้างสำเร็จ ให้ลบหรือเปลี่ยนชื่อ `database/create_admin.php`
11. เปิดเว็บที่ `http://localhost/Projectrapeepatjaecooomoda/`

## ทดสอบ End-to-End

### Booking

1. หน้าเว็บไซต์ เลือกรุ่นและสีรถ
2. เลื่อนไปส่วน “ทดลองขับ” แล้วกรอกข้อมูล
3. กดยืนยัน ระบบต้องแสดง Reference Number รูปแบบ `TRG-xxxxxx-xxxxxx`
4. ตรวจตาราง `leads` ใน phpMyAdmin ต้องพบข้อมูลเดียวกัน

### Login และ Secret Code

1. กด “เจ้าหน้าที่” บน Navigation ซึ่งจะเปิด `admin/login.php`
2. กรอก Staff Code และ Password ที่สร้างไว้
3. กรอก Personal Secret Code 6 หลักในช่องแยก
4. เมื่อถูกต้องจะเข้าสู่ Dashboard หากเปิด `admin/dashboard.php` โดยไม่ Login จะถูกส่งกลับหน้า Login

### Dashboard และ CRUD

1. Dashboard แสดงจำนวนทั้งหมด รายการใหม่ ยืนยันการจอง และขายสำเร็จจาก MySQL
2. หน้า “รายการผู้สนใจ” ค้นหาด้วยชื่อ เบอร์ รุ่น หรือ Reference Number และกรองตามสถานะได้
3. กดแก้ไขเพื่อเปลี่ยนข้อมูล วันนัด สถานะ และ Staff Note
4. เมื่อตั้งสถานะ `confirmed` จะนับเป็นยอดจอง และ `completed` จะนับเป็นยอดขายใน Dashboard
5. กดลบและยืนยันเพื่อทดสอบ Delete

### Logout

1. กด “ออกจากระบบ” ระบบทำลาย Session และกลับหน้าเว็บไซต์หลัก
2. ลองเปิด URL Dashboard โดยตรง ระบบต้องส่งไปหน้า Login

## โครงสร้างสำคัญ

- `index.php` หน้าเว็บไซต์ลูกค้า
- `api/create_booking.php` รับและตรวจข้อมูล Booking ก่อนบันทึก MySQL
- `config/` การตั้งค่าและ PDO connection
- `admin/` Login, Secret verification, Dashboard, Lead CRUD และ Staff management
- `admin/includes/` Session guard, CSRF และ layout
- `database/omoda.sql` Schema
- `database/create_admin.php` เครื่องมือสร้าง Admin ครั้งแรก
- `assets/` CSS และ JavaScript สำหรับ UI เท่านั้น
- `cars/` รูปรถและโลโก้เดิม

## Security ที่ใช้

- `password_hash()` และ `password_verify()` สำหรับ Password และ Secret Code
- PDO Prepared Statements และ server-side validation
- PHP Session พร้อม HttpOnly และ SameSite cookies
- Session ID regeneration หลังผ่านแต่ละขั้น
- CSRF token ใน action ของเจ้าหน้าที่
- `htmlspecialchars()` เมื่อแสดงข้อมูล
- จำกัดการลอง Login/Secret Code 5 ครั้งต่อ 15 นาทีใน Session พร้อมหน่วงเมื่อกรอกผิด
- Role authorization สำหรับหน้าจัดการเจ้าหน้าที่

## ข้อจำกัด

- GitHub Pages รัน PHP/MySQL ไม่ได้ จึงใช้ repository เก็บ source code แต่ต้องทดลองผ่าน XAMPP หรือ PHP hosting
- Rate limit แบบ Session เหมาะกับโปรเจกต์การศึกษา หากใช้จริงควรเก็บ attempt ตาม IP/account ใน Redis หรือ Database
- ยังไม่มีระบบส่ง SMS/Email และไม่มีระบบสำรองข้อมูลอัตโนมัติ
- ก่อน Production ต้องตั้ง Database password, เปิด HTTPS, ปิด `display_errors`, ลบ `database/create_admin.php` และจำกัดสิทธิ์ Database user
