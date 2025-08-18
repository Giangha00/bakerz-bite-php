<?php

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: *");
header("Access-Control-Allow-Headers: *");

require_once("../db/connect.php");
$data = json_decode(file_get_contents("php://input"), true);

$conn = connect();

$name        = mysqli_real_escape_string($conn, $data["name"] ?? "");
$type        = mysqli_real_escape_string($conn, $data["type"] ?? "");
$description = mysqli_real_escape_string($conn, $data["description"] ?? "");
$qty         = (int)($data["qty"] ?? 0);
$thumbnail   = mysqli_real_escape_string($conn, $data["thumbnail"] ?? "");
$price       = (float)($data["price"] ?? 0);
$images_raw = $data["images"] ?? [];
if (is_string($images_raw)) {
    $images_raw = json_decode($images_raw, true);
}
$images      = mysqli_real_escape_string($conn, json_encode($images_raw));

$ingredients_raw = $data["ingredients"] ?? [];
if (is_string($ingredients_raw)) {
    $ingredients_raw = json_decode($ingredients_raw, true);
}
$ingredients = mysqli_real_escape_string($conn, json_encode($ingredients_raw));

$category_id = (int)($data["category_id"] ?? 1);

$sql = "INSERT INTO products 
        (name, type, description, qty, thumbnail, images, price, ingredients, category_id) 
        VALUES ('$name', '$type', '$description', $qty, '$thumbnail', '$images', $price, '$ingredients', $category_id)";

if (empty($name) || $price <= 0 || $qty <= 0) {
    echo json_encode(["status" => false, "message" => "Missing or invalid product info"]);
    exit;
}


$result = insert($sql);

if ($result !== false) {
    echo json_encode(["status" => true, "message" => "Product inserted", "id" => $result]);
} else {
    echo json_encode(["status" => false, "message" => "Insert failed", "sql" => $sql]);
}