<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include "./connect.php";

class Ecommerce {
    public function getItem($tbname, $criteria=""){
        global $pdo;
        if (isset($_GET['id'])) {
            $id = $_GET['id'];
            if (empty($criteria)) {
                $criteria = "id = :id";
            }
            $stmt = $pdo->prepare("select * from $tbname where $criteria");
            $stmt->execute([':id' => $id]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($result) {
                echo json_encode($result);
            } else {
                echo json_encode(["message" => "Item not found"]);
            }
        } else {
            $stmt = $pdo->query("select * from $tbname");
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode($result);
        }
    }

    public function createItem($tbname, $data){
        global $pdo;
        $fields = implode(", ", array_keys($data));
        $placeholders = ":" . implode(", :", array_keys($data));
        $stmt = $pdo->prepare("insert into $tbname ($fields) values ($placeholders)");
        foreach ($data as $key => &$value) {
            $stmt->bindParam(":$key", $value);
        }
        if ($stmt->execute()) {
            echo json_encode(['message' => "Item created successfully"]);
        } else {
            echo json_encode(['message' => "Unable to create item"]);
        }
    }

    public function updateItem($tbname, $data, $id){
        global $pdo;
        $setClause = [];
        foreach ($data as $field => $value) {
            $setClause[] = "$field = :$field";
        }
        $setString = implode(", ", $setClause);
        $sql = "update $tbname set $setString where id = :id";
        $stmt = $pdo->prepare($sql);
        foreach ($data as $field => $value) {
            $stmt->bindValue(":$field", $value);
        }
        $stmt->bindValue(":id", $id);
        if($stmt->execute()) {
            echo json_encode(['message' => "Item updated successfully"]);
        } else {
            echo json_encode(['message' => "Unable to update item"]);
        }
    }

    public function deleteItem($tbname, $id){
        global $pdo;
        $stmt = $pdo->prepare("update $tbname set active = 0 where id = :id");
        if ($stmt->execute([':id' => $id])) {
            echo json_encode(['message' => "Item deleted successfully"]);
        } else {
            echo json_encode(['message' => "Unable to delete item"]);
        }
    }
}
?>