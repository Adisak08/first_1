<!DOCTYPE html>
<html lang="en">

<?php

$username = $_POST(user);
$password = $_POST(password);

require("dbconnect.php");
$sql = "SELECT * FROM employees ORDER BY fname ASC"; 
$result = mysqli_query($con,$sql);
$row = mysqli_fetch_row($result);

?>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>

<body>
    
</body>
</html>
