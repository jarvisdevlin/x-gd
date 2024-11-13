<?php
include '../database/config/connection.php';
session_start();

function redirect() {
    $url = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    if (substr($url, -1) !== '/' && strpos(basename($url), '.') === false) {
        header("Location: $url/");
        exit();
    }
}

redirect();

$loginErr = $registerMsg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['login'])) {
        $stmt = $db->prepare("SELECT * FROM db_users WHERE username = ?");
        $stmt->execute([$_POST['username']]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($_POST['password'], $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['isAdmin'] = $user['isAdmin'];
        } else $loginErr = 'Invalid username or password.';
    }
    if (isset($_POST['register'])) {
        try {
            $stmt = $db->prepare("INSERT INTO db_users (username, password, isAdmin) VALUES (?, ?, ?)");
            $stmt->execute([$_POST['username'], password_hash($_POST['password'], PASSWORD_DEFAULT), 0]);
            $registerMsg = "Registration successful! You can now log in.";
        } catch (PDOException $e) {
            $registerMsg = $e->getCode() === '23000' ? "Username already exists." : "Registration failed: " . $e->getMessage();
        }
    }
}

if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: index.php");
    exit();
}

$tools = array_diff(scandir('./tools'), ['.', '..']);

function formatName($file) {
    return ucwords(str_replace('_', ' ', pathinfo($file, PATHINFO_FILENAME)));
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>x-gd Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body { font-family: Arial, sans-serif; margin: 0; display: flex; }
        .sidebar { width: 60px; background-color: black; color: white; padding: 10px 0; text-align: center; height: 100vh; position: fixed; display: flex; flex-direction: column; justify-content: space-between; }
        .sidebar h2 { writing-mode: vertical-rl; transform: rotate(180deg); margin: 0; font-size: 42px; }
        .sidebar a { display: block; color: white; font-size: 24px; margin: 10px 0; }
        .main { margin-left: 60px; padding: 20px; text-align: center; flex-grow: 1; }
        form { display: inline-block; text-align: left; margin-top: 10px; }
        form input, form button { margin: 5px; }
        form input { padding: 5px; width: 150px; }
        form button { padding: 5px 10px; }
        .menu { display: flex; justify-content: center; flex-wrap: wrap; margin-top: 20px; }
        .menu-item { margin: 10px; padding: 10px 20px; border: 1px solid #333; text-decoration: none; color: #333; font-weight: bold; }
    </style>
</head>
<body>
    <div class="sidebar">
        <h2>x-gd</h2>
        <div>
            <a href="https://github.com/jarvisdevlin/x-gd" target="_blank"><i class="fab fa-github"></i></a>
            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="?logout=1"><i class="fas fa-sign-out-alt"></i></a>
            <?php else: ?>
                <a href="#login"><i class="fas fa-user-circle"></i></a>
            <?php endif; ?>
        </div>
    </div>
    <div class="main">
        <?php if (!isset($_SESSION['user_id'])): ?>
            <?php if ($loginErr): ?><p style="color: red;"><?php echo $loginErr; ?></p><?php endif; ?>
            <?php if ($registerMsg): ?><p style="color: green;"><?php echo $registerMsg; ?></p><?php endif; ?>
            
            <form method="post">
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" required>
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required>
                <button type="submit" name="login">Login</button>
                <button type="submit" name="register">Register</button>
            </form>
        <?php else: ?>
            <h1>Welcome, <?php if ($_SESSION['isAdmin']): ?>(ADMIN)<?php endif; ?> <?php echo htmlspecialchars($_SESSION['username']); ?>!</h1>
        <?php endif; ?>

        <div class="menu">
            <?php foreach ($tools as $file): ?>
                <a href="./tools/<?php echo $file; ?>" class="menu-item"><?php echo formatName($file); ?></a>
            <?php endforeach; ?>
        </div>
    </div>
</body>
</html>