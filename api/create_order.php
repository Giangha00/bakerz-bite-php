<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");

require_once("../db/connect.php");

$conn = connect();
$data = json_decode(file_get_contents("php://input"), true);

$name = trim($data["name"] ?? '');
$email = trim($data["email"] ?? '');
$telephone = trim($data["telephone"] ?? '');
$address = trim($data["address"] ?? '');
$cart = $data["cart"] ?? [];

if (!$name || !$email || !$telephone || !$address || empty($cart)) {
    echo json_encode([
        "status" => false,
        "message" => "Missing customer info or cart"
    ]);
    exit;
}

// Tính tổng tiền
$grand_total = 0;
foreach ($cart as $item) {
    $grand_total += floatval($item["price"]) * intval($item["qty"]);
}

// Insert customer
$stmt = $conn->prepare("INSERT INTO customers (name, email, telephone, address) VALUES (?, ?, ?, ?)");
$stmt->bind_param("ssss", $name, $email, $telephone, $address);
if (!$stmt->execute()) {
    echo json_encode(["status" => false, "message" => "Insert customer failed: " . $stmt->error]);
    exit;
}
$customer_id = $conn->insert_id;
$stmt->close();

// Insert order
$order_date = date('Y-m-d H:i:s');
$stmt = $conn->prepare("INSERT INTO orders (customer_id, grand_total, status, order_date) VALUES (?, ?, 'Pending', ?)");
$stmt->bind_param("ids", $customer_id, $grand_total, $order_date);
if (!$stmt->execute()) {
    echo json_encode(["status" => false, "message" => "Insert order failed: " . $stmt->error]);
    exit;
}
$order_id = $conn->insert_id;
$stmt->close();

// Insert order_items (KHÔNG có name)
foreach ($cart as $item) {
    $stmt = $conn->prepare("INSERT INTO order_items (order_id, product_id, qty, price) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("iiid", $order_id, $item["id"], $item["qty"], $item["price"]);
    $stmt->execute();
}
$stmt->close();

$conn->close();

echo json_encode([
    "status" => true,
    "message" => "Order created successfully",
    "order_id" => $order_id,
    "grand_total" => $grand_total
]);