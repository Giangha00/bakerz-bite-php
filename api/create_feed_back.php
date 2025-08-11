<?php

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: *");
header("Access-Control-Allow-Headers: *");

$data = json_decode(file_get_contents("php://input"), true);
$feedback = $data["feedback"] ?? null;
$name     = $feedback["name"] ?? null;
$email    = $feedback["email"] ?? null;
$rate     = $feedback["rate"] ?? null;
$message  = $feedback["message"] ?? null;

require_once("../db/connect.php");

$conn = connect();


$stmt = $conn->prepare("INSERT INTO feedback (name, email, rate, message) VALUES (?, ?, ?, ?)");
if (!$stmt) {
    echo json_encode([
        "status" => false,
        "message" => "Prepare failed: " . $conn->error,
    ]);
    exit;
}

$stmt->bind_param("ssis", $name, $email, $rate, $message);

if ($stmt->execute()) {
    echo json_encode([
        "status"  => true,
        "message" => "Success",
        "data"    => [
            "name"    => $name,
            "email"   => $email,
            "rate"    => $rate,
            "message" => $message
        ]
    ]);
} else {
    echo json_encode([
        "status"  => false,
        "message" => "Insert failed: " . $stmt->error,
    ]);
}

$stmt->close();
$conn->close();