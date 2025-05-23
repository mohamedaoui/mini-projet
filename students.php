<?php
require 'includes/auth.php';
require 'includes/db.php';

$search = '';
$students = [];

if (isset($_GET['search']) && !empty(trim($_GET['search']))) {
  $search = trim($_GET['search']);
  $stmt = $pdo->prepare("SELECT * FROM students WHERE name LIKE ? OR email LIKE ? OR phone LIKE ? ORDER BY id DESC");
  $stmt->execute(["%$search%", "%$search%", "%$search%"]);
  $students = $stmt->fetchAll();
} else {
  $stmt = $pdo->query("
    SELECT s.*, 
           ROUND(SUM(g.grade * g.coefficient) / NULLIF(SUM(g.coefficient), 0), 2) AS average
    FROM students s
    LEFT JOIN grades g ON s.id = g.student_id
    GROUP BY s.id
    ORDER BY s.id DESC
");

  $students = $stmt->fetchAll();
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
  <meta charset="UTF-8">
  <title>Liste des étudiants</title>
  <link rel="stylesheet" href="assets/css/style.css">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body>

  <div class="students-container">
    <h2>📋 Liste des étudiants</h2>

    <form method="GET" action="students.php">
      <input type="text" name="search" placeholder="Rechercher par nom, email ou téléphone"
        value="<?= htmlspecialchars($search) ?>">
      <button type="submit">Rechercher</button>
      <a href="students.php" class="button">Réinitialiser</a>
    </form>

    <table>
      <tr>
        <th>ID</th>
        <th>Nom</th>
        <th>Email</th>
        <th>Téléphone</th>
        <th>Moyenne</th>
        <th>Actions</th>
      </tr>
      <?php foreach ($students as $student): ?>
        <tr>
          <td><?= $student['id'] ?></td>
          <td><?= htmlspecialchars($student['name']) ?></td>
          <td><?= htmlspecialchars($student['email']) ?></td>
          <td><?= htmlspecialchars($student['phone']) ?></td>
          <td><?= $student['average'] !== null ? $student['average'] : '—' ?></td>

          <td class="actions">
            <a href="edit-student.php?id=<?= $student['id'] ?>">✏️ Modifier</a> |
            <a href="delete-student.php?id=<?= $student['id'] ?>"
              onclick="return confirm('Supprimer cet étudiant ?');">🗑️ Supprimer</a> |
            <a href="manage-grades.php?id=<?= $student['id'] ?>">📚 Notes</a>
          </td>

        </tr>
      <?php endforeach; ?>
    </table>

    <br>
    <a href="export-csv.php" class="button">⬇️ Exporter en CSV</a>
    <br><br>
    <a class="button" href="dashboard.php">← Retour au tableau de bord</a>

    <div class="chart-container">
      <canvas id="chart" height="100"></canvas>
    </div>
  </div>

  <?php

  $labels = [];
  $averages = [];

  foreach ($students as $s) {
    $labels[] = htmlspecialchars($s['name']);
    $averages[] = $s['average'] ?? 0;
  }
  ?>

  

</body>

</html>