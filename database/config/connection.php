<?php
// WARNING: Please put the database outside of reach from anyone thats able to access your GDPS because
// if you put it inside your public webserver then you are begging to have everyones data stolen.
$dbFile = '../../../../database.db'; // Path to SQLite3 database.

try {
    $db = new PDO('sqlite:' . __DIR__ . $dbFile);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}
?>