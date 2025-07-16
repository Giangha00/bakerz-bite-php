<?php

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: *");
header("Access-Control-Allow-Headers: *");

$data = json_decode(file_get_contents("php://input"), true);
$feedback = $data["feedback"] ?? null;
$name = $feedback["name"];
$email = $feedback["email"];
$message = $feedback["message"];
$rate = $feedback["rate"];

require_once("../db/connect.php");

$c_insert_sql = "INSERT INTO feedback(name, email, message, rate) VALUES('$name', '$email', '$message', '$rate')";
$feedback_id = insert($c_insert_sql);

$data = [
    "status" => true,
    "message" => "Success",
];

echo json_encode($data);