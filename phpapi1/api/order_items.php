<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requeted-With");

include "../connect.php";

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        getOrder_Items();
        break;
    case 'POST':
        if(isset($_GET['id'])){
            updateOrder_Items();
        }else{
            createOrder_Items();
        }
        break;
    case 'PUT':
        deleteOrder_Items();
        break;
    default:
        echo json_encode(["message" => "Invalid request method"]);
        break;
}

function getOrder_Items(){
    global $pdo;
    if(isset($_GET['id'])){
        $id = $_GET['id'];
        $stmt = $pdo->prepare("select * from Order_Items where id = :id");
        $stmt->execute([':id' => $id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        if($result){
            echo json_encode($result);
        }else {
            echo json_encode(["message" => "OrderItems not found"]);
        }
    }else{
        $stmt = $pdo->query("select * from Order_Items");
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($result);
    }
}

function createOrder_Items(){
    global $pdo;
    $data = json_decode(file_get_contents('php://input'), true);

    // Debugging: Check incoming data
    if (json_last_error() !== JSON_ERROR_NONE) {
        echo json_encode(['message' => "Invalid JSON input"]);
        return;
    }

    // Prepare the insert statement
    $stmt = $pdo->prepare("INSERT INTO Order_Items (order_id, products_id, quantity, price, active) VALUES(:order_id, :products_id, :quantity, :price, :active) ");

    // Execute the statement
    try {
        if($stmt->execute([
            ":order_id" => $data['order_id'],
            ":products_id" => $data['product_id'],
            ":quantity" => $data['quantity'],
            ":price" => $data['price'],
            ":active" => 1 // Default to active status
            
        ])){
            echo json_encode(['message' => "OrderItems created successfully"]);
        } else {
            echo json_encode(['message' => "Unable to create OrderItems"]);
        }
    } catch (PDOException $e) {
        echo json_encode(['message' => "Database error: " . $e->getMessage()]);
    }
}





function updateOrder_Items(){
    global $pdo;
    $id = $_GET['id'];
    $data = json_decode(file_get_contents('php://input'), true);

    $stmt = $pdo->prepare("update Order_Items set order_id = :order_id where products_id = :products_id");
    if($stmt->execute([
        ":order_id" => $data['order_id'],
        ":products_id" => $data['products_id'],
    ])){
        echo json_encode(['message' => "OrderItems updated successfully"]);
    }else{
        echo json_encode(['message' => "Unable to update OrderItems"]);
    }
}

function deleteOrder_Items(){
    global $pdo;
    $id = $_GET['id'];

    $stmt = $pdo->prepare("update Order_Items set active = 0 where id = :id");
    if($stmt->execute([':id' => $id])){
        echo json_encode(['message' => "OrderItems deleted successfully"]);
    } else {
        echo json_encode(['message' => "Unable to delete OrderItems"]);
    }
}
?>