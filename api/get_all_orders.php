<?php

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: *");
header("Access-Control-Allow-Headers: *");

require_once("../db/connect.php");

$sql = "
SELECT 
    o.*,
    c.name AS customer_name,
    c.telephone AS customer_telephone
FROM orders o
JOIN customers c ON o.customer_id = c.id
";


$rs = query($sql);
$list = [];
while ($row = $rs->fetch_assoc()) {
    $list[] = $row;
}

echo json_encode($list);