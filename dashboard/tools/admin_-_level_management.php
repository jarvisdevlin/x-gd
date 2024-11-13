<?php
include '../../database/config/connection.php';
session_start();

if ($_SESSION['isAdmin'] != 1) {
    header("Location: /dashboard");
    exit();
}

$msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $levelID = $_POST['levelID'];
    $level = $db->prepare("SELECT * FROM levels WHERE id = ?")->execute([$levelID])->fetch(PDO::FETCH_ASSOC);

    if (!$level) {
        $msg = "Level not found.";
    } else {
        try {
            $db->beginTransaction();

            if (isset($_POST['levelName'])) {
                $db->prepare("UPDATE levels SET name = ? WHERE id = ?")->execute([$_POST['levelName'], $levelID]);
            }

            if (isset($_POST['description'])) {
                $db->prepare("UPDATE levels SET description = ? WHERE id = ?")->execute([$_POST['description'], $levelID]);
            }

            if (isset($_POST['difficulty'])) {
                $rating = 0;
                switch ($_POST['difficulty']) {
                    case 'easy': $rating = 10; break;
                    case 'normal': $rating = 20; break;
                    case 'hard': $rating = 30; break;
                    case 'harder': $rating = 40; break;
                    case 'insane': $rating = 50; break;
                }
                $db->prepare("UPDATE levels SET rating = ? WHERE id = ?")->execute([$rating, $levelID]);
            }

            if (isset($_POST['featured'])) {
                $featured = $_POST['featured'] == '1' ? 1 : 0;
                $db->prepare("UPDATE levels SET featured = ? WHERE id = ?")->execute([$featured, $levelID]);
            }

            if (isset($_POST['downloads'])) {
                $db->prepare("UPDATE levels SET downloads = ? WHERE id = ?")->execute([$_POST['downloads'], $levelID]);
            }

            if (isset($_POST['likes'])) {
                $db->prepare("UPDATE levels SET likes = ? WHERE id = ?")->execute([$_POST['likes'], $levelID]);
            }

            $db->commit();
            $msg = "Level updated successfully.";
        } catch (PDOException $e) {
            $db->rollBack();
            $msg = "Error: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Level Management</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; padding: 20px; }
        form { margin-top: 20px; }
        label { display: block; margin: 5px 0; }
        input, select { padding: 5px; width: 200px; margin-bottom: 10px; }
        button { padding: 5px 10px; margin-right: 10px; }
        .msg { margin-top: 20px; padding: 10px; background-color: #f0f0f0; }
        .msg.success { background-color: #d4edda; color: #155724; }
        .msg.error { background-color: #f8d7da; color: #721c24; }
    </style>
</head>
<body>

<h1>Level Management</h1>

<?php if ($msg): ?>
    <div class="msg <?php echo strpos($msg, 'success') !== false ? 'success' : 'error'; ?>">
        <?php echo $msg; ?>
    </div>
<?php endif; ?>

<form method="post">
    <label for="levelID">Enter Level ID:</label>
    <input type="number" id="levelID" name="levelID" required>
    <button type="submit">Fetch Level</button>
</form>

<?php if (isset($level) && $level): ?>
    <h2>Edit Level ID: <?php echo htmlspecialchars($level['id']); ?></h2>

    <form method="post">
        <input type="hidden" name="levelID" value="<?php echo htmlspecialchars($level['id']); ?>">

        <label for="levelName">Level Name:</label>
        <input type="text" id="levelName" name="levelName" value="<?php echo htmlspecialchars($level['name']); ?>">

        <label for="description">Description:</label>
        <input type="text" id="description" name="description" value="<?php echo htmlspecialchars($level['description']); ?>">

        <label for="difficulty">Difficulty:</label>
        <select id="difficulty" name="difficulty">
            <option value="easy" <?php echo $level['rating'] == 10 ? 'selected' : ''; ?>>Easy</option>
            <option value="normal" <?php echo $level['rating'] == 20 ? 'selected' : ''; ?>>Normal</option>
            <option value="hard" <?php echo $level['rating'] == 30 ? 'selected' : ''; ?>>Hard</option>
            <option value="harder" <?php echo $level['rating'] == 40 ? 'selected' : ''; ?>>Harder</option>
            <option value="insane" <?php echo $level['rating'] == 50 ? 'selected' : ''; ?>>Insane</option>
        </select>

        <label for="featured">Featured:</label>
        <select id="featured" name="featured">
            <option value="1" <?php echo $level['featured'] == 1 ? 'selected' : ''; ?>>Yes</option>
            <option value="0" <?php echo $level['featured'] == 0 ? 'selected' : ''; ?>>No</option>
        </select>

        <label for="downloads">Downloads:</label>
        <input type="number" id="downloads" name="downloads" value="<?php echo htmlspecialchars($level['downloads']); ?>">

        <label for="likes">Likes:</label>
        <input type="number" id="likes" name="likes" value="<?php echo htmlspecialchars($level['likes']); ?>">

        <button type="submit">Update Level</button>
    </form>
<?php endif; ?>

</body>
</html>