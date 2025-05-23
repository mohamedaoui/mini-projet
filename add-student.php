<?php
require 'includes/auth.php';
require 'includes/db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name  = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $level = $_POST['level'];

    $stmt = $pdo->prepare("INSERT INTO students (name, email, phone, level) VALUES (?, ?, ?, ?)");
    $stmt->execute([$name, $email, $phone, $level]);

    $success = "✅ Étudiant ajouté avec succès !";
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Ajouter un étudiant</title>
  <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

  <div class="add-student-container">
    <h2>➕ Ajouter un étudiant</h2>

    <form method="POST">
      <input type="text" name="name" placeholder="Nom complet" required>
      <input type="email" name="email" placeholder="Adresse email" required>
      <input type="text" name="phone" placeholder="Numéro de téléphone" required>

      <select name="level" required>
        <option disabled selected>Niveau</option>
        <option>1ère année</option>
        <option>2ème année</option>
        <option>3ème année</option>
        <option>Master</option>
      </select>

      <button type="submit">Ajouter</button>
    </form>

    <?php if (!empty($success)) echo "<p class='success-message'>$success</p>"; ?>

    <a class="back-link" href="dashboard.php">← Retour au tableau de bord</a>
  </div>

</body>
</html>
