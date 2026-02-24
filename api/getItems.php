<?php

ini_set('display_errors', 1);
error_reporting(E_ALL);
// ini_set('display_errors', 0);
// error_reporting(0);

require_once '../config.php';

header('Content-Type: application/json; charset=utf-8');

$draw   = $_POST['draw'] ?? 0;
$start  = $_POST['start'] ?? 0;
$length = $_POST['length'] ?? 25;
$search = $_POST['search']['value'] ?? '';
$search = trim($search);

$start  = (int) $start;
$length = (int) $length;

$orderColumnIndex = $_POST['order'][0]['column'] ?? 0;
$orderDir = $_POST['order'][0]['dir'] ?? 'asc';
$orderDir = $orderDir === 'desc' ? 'DESC' : 'ASC';

/*
|--------------------------------------------------------------------------
| MAPOWANIE KOLUMN (musi odpowiadać kolejności w DataTables)
|--------------------------------------------------------------------------
*/

$allowedColumns = [
    0 => 'r.id',
    1 => 'd.nazwa',
    2 => 'r.przedmiot',
    3 => 'r.zdjecie_lokalne',
    4 => 'r.autor_szkola',
    5 => 'r.nr_inwentarzowy',
    6 => 'r.opis'
];

$columnName = $allowedColumns[$orderColumnIndex] ?? 'r.id';

/*
|--------------------------------------------------------------------------
| WHERE (wyszukiwanie)
|--------------------------------------------------------------------------
*/

$where = '';
$params = [];

if ($search !== '') {

    $where = " WHERE (
        r.przedmiot LIKE :s1
        OR r.nr_inwentarzowy LIKE :s2
        OR r.autor_szkola LIKE :s3
        OR r.czas_powstania LIKE :s4
        OR d.nazwa LIKE :s5
    )";


    $params = [
        ':s1' => "%$search%",
        ':s2' => "%$search%",
        ':s3' => "%$search%",
        ':s4' => "%$search%",
        ':s5' => "%$search%"
    ];
}

/*
|--------------------------------------------------------------------------
| GŁÓWNE ZAPYTANIE (z LIMIT)
|--------------------------------------------------------------------------
*/

$sql = "
    SELECT 
        r.id,
        d.nazwa AS dzial,
        r.przedmiot,
        r.zdjecie_lokalne,
        r.autor_szkola,
        r.nr_inwentarzowy,
        r.opis
    FROM rekordy r
    JOIN dzialy d ON r.dzialy_id = d.id
    $where
    ORDER BY $columnName $orderDir
    LIMIT $start, $length
";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);

$stmt->execute();
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);

/*
|--------------------------------------------------------------------------
| recordsTotal (bez filtra)
|--------------------------------------------------------------------------
*/

$totalRecords = $pdo->query("
    SELECT COUNT(*) 
    FROM rekordy
")->fetchColumn();

/*
|--------------------------------------------------------------------------
| recordsFiltered (z filtrem)
|--------------------------------------------------------------------------
*/

if ($search !== '') {

    $countSql = "
        SELECT COUNT(*)
        FROM rekordy r
        JOIN dzialy d ON r.dzialy_id = d.id
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
| ODPOWIEDŹ JSON
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
