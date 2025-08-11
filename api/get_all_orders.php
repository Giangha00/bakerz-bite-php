<?php

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: *");
header("Access-Control-Allow-Headers: *");

require_once("../db/connect.php");

$sql = "
SELECT 
    o.*,
    (SELECT name FROM order_items WHERE order_id = o.id LIMIT 1) AS name
FROM orders o
";

$rs = query($sql);
$list = [];
while ($row = $rs->fetch_assoc()) {
    $list[] = $row;
}

echo json_encode($list);