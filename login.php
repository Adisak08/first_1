<?php
session_start();

// เชื่อมต่อฐานข้อมูล
require("connectdata.php");

try {
    $obj = new PDO($dsn, $username, $password);
    $obj->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // ตรวจสอบว่าฟอร์มถูกส่งมาหรือไม่
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // รับค่าจากฟอร์มล็อกอิน
        $username_id = trim($_POST["user"]);
        $password_id = trim($_POST["password"]);

        if (!empty($username_id) && !empty($password_id)) {
            // ดึงข้อมูลรหัสผ่านจากฐานข้อมูล
            $sql = "SELECT * FROM employees WHERE username = ?";
            $stmt = $obj->prepare($sql);
            $stmt->bindParam(1, $username_id, PDO::PARAM_STR);
            $stmt->execute();

            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($row) {
                $stored_password = $row['password'];

                // ตรวจสอบรหัสผ่านที่ผู้ใช้ป้อน
                if (password_verify($password_id, $stored_password)) {
                    // ตั้งค่าเซสชัน
                    $_SESSION['username'] = $username_id;
                    echo "<script>alert('ล๊อคอินสำเร็จ!'); window.location.href='index.html';</script>";
                } else {
                    echo "<script>alert('รหัสผ่านไม่ถูกต้อง!'); window.location.href='index.html';</script>";                 

                }
            } else {
                echo "<script>alert('ไม่พบข้อมูลผู้ใช้!'); window.location.href='index.html';</script>";
            }
        } else {
            echo "<script>alert('กรุณากรอกข้อมูลให้ครบถ้วน!'); window.location.href='index.html';</script>";
        }
    }
} catch (PDOException $e) {
    // บันทึกข้อผิดพลาดใน log แทนการแสดงผลให้ผู้ใช้
    error_log($e->getMessage());
    echo "<script>alert('เกิดข้อผิดพลาดในการเชื่อมต่อกับฐานข้อมูล!'); window.location.href='index.html';</script>";
}
?>

