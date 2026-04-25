<?php

declare(strict_types=1);

/**
 * CLI: php scripts/migrate.php
 * Runs all pending migrations and prints a summary.
 */
require __DIR__ . '/../src/bootstrap.php';

use App\Core\Database;

echo "Running migrations…\n";
$applied = Database::runMigrations();

if (empty($applied)) {
    echo "  (no new migrations – schema already up to date)\n";
} else {
    foreach ($applied as $name) {
        echo "  ✓ {$name}\n";
    }
}

$pdo = Database::getInstance();
$tables = $pdo->query("SELECT name FROM sqlite_master WHERE type='table' AND name NOT LIKE 'sqlite_%' ORDER BY name")
    ->fetchAll(PDO::FETCH_COLUMN);

echo "\nTables in database:\n";
foreach ($tables as $t) {
    $count = (int) $pdo->query("SELECT COUNT(*) FROM {$t}")->fetchColumn();
    printf("  %-20s %4d rows\n", $t, $count);
}
echo "\nDone.\n";
