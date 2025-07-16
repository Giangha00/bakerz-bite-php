<?php

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: *");
header("Access-Control-Allow-Headers: *");

require_once("../db/connect.php");

$s = $_GET["search"];
$sql = "SELECT * FROM products WHERE name like '%$s%' ORDER BY id DESC LIMIT 8";
$rs = query($sql);
$list = [];
while ($row = $rs->fetch_assoc()) {
    $list[] = $row;
}

$data = [
    "status" => true,
    "message" => "Success",
    "data" => $list,
];

echo json_encode($data);