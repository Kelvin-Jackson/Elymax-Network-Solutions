<?php
// stock_deductions.php
header('Content-Type: application/json');
require_once 'db_connect.php';

$product_id = $_POST['product_id'] ?? 0;
$quantity = $_POST['quantity'] ?? 1;

try {
    $pdo->beginTransaction();

    // Get current stock
    $stmt = $pdo->prepare("SELECT stock FROM products WHERE id = :id FOR UPDATE");
    $stmt->execute([':id' => $product_id]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$product) {
        throw new Exception("Product not found.");
    }

    if ($product['stock'] < $quantity) {
        throw new Exception("Not enough stock available.");
    }

    // Deduct stock
    $new_stock = $product['stock'] - $quantity;
    $update = $pdo->prepare("UPDATE products SET stock = :new_stock WHERE id = :id");
    $update->execute([':new_stock' => $new_stock, ':id' => $product_id]);

    $pdo->commit();

    echo json_encode(["status" => "success", "message" => "Stock updated successfully."]);
} catch (Exception $e) {
    $pdo->rollBack();
    error_log("[" . date('Y-m-d H:i:s') . "] stock_deductions.php: " . $e->getMessage() . PHP_EOL, 3, "error_log");
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
}
?>
