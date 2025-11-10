<?php
require_once 'db_connect.php';

try {
    echo json_encode(["status" => "success", "message" => "Database connection OK"]);
} catch (PDOException $e) {
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
}
?>
