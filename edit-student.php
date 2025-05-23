<?php
require 'includes/auth.php';
require 'includes/db.php';

$id = $_GET['id'];
$stmt = $pdo->prepare("SELECT * FROM students WHERE id = ?");
$stmt->execute([$id]);
$student = $stmt->fetch();

if (!$student) {
    echo "Étudiant introuvable.";
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name  = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $level = $_POST['level'];

    $update = $pdo->prepare("UPDATE students SET name = ?, email = ?, phone = ?, level = ? WHERE id = ?");
    $update->execute([$name, $email, $phone, $level, $id]);

    header("Location: students.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>Modifier étudiant</title>
  <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
  <div class="form-container">
    <h2>✏️ Modifier étudiant</h2>
    <form method="POST">
      
      <label for="nomcomplet">Nom et prenom :</label>
      <input type="text" name="name" placeholder="entrer votre nom et prenom" value="<?= htmlspecialchars($student['name']) ?>" required>
     
      <label for="email">Email :</label>
      <input type="email" name="email" placeholder="entrer vote email" value="<?= htmlspecialchars($student['email']) ?>" required>
      
      <label for="number">nombre :</label>
      <input type="text" name="phone" placeholder="entrer votre numero du telephone" value="<?= htmlspecialchars($student['phone']) ?>" required>
      
    
      <label for="level">Niveau :</label>
      <select name="level" required>
        <option value="1ère année" <?= $student['level'] == '1ère année' ? 'selected' : '' ?>>1ère année</option>
        <option value="2ème année" <?= $student['level'] == '2ème année' ? 'selected' : '' ?>>2ème année</option>
        <option value="3ème année" <?= $student['level'] == '3ème année' ? 'selected' : '' ?>>3ème année</option>
        <option value="Master" <?= $student['level'] == 'Master' ? 'selected' : '' ?>>Master</option>
      </select>

      <button type="submit">💾 Enregistrer</button>
    </form>

    <a href="students.php">← Retour à la liste</a>
  </div>
</body>
</html>
