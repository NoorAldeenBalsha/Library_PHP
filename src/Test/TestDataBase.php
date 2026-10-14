<?php

declare(strict_types=1);


$config = require_once __DIR__ . '/../../config.example.php';

require_once __DIR__ . '/../autoload.php';

echo "=== Starting Tests ===\n\n";

try {
    // First check: Database connection
    echo "Database Connection Test:\n";
    $db = new Database($config);
    $pdo = $db->getConnection();
    echo "Success: Database connection established.\n";

} catch (Throwable $e) {
    echo "[Error] Test failed: " . $e->getMessage() . "\n";
}