<?php
// ตรวจสอบว่ามีการส่งข้อมูลจากฟอร์มหรือไม่
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // รับค่าจากฟอร์มและทำการ trim เพื่อลบช่องว่างที่ไม่จำเป็น
    $first_name = trim($_POST["first_name"]);
    $last_name = trim($_POST["last_name"]);
    $user_id = trim($_POST["username"]);
    $email = trim($_POST["email"]); // รับค่า email จากฟอร์ม
    $password_id = trim($_POST["password"]);
    $confirm_password_id = trim($_POST["confirm_password"]);

    // เชื่อมต่อฐานข้อมูล
    require("connectdata.php");
    $obj = new PDO($dsn, $username, $password);

    // ตรวจสอบความถูกต้องของ Email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "ที่อยู่อีเมลไม่ถูกต้อง";
        exit();
    }

    // ตรวจสอบความแข็งแกร่งของรหัสผ่าน
    if (strlen($password_id) < 8 || !preg_match('/[A-Z]/', $password_id) || !preg_match('/[a-z]/', $password_id) || !preg_match('/[0-9]/', $password_id)) {
        echo "รหัสผ่านควรมีความยาวอย่างน้อย 8 ตัวอักษรและประกอบด้วยตัวอักษรใหญ่, เล็ก, และตัวเลข";
        exit();
    }

    // ตรวจสอบว่ารหัสผ่านและการยืนยันรหัสผ่านตรงกันหรือไม่
    if ($password_id !== $confirm_password_id) {
        echo "รหัสผ่านและการยืนยันรหัสผ่านไม่ตรงกัน";
        exit();
    }

    // ตรวจสอบว่าชื่อผู้ใช้หรืออีเมลมีอยู่แล้วหรือไม่
    $sql_check_user = "SELECT COUNT(*) FROM employees WHERE username = ? OR email = ?";
    $stmt_check = $obj->prepare($sql_check_user);
    $stmt_check->bindParam(1, $user_id, PDO::PARAM_STR);
    $stmt_check->bindParam(2, $email, PDO::PARAM_STR);
    $stmt_check->execute();
    $user_exists = $stmt_check->fetchColumn();

    if ($user_exists) {
        echo "ชื่อผู้ใช้หรืออีเมลนี้มีอยู่แล้ว กรุณาเลือกชื่อผู้ใช้อื่น";
        exit();
    }

    // เข้ารหัสรหัสผ่าน
    $hashed_password = password_hash($password_id, PASSWORD_DEFAULT);

    try {
        // คำสั่ง SQL เพื่อแทรกรหัสที่ hash แล้วลงในฐานข้อมูล
        $sql = "INSERT INTO employees (fname, lname, username, email, password) VALUES (:fname, :lname, :username, :email, :hashed_password)";
        $stmt = $obj->prepare($sql);

        // การเชื่อมโยงค่ากับพารามิเตอร์
        $stmt->bindParam(":fname", $first_name, PDO::PARAM_STR);
        $stmt->bindParam(":lname", $last_name, PDO::PARAM_STR);
        $stmt->bindParam(":username", $user_id, PDO::PARAM_STR);
        $stmt->bindParam(":email", $email, PDO::PARAM_STR); // เพิ่มการเชื่อมโยงสำหรับอีเมล
        $stmt->bindParam(":hashed_password", $hashed_password, PDO::PARAM_STR);

        // บันทึกข้อมูล
        if ($stmt->execute()) {
            echo "สมัครสมาชิกสำเร็จ!<br>";
        } else {
            echo "การสมัครสมาชิกล้มเหลว!<br>";
        }
    } catch (PDOException $e) {
        echo "เกิดข้อผิดพลาด: " . $e->getMessage();
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>

    <style>
body {
            background-color: #DCDCDC; /* กำหนดสีพื้นหลังของหน้าเว็บ */
            font-family: Arial, sans-serif; /* กำหนดฟอนต์ให้กับหน้าเว็บ */
            display: flex; /* ใช้ Flexbox เพื่อจัดกึ่งกลางแนวตั้ง */
            justify-content: center; /* จัดให้อยู่กึ่งกลางแนวนอน */
            align-items: top; /* จัดให้อยู่กึ่งกลางแนวตั้ง */
            height: 100vh; /* ตั้งค่าความสูงของหน้าเว็บ */
            margin: 0; /* ลบค่า margin ของหน้าเว็บ */
        }

        /* ใช้ CSS เพื่อกำหนดรูปแบบของฟอร์ม */
        .container {
            background-color: white; /* กำหนดสีพื้นหลังของฟอร์ม */
            padding: 30px; /* เพิ่มพื้นที่ภายในฟอร์ม */
            border-radius: 15px; /* ทำมุมโค้ง */
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); /* เพิ่มเงา */
            width: 300px; /* กำหนดความกว้างของฟอร์ม */
            height: 600px;
            margin-top: 100px;/*กำหนดระยะห่างกับขอบบน*/
        }


</style>
</head>
<body>
<div class = container >
    <div style="text-align: center;">    
    <h2 style="display: inline-block; width: 100px;" >Register</h2>
    <br><br>
    <form action="register.php" method="POST">
        <label for="first_name" style="display: inline-block; width: 150px;">First Name:</label>
        <input type="text" id="first_name" name="first_name" required><br><br><br>
        
        <label for="last_name" style="display: inline-block; width: 150px;">Last Name:</label>
        <input type="text" id="last_name" name="last_name" required><br><br><br>
        
        <label for="email" style="display: inline-block; width: 150px;">Email:</label>
        <input type="text" id="email" name="email" required><br><br><br>

        <label for="username" style="display: inline-block; width: 150px;  ">Username:</label>
        <input type="text" id="username" name="username" required><br><br><br>
        
        <label for="password" style="display: inline-block; width: 150px; ">Password:</label>
        <input type="password" id="password" name="password" required><br><br><br>

        <label for="confirm_password" style="display: inline-block; width: 150px; ">Confirm Password:</label>
        <input type="password" id="confirm_password" name="confirm_password" required><br><br><br><br>

        
        <input type="submit" value="Register" style="display: inline-block; width: 150px;">
    </form>
</div>
</body>
</html>
