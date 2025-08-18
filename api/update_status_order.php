<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

require_once("../db/connect.php");

$order_id = $_POST["order_id"] ?? null;
$status   = $_POST["status"] ?? null;

if ($order_id && $status) {
    $sql = "UPDATE orders SET status = '$status' WHERE id = '$order_id'";
    $result = query($sql);

    if ($result) {
        echo json_encode(["status" => true, "message" => "Order updated"]);
    } else {
        echo json_encode(["status" => false, "message" => "Update failed"]);
    }
} else {
    echo json_encode(["status" => false, "message" => "Invalid data"]);
}