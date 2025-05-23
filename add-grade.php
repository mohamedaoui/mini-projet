<?php
require 'includes/auth.php';
require 'includes/db.php';

$students = $pdo->query("SELECT id, name FROM students")->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $student_id = $_POST['student_id'];
    $subject = $_POST['subject'];
    $grade = $_POST['grade'];

    $stmt = $pdo->prepare("INSERT INTO grades (student_id, subject, grade) VALUES (?, ?, ?)");
    $stmt->execute([$student_id, $subject, $grade]);

    $success = "Note ajoutée avec succès !";
}
?>

<!DOCTYPE html>
<html>
<head>
  <title>Ajouter une note</title>
</head>
<body>
  <h2>Ajouter une note à un étudiant</h2>

  <?php if (!empty($success)) echo "<p style='color:green;'>$success</p>"; ?>

  <form method="POST">
    <label>Étudiant :</label>
    <select name="student_id" required>
      <option value="">-- Choisir un étudiant --</option>
      <?php foreach ($students as $s): ?>
        <option value="<?= $s['id'] ?>"><?= htmlspecialchars($s['name']) ?></option>
      <?php endforeach; ?>
    </select><br><br>

    <label>Matière :</label>
    <input type="text" name="subject" required><br><br>

    <label>Note :</label>
    <input type="number" step="0.01" name="grade" required><br><br>

    <button type="submit">Ajouter la note</button>
  </form>

  <br>
  <a href="dashboard.php">← Retour au tableau de bord</a>
</body>
</html>
