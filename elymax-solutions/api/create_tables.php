<?php
// create_tables.php
require_once 'db_connect.php';

try {
    // ✅ Create products table
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS products (
            id SERIAL PRIMARY KEY,
            name VARCHAR(255) NOT NULL,
            price DECIMAL(10,2) NOT NULL DEFAULT 0,
            image VARCHAR(500),
            category VARCHAR(100),
            stock INTEGER NOT NULL DEFAULT 0,
            sold INTEGER NOT NULL DEFAULT 0,
            daily_sales INTEGER DEFAULT 0,
            weekly_sales INTEGER DEFAULT 0,
            monthly_sales INTEGER DEFAULT 0,
            yearly_sales INTEGER DEFAULT 0,
            last_sale_date TIMESTAMP,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        );
    ");

    // ✅ Create sales table
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS sales (
            id SERIAL PRIMARY KEY,
            product_id INTEGER REFERENCES products(id) ON DELETE CASCADE,
            product_name VARCHAR(255) NOT NULL,
            product_price DECIMAL(10,2) NOT NULL,
            quantity INTEGER NOT NULL DEFAULT 1,
            total DECIMAL(10,2) NOT NULL,
            timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            date VARCHAR(50),
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        );
    ");

    // ✅ Create stock_deductions table
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS stock_deductions (
            id SERIAL PRIMARY KEY,
            product_id INTEGER REFERENCES products(id) ON DELETE CASCADE,
            product_name VARCHAR(255) NOT NULL,
            quantity INTEGER NOT NULL,
            reason VARCHAR(100),
            notes TEXT,
            username VARCHAR(100),
            timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            date VARCHAR(50),
            time VARCHAR(50),
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        );
    ");

    // ✅ Safely clear existing data before inserting (cascade to child tables)
    $pdo->exec("TRUNCATE TABLE products RESTART IDENTITY CASCADE;");

    // ✅ Sample products
    $sampleProducts = [
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
        ['1x4 Splitters', 250, 'images/1 x 4-splitter.jpg', 'accessories', 50, 5],
        ['Splicing Tray', 200, 'images/splicing-tray.jpeg', 'accessories', 50, 5],
        ['Unloaded ATB', 50, 'images/atb.jpeg', 'accessories', 100, 50],
        ['UPB', 250, 'images/fibre-upb.webp', 'accessories', 50, 5],
        ['Small Cable Ties', 100, 'images/Small-cable-ties.jpg', 'accessories', 10, 3],
        ['Large Cable Ties', 200, 'images/Large-cable-ties.jpeg', 'accessories', 20, 5],
        ['Loaded ATB', 120, 'images/loaded-atb.webp', 'accessories', 100, 3],
        ['1X2 Splitter', 200, 'images/1X2-splitters.webp', 'accessories', 60, 15],
        ['CAT5 LAN', 3400, 'images/Cat 5 LAN Cable.jpg', 'accessories', 10, 5],
        ['CAT6 LAN', 3800, 'images/Cat 6 LAN-cable.jpg', 'accessories', 10, 7],
        ['J-Hooks', 250, 'images/J-hooks.jpeg', 'accessories', 97, 15],
        ['Adapter Box (Large)', 550, 'images/Adapter-box.webp', 'accessories', 10, 5],
        ['Adapter Box (Medium)', 500, 'images/medium-adapter-box.webp', 'accessories', 20, 7],
        ['Adapter Box (Small)', 450, 'images/small-adapter-box.webp', 'accessories', 15, 3],
        ['Cable Clips', 100, 'images/Cable-Clips.jpg', 'accessories', 100, 25],
        ['RJ45', 450, 'images/RJ45.webp', 'accessories', 1000, 100],
        ['1x8 Splitter', 300, 'images/1x8-splitter.webp', 'accessories', 50, 25]
    ];

    // ✅ Insert all products in a single transaction
    $pdo->beginTransaction();

    $insertStmt = $pdo->prepare("
        INSERT INTO products (name, price, image, category, stock, sold)
        VALUES (:name, :price, :image, :category, :stock, :sold)
    ");

    foreach ($sampleProducts as $product) {
        $insertStmt->execute([
            ':name' => $product[0],
            ':price' => $product[1],
            ':image' => $product[2],
            ':category' => $product[3],
            ':stock' => $product[4],
            ':sold' => $product[5]
        ]);
    }

    $pdo->commit();

    echo "✅ Database tables recreated and ALL sample products inserted successfully!";
} catch (PDOException $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    error_log("[" . date('Y-m-d H:i:s') . "] create_tables.php: " . $e->getMessage() . PHP_EOL, 3, "error_log");
    echo "❌ Error creating tables: " . $e->getMessage();
}
?>
