<?php
// ตรวจสอบว่ามีการส่งข้อมูลจากฟอร์มหรือไม่
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // รับค่าจากฟอร์ม
    $first_name = trim($_POST["first_name"]);
    $last_name = trim($_POST["last_name"]);
    $user_id = trim($_POST["username"]);
    $password_id = trim($_POST["password"]); // แก้ไขการรับรหัสผ่านจากฟอร์ม

    // เชื่อมต่อฐานข้อมูล
    require("connectdata.php");
    $obj = new PDO($dsn, $username, $password);

// เข้ารหัสรหัสผ่าน
    $hashed_password = password_hash($password_id, PASSWORD_DEFAULT);


    try {
        // คำสั่ง SQL เพื่อแทรกรหัสที่ hash แล้วลงในฐานข้อมูล
        $sql = "INSERT INTO employees (fname, lname, username,password) VALUES (:fname, :lname, :username, :hashed_password)";
        $stmt = $obj->prepare($sql);

        // การเชื่อมโยงค่ากับพารามิเตอร์
        $stmt->bindParam(":fname", $first_name, PDO::PARAM_STR);
        $stmt->bindParam(":lname", $last_name, PDO::PARAM_STR);
        $stmt->bindParam(":username", $user_id, PDO::PARAM_STR);
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
</head>
<body>
    <h2>Register</h2>
    <form action="register.php" method="POST">
        <label for="first_name">First Name:</label>
        <input type="text" id="first_name" name="first_name" required><br><br>
        
        <label for="last_name">Last Name:</label>
        <input type="text" id="last_name" name="last_name" required><br><br>
        
        <label for="username">Username:</label>
        <input type="text" id="username" name="username" required><br><br>
        
        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required><br><br>
        
        <input type="submit" value="Register">
    </form>
</body>
</html>
