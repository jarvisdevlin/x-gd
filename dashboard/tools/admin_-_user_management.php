<?php
include '../../database/config/connection.php';
session_start();

if ($_SESSION['isAdmin'] != 1) {
    header("Location: /dashboard");
    exit();
}

$msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];

    if (isset($_POST['delete'])) {
        try {
            $db->beginTransaction();
            $user = $db->prepare("SELECT id FROM users WHERE username = ?")->execute([$username])->fetch(PDO::FETCH_ASSOC);

            if ($user) {
                $userID = $user['id'];
                $db->prepare("DELETE FROM levels WHERE userID = ?")->execute([$userID]);
                $db->prepare("DELETE FROM users WHERE id = ?")->execute([$userID]);
                $db->commit();
                $msg = "User deleted successfully.";
            } else {
                $msg = "User not found.";
            }
        } catch (PDOException $e) {
            $db->rollBack();
            $msg = "Error: " . $e->getMessage();
        }
    }

    if (isset($_POST['change'])) {
        $newUsername = $_POST['newUsername'];

        try {
            $db->beginTransaction();
            $db->prepare("UPDATE users SET username = ? WHERE username = ?")->execute([$newUsername, $username]);
            $db->prepare("UPDATE levels SET userName = ? WHERE userName = ?")->execute([$newUsername, $username]);
            $db->commit();
            $msg = "Username successfully changed.";
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
    <title>User Management</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; padding: 20px; }
        form { margin-top: 20px; }
        label { display: block; margin: 5px 0; }
        input { padding: 5px; width: 200px; margin-bottom: 10px; }
        button { padding: 5px 10px; margin-right: 10px; }
        .msg { margin-top: 20px; padding: 10px; background-color: #f0f0f0; }
        .msg.success { background-color: #d4edda; color: #155724; }
        .msg.error { background-color: #f8d7da; color: #721c24; }
        .box { margin-top: 25px; padding: 15px; border: 3px solid black; background-color: #f9f9f9; box-shadow: 0 0 10px rgba(0, 0, 0, 0.1); }
    </style>
</head>
<body>

<h1>User Management</h1>

<?php if ($msg): ?>
    <div class="msg <?php echo strpos($msg, 'success') !== false ? 'success' : 'error'; ?>">
        <?php echo $msg; ?>
    </div>
<?php endif; ?>

<?php if (isset($_POST['changeu'])): ?>
    <form method="post">
        <label for="newUsername">Enter New Username:</label>
        <input type="text" id="newUsername" name="newUsername" required>
        <input type="hidden" name="username" value="<?php echo htmlspecialchars($_POST['username']); ?>">
        <button type="submit" name="change">Update Username</button>
    </form>
<?php else: ?>
    <form method="post">
        <label for="username">Enter Username:</label>
        <input type="text" id="username" name="username" required>
    
        <div>
            <button type="submit" name="delete">Delete User</button>
            <button type="submit" name="changeu">Change Username</button>
        </div>
    </form>
<?php endif; ?>

<div class="box">
    <p>Changing a username also changes the username on levels and the user would have to be forced to change their name in-game.</p>
    <p>Deleting a user also deletes their levels they have uploaded, be careful.</p>
</div>

</body>
</html>