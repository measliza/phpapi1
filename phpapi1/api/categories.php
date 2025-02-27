<?php
include "../connect.php";
include "../lib.php";

$ecommerce = new Ecommerce();

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        $ecommerce->getItem("Categories", "");
        break;
    case 'POST':
        if(isset($_GET['id'])){
            $id = $_GET['id'];
            $data = json_decode(file_get_contents('php://input'), true);
            $ecommerce->updateItem("Categories", $data, $id);
        }else{
            $data = json_decode(file_get_contents('php://input'), true);
            $ecommerce->createItem("Categories", $data);
        }
        break;
    case 'PUT':
            $id = $_GET['id'];
            $data = json_decode(file_get_contents('php://input'), true);
            $ecommerce->deleteItem("Categories", $id);
        break;
    default:
            echo json_encode(["message" => "Invalid request method"]);
        break;
}
?>