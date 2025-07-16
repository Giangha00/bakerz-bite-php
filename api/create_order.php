<?php

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: *");
header("Access-Control-Allow-Headers: *");


$data = json_decode(file_get_contents("php://input"), true);
$order = $data["order"] ?? null;
$name = $order["name"];
$telephone = $order["telephone"];
$address = $order["address"];
$cart = $order["cart"];
// thêm khách hàng
require_once("../db/connect.php");
$c_insert_sql = "insert into customers(name, telephone, address) values('$name', '$telephone', '$address')";
$customer_id = insert($c_insert_sql);
// thêm đơn hàng
$grand_total = 0;
foreach($cart as $item){
    $grand_total += $item["price"] * $item["buyQty"];
}
$order_date = date("Y-m-d H:i:s");
$o_insert_sql = "insert into orders(grand_total, order_date, customer_id) values('$grand_total', '$order_date', '$customer_id')";
$order_id = insert($o_insert_sql);
// thêm sản phẩm đơn hàng
foreach($cart as $item){
    $product_id = $item["id"];
    $qty = $item["buyQty"];
    $price = $item["price"];
    $op_insert_sql = "insert into order_products(order_id, product_id, qty, price) values('$order_id', '$product_id', '$qty', '$price')";
    insert($op_insert_sql);
};

$data = [
    "status" => true,
    "message" => "Success",
    "data" => [
        "order_id" => $order_id,
        "grand_total" => number_format($grand_total,2,".",""),
    ]
];

echo json_encode($data);