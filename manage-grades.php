<?php
require 'includes/auth.php';
require 'includes/db.php';
if (!empty($error))
    echo "<p style='color:red;'>$error</p>";

$student_id = $_GET['id'] ?? null;


$stmt = $pdo->prepare("SELECT * FROM students WHERE id = ?");
$stmt->execute([$student_id]);
$student = $stmt->fetch();

if (!$student) {
    die("Étudiant introuvable.");
}

$editMode = false;
$editGrade = null;

if (isset($_POST['add_grade'])) {
    $subject = trim($_POST['subject']);
    $grade = floatval($_POST['grade']);

    
    $check = $pdo->prepare("SELECT * FROM grades WHERE student_id = ? AND subject = ?");
    $check->execute([$student_id, $subject]);

    if ($check->rowCount() > 0) {
        $error = "❌ Cette matière est déjà enregistrée pour cet étudiant.";
    } else {
        $coefficient = intval($_POST['coefficient']);
        $stmt = $pdo->prepare("INSERT INTO grades (student_id, subject, grade, coefficient) VALUES (?, ?, ?, ?)");
        $stmt->execute([$student_id, $subject, $grade, $coefficient]);

        header("Location: manage-grades.php?id=" . $student_id);
        exit;
    }
}


if (isset($_GET['edit'])) {
    $editMode = true;
    $gradeId = $_GET['edit'];

    $getGrade = $pdo->prepare("SELECT * FROM grades WHERE id = ? AND student_id = ?");
    $getGrade->execute([$gradeId, $student_id]);
    $editGrade = $getGrade->fetch();
}


if (isset($_POST['update_grade'])) {
    $subject = trim($_POST['subject']);
    $grade = floatval($_POST['grade']);
    $gradeId = $_GET['edit'];


    $check = $pdo->prepare("SELECT * FROM grades WHERE student_id = ? AND subject = ? AND id != ?");
    $check->execute([$student_id, $subject, $gradeId]);

    if ($check->rowCount() > 0) {
        $error = "❌ Cette matière est déjà utilisée pour une autre note.";
    } else {
        $coefficient = intval($_POST['coefficient']);

        $update = $pdo->prepare("UPDATE grades SET subject = ?, grade = ?, coefficient = ? WHERE id = ? AND student_id = ?");
        $update->execute([$subject, $grade, $coefficient, $gradeId, $student_id]);


        header("Location: manage-grades.php?id=" . $student_id);
        exit;
    }
}



if (isset($_GET['delete'])) {
    $gradeId = $_GET['delete'];
    $pdo->prepare("DELETE FROM grades WHERE id = ?")->execute([$gradeId]);
    header("Location: manage-grades.php?id=" . $student_id);
    exit;
}


$notes = $pdo->prepare("SELECT * FROM grades WHERE student_id = ?");
$notes->execute([$student_id]);
$grades = $notes->fetchAll();
?>

<!DOCTYPE html>
<html>

<head>
    <title>Gérer les notes</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>

<body>
    <div class="form-container">
        <h3><?= $editMode ? '✏️ Modifier une note' : '➕ Ajouter une note' ?></h3>
        <form method="POST"
            action="manage-grades.php?id=<?= $student_id ?><?= $editMode ? '&edit=' . $editGrade['id'] : '' ?>">
            <input type="text" name="subject" placeholder="Matière"
                value="<?= $editMode ? htmlspecialchars($editGrade['subject']) : '' ?>" required>


            <input type="number" name="coefficient" placeholder="Coefficient" value="1" min="1" required>

            <input type="number" step="0.01" name="grade" placeholder="Note (sur 20)"
                value="<?= $editMode ? $editGrade['grade'] : '' ?>" required>
            <button type="submit" name="<?= $editMode ? 'update_grade' : 'add_grade' ?>">
                <?= $editMode ? 'Enregistrer les modifications' : 'Ajouter la note' ?>
            </button>
        </form>


        <br>
        <h3>📝 Liste des notes</h3>
        <?php if (count($grades) > 0): ?>
            <table border="1" cellpadding="8">
                <tr>
                    <th>Matière</th>
                    <th>Note</th>
                    <th>Coefficient</th>
                    <th>Actions</th>
                </tr>
                <?php foreach ($grades as $grade): ?>
                    <tr>
                        <td><?= htmlspecialchars($grade['subject']) ?></td>
                        <td><?= $grade['grade'] ?></td>
                        <td><?= $grade['coefficient'] ?></td>
                        <td>
                            <a href="?id=<?= $student_id ?>&edit=<?= $grade['id'] ?>">✏️ Modifier</a> |
                            <a href="?id=<?= $student_id ?>&delete=<?= $grade['id'] ?>"
                                onclick="return confirm('Supprimer cette note ?')">❌ Supprimer</a>
                        </td>

                    </tr>
                <?php endforeach; ?>
            </table>
        <?php else: ?>
            <p>Aucune note enregistrée.</p>
        <?php endif; ?>

        <br>
        <a href="students.php">← Retour à la liste des étudiants</a>
    </div>
</body>

</html>