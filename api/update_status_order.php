<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: *");

require_once("../db/connect.php");

$data = json_decode(file_get_contents("php://input"), true);
$order_id = $data["order_id"] ?? null;
$status   = $data["status"] ?? null;

if (!$order_id || !$status) {
    echo json_encode([
        "status" => false,
        "message" => "Missing order_id or status"
    ]);
    exit;
}

$conn = connect();
$stmt = $conn->prepare("UPDATE orders SET status = ? WHERE id = ?");
$stmt->bind_param("si", $status, $order_id);

if ($stmt->execute()) {
    echo json_encode([
        "status" => true,
        "message" => "Order status updated successfully"
    ]);
} else {
    echo json_encode([
        "status" => false,
        "message" => "Failed to update: " . $stmt->error
    ]);
}

$stmt->close();
$conn->close();