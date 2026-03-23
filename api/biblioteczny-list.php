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

$allowedColumns = [
    0 => 'id',
    1 => 'autor',
    2 => 'tytul',
    3 => 'rok_wydania_wydawca',
    4 => 'nr_inwentarzowy'
];

$columnName = $allowedColumns[$orderColumnIndex] ?? 'id';

/*
|--------------------------------------------------------------------------
| WHERE
|--------------------------------------------------------------------------
*/

$where = '';
$params = [];

if ($search !== '') {

    $where = " WHERE (
        id LIKE :s0
        OR autor LIKE :s1
        OR tytul LIKE :s2
        OR rok_wydania_wydawca LIKE :s3
        OR nr_inwentarzowy LIKE :s4
    )";

    $params = [
        ':s0' => "%$search%",
        ':s1' => "%$search%",
        ':s2' => "%$search%",
        ':s3' => "%$search%",
        ':s4' => "%$search%"
    ];
}

/*
|--------------------------------------------------------------------------
| SELECT
|--------------------------------------------------------------------------
*/

$sql = "
    SELECT 
        id,
        autor,
        tytul,
        rok_wydania_wydawca,
        nr_inwentarzowy
    FROM biblioteczny
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

$totalRecords = $pdo->query("
    SELECT COUNT(*) FROM biblioteczny
")->fetchColumn();

/*
|--------------------------------------------------------------------------
| recordsFiltered
|--------------------------------------------------------------------------
*/

if ($search !== '') {

    $countSql = "
        SELECT COUNT(*) 
        FROM biblioteczny
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
| JSON
|--------------------------------------------------------------------------
*/

echo json_encode([
    "draw" => intval($draw),
    "recordsTotal" => intval($totalRecords),
    "recordsFiltered" => intval($recordsFiltered),
    "data" => $data
], JSON_UNESCAPED_UNICODE);

exit;
