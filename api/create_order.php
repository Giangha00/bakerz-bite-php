<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: *");
header("Access-Control-Allow-Headers: *");
header("Content-Type: application/json");

require_once("../db/connect.php");
$pdo = getPDO();

$data = json_decode(file_get_contents("php://input"), true);

$name = $data["name"] ?? null;
$email = $data["email"] ?? null;
$telephone = $data["telephone"] ?? null;
$address = $data["address"] ?? null;
$cart = $data["cart"] ?? [];

if (!$name || !$email || !$telephone || !$address || empty($cart)) {
    echo json_encode([
        "status" => false,
        "message" => "Missing order info",
    ]);
    exit;
}

try {
    $pdo->beginTransaction();

    // insert customer
    $stmt = $pdo->prepare("INSERT INTO customers (name,email,telephone,address) VALUES (?,?,?,?)");
    $stmt->execute([$name,$email,$telephone,$address]);
    $customer_id = $pdo->lastInsertId();

    // insert order
    $stmt = $pdo->prepare("
        INSERT INTO orders (customer_id, grand_total, status)
        VALUES (?, 0, 'Pending')
    ");
    $stmt->execute([$customer_id]);
    $order_id = $pdo->lastInsertId();

    $grand_total = 0;

    $stmt = $pdo->prepare("INSERT INTO order_items (order_id, product_id, qty, price) VALUES (?,?,?,?)");
    foreach ($cart as $item) {
        $product_id = $item['id'];
        $qty = $item['qty'];
        $price = (float)$item['price'];
        $grand_total += $price * $qty;

        $stmt->execute([$order_id, $product_id, $qty, $price]);
    }

    // update grand total
    $stmt = $pdo->prepare("UPDATE orders SET grand_total=? WHERE id=?");
    $stmt->execute([$grand_total,$order_id]);

    $pdo->commit();

    echo json_encode([
        "status" => true,
        "order_id" => $order_id,
        "grand_total" => $grand_total
    ]);

} catch (Exception $e) {
    $pdo->rollBack();
    echo json_encode([
        "status" => false,
        "message" => "Create order failed: " . $e->getMessage()
    ]);
}