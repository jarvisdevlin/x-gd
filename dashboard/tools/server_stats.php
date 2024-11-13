<?php
include '../../database/config/connection.php';
session_start();
$message = '';

$activeUsers = $db->query("SELECT COUNT(*) FROM users")->fetchColumn();
$dashboardUsers = $db->query("SELECT COUNT(*) FROM db_users")->fetchColumn();
$totalLevels = $db->query("SELECT COUNT(*) FROM levels")->fetchColumn();
$featuredLevels = $db->query("SELECT COUNT(*) FROM levels WHERE featured = 1")->fetchColumn();
$ratedLevels = $db->query("SELECT COUNT(*) FROM levels WHERE rating > 0")->fetchColumn();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GDPS Statistics</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; padding: 20px; }
        .box { margin-top: 25px; padding: 15px; border: 3px solid black; background-color: #f9f9f9;  box-shadow: 0 0 10px rgba(0, 0, 0, 0.1); }
        h3 { font-size: 20px; font-weight: bold; }
    </style>
</head>
<body>

<h1>GDPS Statistics</h1>

<div class="box">
    <h3>Users In-Game: <?php echo $activeUsers; ?></h3>
    <h3>Dashboard Users: <?php echo $dashboardUsers; ?></h3>
    <h3>Total Levels: <?php echo $totalLevels; ?></h3>
    <h3>Featured Levels: <?php echo $featuredLevels; ?></h3>
    <h3>Rated Levels: <?php echo $ratedLevels; ?></h3>
</div>

</body>
</html>