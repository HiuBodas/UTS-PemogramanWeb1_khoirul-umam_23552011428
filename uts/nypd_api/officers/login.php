<?php
header("Content-Type: application/json");
include("../config/database.php");

$data = json_decode(file_get_contents("php://input"));

// Validate input fields
if(empty($data->user) || empty($data->pass)){
    echo json_encode(["status"=>false, "message"=>"Missing fields"]);
    exit;
}

$username = $data->user;
$password = $data->pass;

// Connect database
$db = new Database();
$conn = $db->getConnection();

$query = "SELECT id, name, username, password FROM officers WHERE username = ? LIMIT 1";
$stmt = $conn->prepare($query);
$stmt->execute([$username]);

$row = $stmt->fetch(PDO::FETCH_ASSOC);

if($row){
    if(password_verify($password, $row['password'])){
        echo json_encode([
            "status" => true,
            "message" => "Login success",
            "data" => [
                "id" => $row['id'],
                "name" => $row['name'],
                "username" => $row['username']
            ]
        ]);
    } else {
        echo json_encode(["status"=>false, "message"=>"Incorrect password"]);
    }
} else {
    echo json_encode(["status"=>false, "message"=>"User not found"]);
}
?>
