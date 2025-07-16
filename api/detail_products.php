<?php

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: *");
header("Access-Control-Allow-Headers: *");

require_once("../db/connect.php");

$s = $_GET["id"];
$sql = "SELECT * FROM products WHERE id = '$s'";
$rs = query($sql);
$list = null;
while ($row = $rs->fetch_assoc()) {
    $list = $row;
}

$data = [
    "status" => true,
    "message" => "Success",
    "data" => $list,
];

echo json_encode($data);