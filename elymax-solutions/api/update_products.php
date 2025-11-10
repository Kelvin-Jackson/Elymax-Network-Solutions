<?php
// update_products.php
require_once 'db_connect.php'; // connects to PostgreSQL

try {
    // ✅ Define products with updated info (can be same as original)
    $products = [
        ['Mercuseys Router', 1500, 'images/mercuseys-router.webp', 'wireless', 24, 12],
        ['Network Switches', 1400, 'images/Normal Switch tp-link.jpg', 'networking', 15, 8],
        ['Tenda Surveillance Cameras', 2800, 'images/tenda-cctv.webp', 'security', 20, 10],
        ['Tenda Router (Refurbished)', 1000, 'images/refurbished-tenda.webp', 'networking', 18, 7],
        ['Tenda Router (New)', 1500, 'images/tenda-router.webp', 'networking', 12, 9],
        ['Huawei Router', 1450, 'images/huawei-router.webp', 'networking', 10, 6],
        ['Drop Cable (1km)', 5200, 'images/drop-cable.webp', 'fibre-optic', 50, 25],
        ['Fibre Optic Connectors', 50, 'images/connectors.webp', 'fibre-optic', 0, 15],
        // ... continue for all products
    ];

    // ✅ Prepare statements
    $checkStmt = $pdo->prepare("SELECT id FROM products WHERE name = :name");
    $updateStmt = $pdo->prepare("
        UPDATE products
        SET price = :price,
            image = :image,
            category = :category,
            stock = :stock,
            sold = :sold
        WHERE id = :id
    ");
    $insertStmt = $pdo->prepare("
        INSERT INTO products (name, price, image, category, stock, sold)
        VALUES (:name, :price, :image, :category, :stock, :sold)
    ");

    $updatedCount = 0;
    $insertedCount = 0;

    foreach ($products as $p) {
        $checkStmt->execute([':name' => $p[0]]);
        $row = $checkStmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            // ✅ Update existing product
            $updateStmt->execute([
                ':price' => $p[1],
                ':image' => $p[2],
                ':category' => $p[3],
                ':stock' => $p[4],
                ':sold' => $p[5],
                ':id' => $row['id']
            ]);
            $updatedCount++;
        } else {
            // ✅ Insert new product
            $insertStmt->execute([
                ':name' => $p[0],
                ':price' => $p[1],
                ':image' => $p[2],
                ':category' => $p[3],
                ':stock' => $p[4],
                ':sold' => $p[5]
            ]);
            $insertedCount++;
        }
    }

    echo "✅ Products updated: $updatedCount, Products inserted: $insertedCount";

} catch (PDOException $e) {
    error_log("[" . date('Y-m-d H:i:s') . "] update_products.php: " . $e->getMessage() . PHP_EOL, 3, "error_log");
    echo "❌ Error updating/inserting products: " . $e->getMessage();
}
?>
