<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requeted-With");

include "../connect.php";

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        getCarts();
        break;
    case 'POST':
        if(isset($_GET['id'])){
            // updateCarts();
        }else{
            createCarts();
        }
        break;
    case 'PUT':
        // deleteCarts();
        break;
    default:
        echo json_encode(["message" => "Invalid request method"]);
        break;
}

function getCarts(){
    global $pdo;
    if(isset($_GET['id'])){
        $id = $_GET['id'];
        $stmt = $pdo->prepare("select * from Carts where id = :id");
        $stmt->execute([':id' => $id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        if($result){
            echo json_encode($result);
        }else {
            echo json_encode(["message" => "Carts not found"]);
        }
    }else{
        $stmt = $pdo->query("select * from Carts");
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($result);
    }
}

function createCarts(){
    global $pdo;
    $data = json_decode(file_get_contents('php://input'), true);

    // Debugging: Check incoming data
    if (json_last_error() !== JSON_ERROR_NONE) {
        echo json_encode(['message' => "Invalid JSON input"]);
        return;
    }

    // Prepare the insert statement
    $stmt = $pdo->prepare("INSERT INTO Payments (user_id, products_id, quantity, active) VALUES(:user_id, :products_id, :quantity, :active) ");

    // Execute the statement
    try {
        if($stmt->execute([
            ":user_id" => $data['user_id'],
            ":products_id" => $data['products_id'],
            ":quantity" => $data['quantity'],
            ":active" => 1 // Default to active status
                    
        ])){
            echo json_encode(['message' => "Carts created successfully"]);
        } else {
            echo json_encode(['message' => "Unable to create Carts"]);
        }
    } catch (PDOException $e) {
        echo json_encode(['message' => "Database error: " . $e->getMessage()]);
    }
}





function updateCarts() {
    global $pdo;
    $id = $_GET['id'];
    $data = json_decode(file_get_contents('php://input'), true);

    // Corrected SQL query
    $stmt = $pdo->prepare("UPDATE Carts SET order_id = :order_id, amount = :amount WHERE user_id = :user_id");

    // Execute the statement
    if ($stmt->execute([
        ":order_id" => $data['order_id'],
        ":amount" => $data['amount'],
        ":user_id" => $id  // The ID to update
    ])) {
        echo json_encode(['message' => "Carts updated successfully"]);
    } else {
        echo json_encode(['message' => "Unable to update Carts"]);
    }
}


function deleteCarts(){
    global $pdo;
    $id = $_GET['id'];

    $stmt = $pdo->prepare("update Payments set active = 0 where id = :id");
    if($stmt->execute([':id' => $id])){
        echo json_encode(['message' => "Carts deleted successfully"]);
    } else {
        echo json_encode(['message' => "Unable to delete Carts"]);
    }
}
?>