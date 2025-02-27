<?php
include "../connect.php";
include "../lib.php";

$ecommerce = new Ecommerce();

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        $ecommerce->getItem(tbname: "Carts", criteria:"");
        break;
    case 'POST':
        if(isset($_GET['id'])){
            $id = $_GET['id'];
            $data = json_decode(json: file_get_contents(filename: 'php://input'), associative:true);
            $ecommerce->updateItem("Categories", $data, $id);
        }else{
            $data = json_decode(file_get_contents('php://input'), true);
            $ecommerce->createItem("Carts", $data);
        }
        break;
    case 'PUT':
            $id = $_GET['id'];
            $data = json_decode(file_get_contents('php://input'), true);
            $ecommerce->deleteItem("carts", $id);
            break;
       
    default:
        echo json_encode(["message" => "Invalid request method"]);
        break;
}
?>