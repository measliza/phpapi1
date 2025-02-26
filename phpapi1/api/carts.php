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
            updateCarts();
        }else{
            createCarts();
        }
        break;
    case 'PUT':
        deleteCarts();
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

    // Debugging: Log incoming data
    file_put_contents('debug.log', print_r($data, true));

    if (json_last_error() !== JSON_ERROR_NONE) {
        echo json_encode(['message' => "Invalid JSON input"]);
        return;
    }

    if (!isset($data['user_id'], $data['products_id'], $data['quantity']) || !is_numeric($data['products_id']) || !is_numeric($data['user_id'])) {
        echo json_encode(['message' => "Error: Missing or invalid fields"]);
        return;
    }

    // Check if the product exists
    $checkProductStmt = $pdo->prepare("SELECT COUNT(*) FROM products WHERE id = :products_id");
    $checkProductStmt->execute([":products_id" => $data['products_id']]);
    $productExists = $checkProductStmt->fetchColumn();

    if (!$productExists) {
        echo json_encode(['message' => "Error: Product ID does not exist"]);
        return;
    }

    // Insert into carts
    $stmt = $pdo->prepare("INSERT INTO carts (user_id, products_id, quantity, active) VALUES(:user_id, :products_id, :quantity, :active)");
    
    try {
        $pdo->beginTransaction();
        if ($stmt->execute([
            ":user_id" => $data['user_id'],
            ":products_id" => $data['products_id'],
            ":quantity" => $data['quantity'],
            ":active" => 1
        ])) {
            $pdo->commit();
            echo json_encode(['message' => "Cart created successfully"]);
        } else {
            $pdo->rollBack();
            echo json_encode(['message' => "Unable to create Cart"]);
        }
    } catch (PDOException $e) {
        $pdo->rollBack();
        echo json_encode(['message' => "Database error: " . $e->getMessage()]);
    }
}
 




function updateCarts() {
    global $pdo;
    $id = $_GET['id'];
    $data = json_decode(file_get_contents('php://input'), true);

    // Corrected SQL query
    $stmt = $pdo->prepare("UPDATE Carts SET products_id = :products_id, quantity = :quantity WHERE user_id = :user_id");

    // Execute the statement
    if ($stmt->execute([
        ":products_id" => $data['products_id'],
        ":quantity" => $data['quantity'],
        ":user_id" => $id  // The ID to update by
        
    ])) {
        echo json_encode(['message' => "Carts updated successfully"]);
    } else {
        echo json_encode(['message' => "Unable to update Carts"]);
    }
}


function deleteCarts(){
    global $pdo;
    $id = $_GET['id'];

    $stmt = $pdo->prepare("update Carts set active = 0 where id = :id");
    if($stmt->execute([':id' => $id])){
        echo json_encode(['message' => "Carts deleted successfully"]);
    } else {
        echo json_encode(['message' => "Unable to delete Carts"]);
    }
}
?>