<?php
include __DIR__ . '/config/connection.php';

if (!isset($_POST['levelID'])) { echo "-1"; exit; }

$levelID = (int)$_POST['levelID'];
$userIP = $_SERVER['HTTP_CF_CONNECTING_IP'] ?? $_SERVER['REMOTE_ADDR'];

try {
    $checkLike = $db->prepare("SELECT 1 FROM likes WHERE levelID = :levelID AND ip = :ip");
    $checkLike->execute([':levelID' => $levelID, ':ip' => $userIP]);

    if ($checkLike->fetch()) { echo "-1"; exit; }

    $db->prepare("INSERT INTO likes (levelID, ip) VALUES (:levelID, :ip)")->execute([':levelID' => $levelID, ':ip' => $userIP]);
    $db->prepare("UPDATE levels SET likes = likes + 1 WHERE id = :levelID")->execute([':levelID' => $levelID]);
    echo "1";
} catch (Exception $e) {
    echo "-1";
}