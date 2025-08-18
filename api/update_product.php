<?php

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: *");
header("Access-Control-Allow-Headers: *");

require_once("../db/connect.php");

$conn = connect();

$data = json_decode(file_get_contents("php://input"), true);

$id          = (int)($data["id"] ?? 0);
$name        = mysqli_real_escape_string($conn, $data["name"] ?? "");
$type        = mysqli_real_escape_string($conn, $data["type"] ?? "");
$description = mysqli_real_escape_string($conn, $data["description"] ?? "");
$qty         = (int)($data["qty"] ?? 0);
$thumbnail   = mysqli_real_escape_string($conn, $data["thumbnail"] ?? "");
$price       = (float)($data["price"] ?? "");

$images_raw = $data["images"] ?? [];
if (is_string($images_raw)) $images_raw = json_decode($images_raw, true);
$images = mysqli_real_escape_string($conn, json_encode($images_raw));

$ingredients_raw = $data["ingredients"] ?? [];
if (is_string($ingredients_raw)) $ingredients_raw = json_decode($ingredients_raw, true);
$ingredients = mysqli_real_escape_string($conn, json_encode($ingredients_raw));

$category_id = (int)($data["category_id"] ?? 1);

$sql = "UPDATE products SET 
            name='$name',
            type='$type',
            description='$description',
            qty=$qty,
            thumbnail='$thumbnail',
            price=$price,
            images='$images',
            ingredients='$ingredients',
            category_id=$category_id
        WHERE id=$id";

$result = mysqli_query($conn, $sql);

if ($result) {
    echo json_encode([
        "status" => "success",
        "message" => "Product updated successfully."
    ]);
} else {
    echo json_encode([
        "status" => "error",
        "message" => "Failed to update product: " . mysqli_error($conn)
    ]);
}