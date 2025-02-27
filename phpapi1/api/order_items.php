<?php
include "../connect.php";
include "../lib.php";

$ecommerce = new Ecommerce();


$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
            $ecommerce->getItem("Order_items", "");
            break;
    case 'POST':
        if(isset($_GET['id'])){
            $id = $_GET['id'];
            $data = json_decode(file_get_contents('php://input'), true);
            $ecommerce->updateItem("Order_items", $data, $id);
        }else{
            $data = json_decode(file_get_contents('php://input'), true);
            $ecommerce->createItem("Order_items", $data);
        }
        break;
    case 'PUT':
            $id = $_GET['id'];
            $data = json_decode(file_get_contents('php://input'), true);
            $ecommerce->deleteItem("Order_items", $id);
            break;
    default:
        echo json_encode(["message" => "Invalid request method"]);
        break;
}
?>