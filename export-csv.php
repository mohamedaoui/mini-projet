<?php
require 'includes/auth.php';
require 'includes/db.php';


$students = $pdo->query("SELECT * FROM students ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);


header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=students.csv');


$output = fopen('php://output', 'w');


fputcsv($output, ['ID', 'Nom', 'Email', 'Téléphone', 'Date d\'ajout']);


foreach ($students as $student) {
    fputcsv($output, [
        $student['id'],
        $student['name'],
        $student['email'],
        $student['phone'],
        $student['created_at']
    ]);
}


fclose($output);
exit;
