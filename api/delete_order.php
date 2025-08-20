<?php

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: *");
header("Access-Control-Allow-Headers: *");

require_once("../db/connect.php");

$order_id = $_POST['order_id'] ?? null;

if ($order_id <= 0) {
    echo json_encode(["status" => false, "message" => "Invalid order id"]);
    exit;
}

$dl_order_items = "DELETE FROM order_items WHERE order_id = $order_id";
$result_items = query($dl_order_items);


$dl_order = "DELETE FROM orders WHERE id = $order_id";
$result = query($dl_order);


if ($result && $result_items) {
    
    $res = query("SELECT MAX(id) AS max_id FROM orders");
    $row = $res->fetch_assoc();
    $next_id = $row['max_id'] ? $row['max_id'] + 1 : 1;

    
    query("ALTER TABLE orders AUTO_INCREMENT = $next_id");

    echo json_encode([
        "status" => true,
        "message" => "Order deleted, AUTO_INCREMENT reset",
        "next_id" => $next_id
    ]);
} else {
    echo json_encode(["status" => false, "message" => "Delete failed"]);
}