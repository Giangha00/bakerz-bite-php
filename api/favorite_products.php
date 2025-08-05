<?php

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: *");
header("Access-Control-Allow-Headers: *");

require_once("../db/connect.php");

$data = json_decode(file_get_contents("php://input"), true);


$product_id = $data["product_id"] ?? null;

if ($product_id) {

    $sql = "UPDATE products SET favorite = NOT favorite WHERE id = '$product_id'";
    query($sql);

    $response = [
        "status" => true,
        "message" => "Cập nhật favorite thành công",
    ];
} else {
    $response = [
        "status" => false,
        "message" => "Thiếu product_id",
    ];
}

echo json_encode($data);