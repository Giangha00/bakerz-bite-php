<?php

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: *");
header("Access-Control-Allow-Headers: *");

require_once("../db/connect.php");
$pdo = getPDO();

$order_id = $_GET['order_id'] ?? null;

if (!$order_id) {
    echo json_encode(['error' => 'Missing order_id']);
    exit;
}


$sql = "
SELECT 
  o.id,
  o.order_date,
  o.grand_total,
  i.name as customer_name,
  i.email as customer_email,
  i.telephone as customer_telephone,
  i.address as customer_address,
  o.status AS order_status,
  p.id as product_id,
  i.qty as quantity,
  p.name as product_name,
  p.price as product_price
FROM orders o
JOIN order_items i ON o.id = i.order_id
JOIN products p ON i.product_id = p.id
WHERE o.id = ?
";

$stmt = $pdo->prepare($sql);
$stmt->execute([$order_id]);
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);



$order = null;

foreach ($rows as $row) {
    if (!$order) {
        $order = [
            'id' => $row['id'],
            'order_date' => $row['order_date'],
            'grand_total' => $row['grand_total'],
            'customer_name' => $row['customer_name'],
            'customer_email' => $row['customer_email'],
            'customer_telephone' => $row['customer_telephone'],
            'customer_address' => $row['customer_address'],
            'order_status' => $row['order_status'],
            'order_items' => []
        ];
    }

    $order['order_items'][] = [
        'product_id' => $row['product_id'],
        'quantity' => $row['quantity'],
        'product_name' => $row['product_name'],
        'product_price' => $row['product_price']
    ];
}


echo json_encode($order);