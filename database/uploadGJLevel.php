<?php
include __DIR__ . '/config/connection.php';

try {
    $udid = $_POST['udid'];
    $userName = $_POST['userName'] ?? null;
    $secret = $_POST['secret'] ?? null;
    $levelName = $_POST['levelName'] ?? null;
    $levelDesc = $_POST['levelDesc'] ?? null;
    $levelString = $_POST['levelString'] ?? null;
    $levelVersion = $_POST['levelVersion'] ?? 1;
    $levelLength = $_POST['levelLength'] ?? 0;
    $audioTrack = $_POST['audioTrack'] ?? 0;
    $gameVersion = $_POST['gameVersion'] ?? 1;

    if (strlen($userName) > 16 || strlen($levelName) > 26 || $gameVersion > 22) { echo "-1"; exit; }

    $stmt = $db->prepare("SELECT username FROM users WHERE udid = :udid LIMIT 1");
    $stmt->execute([':udid' => $udid]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        if (isset($userName) && $levelName === "register me") {
            $newUsername = preg_replace("/[^a-zA-Z0-9]/", "", $userName);
            $stmt = $db->prepare("INSERT INTO users (udid, username) VALUES (:udid, :username)");
            $stmt->execute([':udid' => $udid, ':username' => $newUsername]);
            echo "1"; exit;
        } else { echo "-1"; exit; }
    }

    $stmt = $db->prepare("SELECT id FROM levels WHERE userName = ? AND name = ?");
    $stmt->execute([$userName, $levelName]);
    if ($stmt->fetch(PDO::FETCH_ASSOC)) { echo "-1"; exit; }

    $stmt = $db->prepare("INSERT INTO levels (name, description, userName, gameVersion, levelVersion, levelLength, audioTrack, udid) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$levelName, $levelDesc, $userName, $gameVersion, $levelVersion, $levelLength, $audioTrack, $udid]);

    $levelID = $db->lastInsertId();
    if (file_put_contents(__DIR__ . "/data/levels/{$levelID}.txt", $levelString) === false) { echo "-1"; } else { echo 1; }

} catch (Exception $e) {
    echo "-1";
}