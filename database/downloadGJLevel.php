<?php
include __DIR__ . '/config/connection.php';

try {
    $levelID = isset($_POST['levelID']) ? (int)$_POST['levelID'] : null;
    if (!$levelID) { 
        echo "-1"; 
        return; 
    }

    $stmt = $db->prepare("SELECT levels.*, users.id AS userID FROM levels 
                          JOIN users ON levels.userName = users.username 
                          WHERE levels.id = :levelID");
    $stmt->bindParam(':levelID', $levelID, PDO::PARAM_INT);
    $stmt->execute();

    $level = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$level) {
        echo "-1"; 
        return; 
    }

    $updateStmt = $db->prepare("UPDATE levels SET downloads = downloads + 1 WHERE id = :levelID");
    $updateStmt->bindParam(':levelID', $levelID, PDO::PARAM_INT);

    $levelStr = file_exists(__DIR__ . "/data/levels/{$levelID}.txt") ? file_get_contents(__DIR__ . "/data/levels/{$levelID}.txt") : '';
    $response = "1:{$level['id']}:2:{$level['name']}:3:{$level['description']}:4:{$levelStr}:5:{$level['levelVersion']}:" .
                "6:{$level['userID']}:8:10:9:{$level['rating']}:10:{$level['downloads']}:13:{$level['gameVersion']}:" .
                "14:{$level['likes']}:15:{$level['levelLength']}";

    $userInfo = "{$level['userID']}:{$level['userName']}:0";
    echo $response . "#$userInfo#1664b8bb919b0822a4408752c37a9fb5f651f813";
} catch (Exception $e) {
    echo "-1";
}
?>
