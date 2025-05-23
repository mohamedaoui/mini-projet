<?php
$pdo = new PDO('mysql:host=localhost;dbname=student_db', 'root', '');


$password = password_hash("admin123", PASSWORD_DEFAULT);


$pdo->prepare("INSERT INTO admins (username, password) VALUES (?, ?)")
    ->execute(["admin", $password]);

echo "Admin inserted successfully. You can now delete this file.";
?>
