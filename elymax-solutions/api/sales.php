<?php
// sales.php
header('Content-Type: application/json');
require_once 'db_connect.php';

$product_id = $_POST['product_id'] ?? 0;
$quantity = $_POST['quantity'] ?? 1;

try {
    // Get product price
    $stmt = $pdo->prepare("SELECT price FROM products WHERE id = :id");
    $stmt->execute([':id' => $product_id]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$product) {
        echo json_encode(["status" => "error", "message" => "Product not found."]);
        exit;
    }

    $total_price = $product['price'] * $quantity;

    // Insert into sales table
    $insert = $pdo->prepare("
        INSERT INTO sales (product_id, quantity, total_price)
        VALUES (:product_id, :quantity, :total_price)
    ");
    $insert->execute([
        ':product_id' => $product_id,
        ':quantity' => $quantity,
        ':total_price' => $total_price
    ]);

    echo json_encode(["status" => "success", "message" => "Sale recorded successfully."]);

} catch (PDOException $e) {
    error_log("[" . date('Y-m-d H:i:s') . "] sales.php: " . $e->getMessage() . PHP_EOL, 3, "error_log");
    echo json_encode(["status" => "error", "message" => "Error recording sale."]);
}
?>
