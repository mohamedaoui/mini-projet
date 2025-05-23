<?php
require 'includes/auth.php';
require 'includes/db.php';

$stmt = $pdo->query("
  SELECT s.name, 
         ROUND(SUM(g.grade * g.coefficient) / NULLIF(SUM(g.coefficient), 0), 2) AS average
  FROM students s
  LEFT JOIN grades g ON s.id = g.student_id
  GROUP BY s.id
  ORDER BY s.id DESC
");
$averages = $stmt->fetchAll();

$labels = array_column($averages, 'name');
$data = array_map(fn($s) => $s['average'] ?? 0, $averages);

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>Dashboard</title>
  <link rel="stylesheet" href="assets/css/style.css">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <link rel="stylesheet" href="assets/css/style.css">

</head>

<body>

  <div class="dashboard-container">
    <h2>Bienvenue, <?= htmlspecialchars($_SESSION['admin']) ?></h2>

    <canvas id="avgChart" width="1000" height="700"></canvas>

    <script>
      const ctx = document.getElementById('avgChart').getContext('2d');

      new Chart(ctx, {
        type: 'bar',
        data: {
          labels: <?= json_encode($labels) ?>,
          datasets: [{
            label: 'Moyenne pondérée',
            data: <?= json_encode($data) ?>,
            backgroundColor: 'rgba(46, 204, 113, 0.7)',
            borderColor: 'rgba(39, 174, 96, 1)',
            borderWidth: 2,
            borderRadius: 6
          }]
        },
        options: {
          responsive: true,
          plugins: {
            title: {
              display: true,
              text: 'Moyenne par étudiant',
              font: {
                size: 20,
                weight: 'bold'
              }
            },
            tooltip: {
              callbacks: {
                label: context => ` ${context.raw} / 20`
              }
            },
            legend: {
              display: false
            }
          },
          scales: {
            y: {
              beginAtZero: true,
              max: 20,
              ticks: {
                stepSize: 1
              },
              title: {
                display: true,
                text: 'Note / 20',
                font: { size: 14 }
              }
            },
            x: {
              ticks: {
                autoSkip: false,
                maxRotation: 45,
                minRotation: 30
              }
            }
          }
        }
      });
    </script>

    <a href="add-student.php">➕ Ajouter un étudiant</a>
    <a href="students.php">📋 Voir les étudiants</a>
    <a href="logout.php">🚪 Déconnexion</a>
  </div>

</body>

</html>