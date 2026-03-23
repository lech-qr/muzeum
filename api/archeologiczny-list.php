<?php

require_once '../config.php';

header('Content-Type: application/json; charset=utf-8');

$draw   = $_POST['draw'] ?? 0;
$start  = (int) ($_POST['start'] ?? 0);
$length = (int) ($_POST['length'] ?? 25);
$search = trim($_POST['search']['value'] ?? '');

$orderColumnIndex = $_POST['order'][0]['column'] ?? 0;
$orderDir = $_POST['order'][0]['dir'] ?? 'asc';
$orderDir = $orderDir === 'desc' ? 'DESC' : 'ASC';

/*
|--------------------------------------------------------------------------
| MAPOWANIE KOLUMN (zgodne z DataTables)
|--------------------------------------------------------------------------
*/

$allowedColumns = [
    0 => 'id',
    1 => 'przedmiot',
    2 => 'szczegolowa_charakterystyka',
    3 => 'zdjecie_lokalne',
    4 => 'chronologia',
    5 => 'kultura',
    6 => 'nr_inwentarzowy'
];

$columnName = $allowedColumns[$orderColumnIndex] ?? 'id';

/*
|--------------------------------------------------------------------------
| WHERE (wyszukiwanie)
|--------------------------------------------------------------------------
*/

$where = '';
$params = [];

if ($search !== '') {

    $where = " WHERE (
        id LIKE :s0
        OR przedmiot LIKE :s1
        OR szczegolowa_charakterystyka LIKE :s2
        OR chronologia LIKE :s3
        OR kultura LIKE :s4
        OR nr_inwentarzowy LIKE :s5
    )";

    $params = [
        ':s0' => "%$search%",
        ':s1' => "%$search%",
        ':s2' => "%$search%",
        ':s3' => "%$search%",
        ':s4' => "%$search%",
        ':s5' => "%$search%"
    ];
}

/*
|--------------------------------------------------------------------------
| GŁÓWNE ZAPYTANIE
|--------------------------------------------------------------------------
*/

$sql = "
    SELECT 
        id,
        przedmiot,
        szczegolowa_charakterystyka,
        zdjecie_lokalne,
        chronologia,
        kultura,
        nr_inwentarzowy
    FROM archeologiczny
    $where
    ORDER BY $columnName $orderDir
    LIMIT $start, $length
";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);

/*
|--------------------------------------------------------------------------
| recordsTotal
|--------------------------------------------------------------------------
*/

$totalRecords = $pdo->query("SELECT COUNT(*) FROM archeologiczny")
    ->fetchColumn();

/*
|--------------------------------------------------------------------------
| recordsFiltered
|--------------------------------------------------------------------------
*/

if ($search !== '') {

    $countSql = "
        SELECT COUNT(*)
        FROM archeologiczny
        $where
    ";

    $countStmt = $pdo->prepare($countSql);
    $countStmt->execute($params);
    $recordsFiltered = $countStmt->fetchColumn();

} else {
    $recordsFiltered = $totalRecords;
}

/*
|--------------------------------------------------------------------------
| RESPONSE
|--------------------------------------------------------------------------
*/

$response = [
    "draw" => intval($draw),
    "recordsTotal" => intval($totalRecords),
    "recordsFiltered" => intval($recordsFiltered),
    "data" => $data
];

echo json_encode($response, JSON_UNESCAPED_UNICODE);
exit;