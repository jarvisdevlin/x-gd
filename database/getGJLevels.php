<?php
include __DIR__ . '/config/connection.php';

try {
    $type = isset($_POST['type']) ? (int)$_POST['type'] : null;
    $search = isset($_POST['str']) && $_POST['str'] !== "" ? '%' . $_POST['str'] . '%' : null;
    $page = max((int)$_POST['page'], 0) + 1;
    $difficulty = isset($_POST['diff']) ? $_POST['diff'] : "-";
    $length = isset($_POST['len']) ? $_POST['len'] : "-";
    $perPage = 10;
    $offset = ($page - 1) * $perPage;

    $countQuery = "SELECT COUNT(*) AS total FROM levels WHERE 1=1";
    $levelQuery = "SELECT levels.id, levels.name, levels.description, levels.gameVersion, levels.levelVersion, levels.levelLength, 
                          levels.audioTrack, levels.created_at, levels.updated_at, levels.udid, levels.downloads, levels.userName, 
                          levels.likes, levels.rating, users.id AS userID
                   FROM levels JOIN users ON levels.userName = users.username WHERE 1=1";

    if ($search) $countQuery .= " AND levels.name LIKE :search";
    if ($search) $levelQuery .= " AND levels.name LIKE :search";

    $ratingMap = ['-1' => '0', '1' => '10', '2' => '20', '3' => '30', '4' => '40', '5' => '50'];
    if ($difficulty !== "-") {
        $ratings = array_filter(array_map(fn($x) => $ratingMap[$x] ?? null, explode(",", $difficulty)));
        if (count($ratings) === 1) $countQuery .= " AND levels.rating = :difficulty";
        if (count($ratings) > 1) $countQuery .= " AND levels.rating IN (" . implode(",", $ratings) . ")";
        if (count($ratings) === 1) $levelQuery .= " AND levels.rating = :difficulty";
        if (count($ratings) > 1) $levelQuery .= " AND levels.rating IN (" . implode(",", $ratings) . ")";
    }

    if ($length !== "-") {
        $lengths = explode(",", $length);
        if (count($lengths) === 1) $countQuery .= " AND levels.levelLength = :length";
        if (count($lengths) > 1) $countQuery .= " AND levels.levelLength IN (" . implode(",", array_map('intval', $lengths)) . ")";
        if (count($lengths) === 1) $levelQuery .= " AND levels.levelLength = :length";
        if (count($lengths) > 1) $levelQuery .= " AND levels.levelLength IN (" . implode(",", array_map('intval', $lengths)) . ")";
    }

    $levelQuery .= match ($type) {
        1 => " ORDER BY levels.downloads DESC",
        2 => " ORDER BY levels.likes DESC",
        4 => " ORDER BY levels.created_at DESC",
        6 => " AND levels.featured = 1 ORDER BY levels.id DESC",
        default => " ORDER BY levels.likes DESC"
    };

    $levelQuery .= " LIMIT :offset, :perPage";

    $countStmt = $db->prepare($countQuery);
    if ($search) $countStmt->bindParam(':search', $search, PDO::PARAM_STR);
    if ($difficulty !== "-" && count($ratings) === 1) $countStmt->bindParam(':difficulty', $ratings[0], PDO::PARAM_INT);
    if ($length !== "-" && count($lengths) === 1) $countStmt->bindParam(':length', $lengths[0], PDO::PARAM_INT);
    $countStmt->execute();
    $totalLevels = (int)$countStmt->fetchColumn();

    $levelStmt = $db->prepare($levelQuery);
    if ($search) $levelStmt->bindParam(':search', $search, PDO::PARAM_STR);
    if ($difficulty !== "-" && count($ratings) === 1) $levelStmt->bindParam(':difficulty', $ratings[0], PDO::PARAM_INT);
    if ($length !== "-" && count($lengths) === 1) $levelStmt->bindParam(':length', $lengths[0], PDO::PARAM_INT);
    $levelStmt->bindParam(':offset', $offset, PDO::PARAM_INT);
    $levelStmt->bindParam(':perPage', $perPage, PDO::PARAM_INT);
    $levelStmt->execute();

    $levels = $levelStmt->fetchAll(PDO::FETCH_ASSOC);
    $levelResponses = [];
    $userInfo = "";

    foreach ($levels as $level) {
        if (empty($level['id']) || empty($level['name']) || empty($level['userName'])) continue;
        $levelResponses[] = "1:{$level['id']}:2:{$level['name']}:5:{$level['levelVersion']}:6:{$level['userID']}:8:10:9:{$level['rating']}:10:{$level['downloads']}:13:{$level['gameVersion']}:14:{$level['likes']}:15:{$level['levelLength']}";
        $userInfo .= "{$level['userID']}:{$level['userName']}:0|";
    }

    echo implode("|", $levelResponses) . "#$userInfo#{$totalLevels}:$offset:$perPage#1664b8bb919b0822a4408752c37a9fb5f651f813";
} catch (Exception $e) {
    echo "-1";
}