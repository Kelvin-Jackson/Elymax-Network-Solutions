<?php
// db_connect.php
require_once 'config.php';

try {
    $pdo = new PDO("pgsql:host=$host;dbname=$dbname", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    error_log("[" . date('Y-m-d H:i:s') . "] DB connection failed: " . $e->getMessage() . PHP_EOL, 3, "error_log");
    die(json_encode(["status" => "error", "message" => "Database connection failed."]));
}
?>
