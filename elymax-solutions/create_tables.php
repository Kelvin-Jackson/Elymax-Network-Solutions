<?php
require_once 'config.php';

$db = new Database();
$pdo = $db->getConnection();

try {
    // Create products table
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
        )
    ");
    
    // Create sales table
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS sales (
            id SERIAL PRIMARY KEY,
            product_id INTEGER,
            product_name VARCHAR(255) NOT NULL,
            product_price DECIMAL(10,2) NOT NULL,
            quantity INTEGER NOT NULL DEFAULT 1,
            total DECIMAL(10,2) NOT NULL,
            timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            date VARCHAR(50),
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )
    ");
    
    // Create stock_deductions table
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS stock_deductions (
            id SERIAL PRIMARY KEY,
            product_id INTEGER,
            product_name VARCHAR(255) NOT NULL,
            quantity INTEGER NOT NULL,
            reason VARCHAR(100),
            notes TEXT,
            username VARCHAR(100),
            timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            date VARCHAR(50),
            time VARCHAR(50),
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )
    ");
    
    // Insert sample products
    $sampleProducts = [
        ['Mercuseys Router', 1500, 'images/mercuseys-router.webp', 'wireless', 24, 12],
        ['Network Switches', 1000, 'images/network-switches.webp', 'networking', 15, 8],
        ['Tenda Surveillance Cameras', 2800, 'images/tenda-cctv.webp', 'security', 20, 10],
        ['Tenda Router (Refurbished)', 1000, 'images/refurbished-tenda.webp', 'networking', 18, 7],
        ['Tenda Router (New)', 1500, 'images/tenda-router.webp', 'networking', 12, 9],
        ['Huawei Router', 1450, 'images/huawei-router.webp', 'networking', 10, 6],
        ['Drop Cable (1km)', 5000, 'images/drop-cable.webp', 'fibre-optic', 50, 25],
        ['Fibre Optic Connectors', 50, 'images/connectors.webp', 'fibre-optic', 0, 15],
        ['Fibre Accessory Termination Box(FAT) - 8 Core', 800, 'images/8-core-fat.webp', 'fibre-optic', 5, 3],
        ['Fibre Accessory Termination Box(FAT)- 16 Core', 1300, 'images/16-core-fat.webp', 'fibre-optic', 5, 3],
        ['Patch Cords - Thick', 110, 'images/thick-patch.webp', 'networking', 30, 10],
        ['Patch Cords - Thin', 100, 'images/thin-patch.webp', 'networking', 25, 5],
        ['Tensions PA 1500', 300, 'images/1500-pa.webp', 'accessories', 45, 20],
        ['Tension PA 3000', 350, 'images/3000-pa.webp', 'accessories', 50, 25],
        ['Splitters', 250, 'images/splitters.webp', 'accessories', 50, 5],
        ['Splicing Tray', 200, 'images/splicing-tray.jpeg', 'accessories', 50, 5],
        ['ATB', 50, 'images/atb.jpeg', 'accessories', 50, 5],
        ['UPB', 250, 'images/fibre-upb.webp', 'accessories', 50, 5],
        ['Cable Ties', 200, 'images/cable-ties.jpeg', 'accessories', 50, 5],
        ['J-Hooks', 250, 'images/J-hooks.jpeg', 'accessories', 60, 15]
    ];
    
    $stmt = $pdo->prepare("INSERT INTO products (name, price, image, category, stock, sold) VALUES (?, ?, ?, ?, ?, ?)");
    
    foreach ($sampleProducts as $product) {
        // Check if product already exists
        $checkStmt = $pdo->prepare("SELECT id FROM products WHERE name = ?");
        $checkStmt->execute([$product[0]]);
        
        if (!$checkStmt->fetch()) {
            $stmt->execute($product);
        }
    }
    
    echo "Database tables created successfully and sample data inserted!";
    
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>