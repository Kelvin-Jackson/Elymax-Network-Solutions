<?php
// add_products.php
require_once 'db_connect.php'; // connects to PostgreSQL

try {
    // ✅ Optional: Clear existing products before inserting new ones
    $pdo->exec("TRUNCATE TABLE products RESTART IDENTITY CASCADE;");

    // ✅ Define all sample products
    $products = [
        ['Mercuseys Router', 1500, 'images/mercuseys-router.webp', 'wireless', 24, 12],
        ['Network Switches', 1400, 'images/Normal Switch tp-link.jpg', 'networking', 15, 8],
        ['Tenda Surveillance Cameras', 2800, 'images/tenda-cctv.webp', 'security', 20, 10],
        ['Tenda Router (Refurbished)', 1000, 'images/refurbished-tenda.webp', 'networking', 18, 7],
        ['Tenda Router (New)', 1500, 'images/tenda-router.webp', 'networking', 12, 9],
        ['Huawei Router', 1450, 'images/huawei-router.webp', 'networking', 10, 6],
        ['Drop Cable (1km)', 5200, 'images/drop-cable.webp', 'fibre-optic', 50, 25],
        ['Fibre Optic Connectors', 50, 'images/connectors.webp', 'fibre-optic', 0, 15],
        ['Fibre Accessory Termination Box(FAT) - 8 Core', 800, 'images/8-core-fat.webp', 'fibre-optic', 5, 3],
        ['Fibre Accessory Termination Box(FAT)- 16 Core', 1300, 'images/16-core-fat.webp', 'fibre-optic', 5, 3],
        ['Patch Cords - Thick', 110, 'images/thick-patch.webp', 'networking', 30, 10],
        ['Patch Cords - Thin', 100, 'images/thin-patch.webp', 'networking', 25, 5],
        ['Tensions PA 1500', 300, 'images/1500-pa.webp', 'accessories', 45, 20],
        ['Tension PA 3000', 350, 'images/3000-pa.webp', 'accessories', 50, 25],
        ['1x4 Splitters', 250, 'images/1x4-splitter.jpg', 'accessories', 50, 5],
        ['Splicing Tray', 200, 'images/splicing-tray.jpeg', 'accessories', 50, 5],
        ['Unloaded ATB', 50, 'images/atb.jpeg', 'accessories', 100, 50],
        ['UPB', 250, 'images/fibre-upb.webp', 'accessories', 50, 5],
        ['Small Cable Ties', 100, 'images/Small-cable-ties.jpg', 'accessories', 10, 3],
        ['Large Cable Ties', 200, 'images/Large-cable-ties.jpg', 'accessories', 20, 5],
        ['Loaded ATB', 120, 'images/loaded-atb.webp', 'accessories', 100, 3],
        ['1X2', 200, 'images/1x2-splitter.webp', 'accessories', 60, 15],
        ['CAT5 LAN', 3400, 'images/Cat 5 LAN Cable.jpg', 'accessories', 10, 5],
        ['CAT6 LAN', 3800, 'images/Cat 6 LAN-cable.jpg', 'accessories', 10, 7],
        ['J-Hooks', 250, 'images/J-hooks.jpeg', 'accessories', 97, 15],
        ['Adapter Box (Large)', 550, 'images/Adapter-box.webp', 'accessories', 10, 5],
        ['Adapter Box (Medium)', 500, 'images/medium-adapter-box.webp', 'accessories', 20, 7],
        ['Adapter Box (Small)', 450, 'images/small-adapter-box.webp', 'accessories', 15, 3],
        ['Cable Clips', 100, 'images/Cable-Clips.webp', 'accessories', 100, 25],
        ['RJ45', 450, 'images/RJ45.webp', 'accessories', 1000, 100],
        ['1x8 Splitter', 300, 'images/1x8-splitter.webp', 'accessories', 50, 25]
    ];

    // ✅ Prepare insert statement
    $stmt = $pdo->prepare("
        INSERT INTO products (name, price, image, category, stock, sold)
        VALUES (:name, :price, :image, :category, :stock, :sold)
    ");

    // ✅ Insert all products
    foreach ($products as $p) {
        $stmt->execute([
            ':name' => $p[0],
            ':price' => $p[1],
            ':image' => $p[2],
            ':category' => $p[3],
            ':stock' => $p[4],
            ':sold' => $p[5]
        ]);
    }

    echo "✅ All products inserted successfully into PostgreSQL!";

} catch (PDOException $e) {
    error_log("[" . date('Y-m-d H:i:s') . "] add_products.php: " . $e->getMessage() . PHP_EOL, 3, "error_log");
    echo "❌ Error inserting products: " . $e->getMessage();
}
?>
