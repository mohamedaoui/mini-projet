<?php
require 'includes/auth.php';
require 'includes/db.php';

$id = $_GET['id'];
$student = $pdo->prepare("SELECT * FROM students WHERE id = ?");
$student->execute([$id]);
$student = $student->fetch();

$grades = $pdo->prepare("SELECT * FROM grades WHERE student_id = ?");
$grades->execute([$id]);
$grades = $grades->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
  <title>Notes de <?= htmlspecialchars($student['name']) ?></title>
</head>
<body>
  <h2>Notes de <?= htmlspecialchars($student['name']) ?></h2>

  <table border="1" cellpadding="10">
    <tr>
      <th>Matière</th>
      <th>Note</th>
      <th>Date</th>
    </tr>
    <?php foreach ($grades as $g): ?>
      <tr>
        <td><?= htmlspecialchars($g['subject']) ?></td>
        <td><?= $g['grade'] ?></td>
        <td><?= $g['created_at'] ?></td>
      </tr>
    <?php endforeach; ?>
  </table>

  <br>
  <a href="students.php">← Retour à la liste</a>
</body>
</html>
