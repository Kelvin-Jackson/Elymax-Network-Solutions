<?php
// fetch_products.php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

require_once 'db_connect.php';

try {
    // ✅ Use your actual column names from the "products" table
    $stmt = $pdo->prepare("
        SELECT id, name, price, image, category, stock, sold
        FROM products
        WHERE stock > 0
        ORDER BY id DESC
    ");
    
    $stmt->execute();
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (!$products) {
        echo json_encode([
            "status" => "error",
            "message" => "No products found in stock."
        ]);
        exit;
    }

    echo json_encode([
        "status" => "success",
        "products" => $products
    ]);

} catch (PDOException $e) {
    error_log("[" . date('Y-m-d H:i:s') . "] fetch_products.php: " . $e->getMessage() . PHP_EOL, 3, "error_log");
    echo json_encode([
        "status" => "error",
        "message" => "Failed to fetch products: " . $e->getMessage()
    ]);
}
?>
