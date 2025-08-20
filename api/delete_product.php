 <?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: *");
header("Access-Control-Allow-Headers: *");
header("Content-Type: application/json");

require_once("../db/connect.php");


$data = json_decode(file_get_contents("php://input"), true);

$product_id = isset($data['id']) ? intval($data['id']) : 0;

if ($product_id <= 0) {
    echo json_encode(["status" => false, "message" => "Invalid product id"]);
    exit;
}


$delete_product = "DELETE FROM products WHERE id = $product_id";
$result = query($delete_product);

if ($result) {
 
    $res = query("SELECT MAX(id) AS max_id FROM products");
    $row = $res->fetch_assoc();
    $next_id = $row['max_id'] ? $row['max_id'] + 1 : 1;

    query("ALTER TABLE products AUTO_INCREMENT = $next_id");

    echo json_encode([
        "status" => true,
        "message" => "Product deleted, AUTO_INCREMENT reset",
        "next_id" => $next_id
    ]);
} else {
    echo json_encode(["status" => false, "message" => "Delete failed"]);
}
?>