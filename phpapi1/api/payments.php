<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requeted-With");

include "../connect.php";

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        getPayments();
        break;
    case 'POST':
        if(isset($_GET['id'])){
            updatePayments();
        }else{
            createPayments();
        }
        break;
    case 'PUT':
        deletePayments();
        break;
    default:
        echo json_encode(["message" => "Invalid request method"]);
        break;
}

function getPayments(){
    global $pdo;
    if(isset($_GET['id'])){
        $id = $_GET['id'];
        $stmt = $pdo->prepare("select * from Payments where id = :id");
        $stmt->execute([':id' => $id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        if($result){
            echo json_encode($result);
        }else {
            echo json_encode(["message" => "Paymentss not found"]);
        }
    }else{
        $stmt = $pdo->query("select * from Payments");
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($result);
    }
}

function createPayments(){
    global $pdo;
    $data = json_decode(file_get_contents('php://input'), true);

    // Debugging: Check incoming data
    if (json_last_error() !== JSON_ERROR_NONE) {
        echo json_encode(['message' => "Invalid JSON input"]);
        return;
    }

    // Prepare the insert statement
    $stmt = $pdo->prepare("INSERT INTO Payments (order_id, user_id, amount, active) VALUES(:order_id, :user_id, :amount, :active) ");

    // Execute the statement
    try {
        if($stmt->execute([
            ":order_id" => $data['order_id'],
            ":user_id" => $data['user_id'],
            ":amount" => $data['amount'],
            ":active" => 1 // Default to active status         
        ])){
            echo json_encode(['message' => "Payments created successfully"]);
        } else {
            echo json_encode(['message' => "Unable to create Payments"]);
        }
    } catch (PDOException $e) {
        echo json_encode(['message' => "Database error: " . $e->getMessage()]);
    }
}





function updatePayments() {
    global $pdo;
    $id = $_GET['id'];
    $data = json_decode(file_get_contents('php://input'), true);

    // Corrected SQL query
    $stmt = $pdo->prepare("UPDATE Payments SET order_id = :order_id, amount = :amount WHERE user_id = :user_id");

    // Execute the statement
    if ($stmt->execute([
        ":order_id" => $data['order_id'],
        ":amount" => $data['amount'],
        ":user_id" => $id  // The ID to update
    ])) {
        echo json_encode(['message' => "Payments updated successfully"]);
    } else {
        echo json_encode(['message' => "Unable to update Payments"]);
    }
}


function deletePayments(){
    global $pdo;
    $id = $_GET['id'];

    $stmt = $pdo->prepare("update Payments set active = 0 where id = :id");
    if($stmt->execute([':id' => $id])){
        echo json_encode(['message' => "Payments deleted successfully"]);
    } else {
        echo json_encode(['message' => "Unable to delete Payments"]);
    }
}
?>