<?php
function connect(){
    $host = 'localhost';
    $user = 'root';
    $password = 'root';
    $db_name = 'bakery';
    $conn = new mysqli($host, $user, $password, $db_name);
    if ($conn->connect_error) {
        die("Connection failed");
    }
    return $conn;
}

function getPDO() {
    try {
        $pdo = new PDO("mysql:host=localhost;dbname=bakery", "root", "root");
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $pdo;
    } catch (PDOException $e) {
        die("Lỗi kết nối CSDL: " . $e->getMessage());
    }
}

// get data
function query($sql){
    $conn = connect();
    return $conn->query($sql);
}

// Thêm khách hàng
function insert($sql){
    $conn = connect();
    $conn->query($sql);
    return $conn->insert_id;
}