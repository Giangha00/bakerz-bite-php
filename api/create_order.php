<?php

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: *");
header("Access-Control-Allow-Headers: *");

$data = json_decode(file_get_contents("php://input"), true);
$order = $data["order"] ?? null;
$name = $order["name"];
$email = $order["email"] ?? "";
$telephone = $order["telephone"];
$address = $order["address"];
$cart = $order["cart"];

require_once("../db/connect.php");

// Nếu giỏ hàng rỗng => không tạo đơn hàng
if (empty($cart)) {
    echo json_encode([
        "status" => false,
        "message" => "Cart is empty. Cannot create order.",
    ]);
    exit;
}

// Tính tổng tiền
$grand_total = 0;
foreach($cart as $item){
    $grand_total += $item["price"] * $item["qty"];
}
$order_date = date("Y-m-d H:i:s");

// Tạo đơn hàng
$o_insert_sql = "INSERT INTO orders(order_date, grand_total) VALUES('$order_date', '$grand_total')";
$order_id = insert($o_insert_sql);

// Thêm từng sản phẩm vào order_items
foreach($cart as $item){
    $product_id = $item["id"];
    $qty = $item["qty"];
    $price = $item["price"];
    $op_insert_sql = "INSERT INTO order_items(order_id, product_id, qty, price, name, email, telephone, address) VALUES('$order_id', '$product_id', '$qty', '$price', '$name', '$email', '$telephone', '$address')";
    insert($op_insert_sql);
}

$data = [
    "status" => true,
    "message" => "Success",
    "data" => [
        "order_id" => $order_id,
        "grand_total" => number_format($grand_total, 2, ".", ""),
    ]
];

echo json_encode($data);