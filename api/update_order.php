<?php

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: *");
header("Access-Control-Allow-Headers: *");
require_once("../db/connect.php");

$order_id = $GET["order_id"];
$sql = "UPDATE orders SET status = 1 WHERE id = '$order_id'";
query($sql);

$data = [
    "status" => true,
    "message" => "Success",
];

echo json_encode($data);