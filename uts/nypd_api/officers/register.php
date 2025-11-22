<?php
header("Content-Type: application/json");
include("../config/database.php");

$data = json_decode(file_get_contents("php://input"));

// Validation
if(empty($data->user) || empty($data->pass)){
    echo json_encode(["status"=>false, "message"=>"Missing fields"]);
    exit;
}

$user = $data->user;
$pass = $data->pass;

// Connect DB
$db = new Database();
$conn = $db->getConnection();

// Query using PDO
$query = "SELECT id, username, password FROM officers WHERE username = ? LIMIT 1";
$stmt = $conn->prepare($query);
$stmt->execute([$user]);

$row = $stmt->fetch(PDO::FETCH_ASSOC);

if($row){
    // Compare password plain
    if($pass === $row['password']){
        echo json_encode([
            "status" => true,
            "message" => "Login success",
            "data" => [
                "id" => $row['id'],
                "username" => $row['username']
            ]
        ]);
    } else {
        echo json_encode(["status"=>false, "message"=>"Wrong password"]);
    }
} else {
    echo json_encode(["status"=>false, "message"=>"User not found"]);
}
?>
