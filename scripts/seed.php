<?php

declare(strict_types=1);

/**
 * CLI: php scripts/seed.php
 * Seeding currently happens via migration 002_seed_initial_content.sql.
 * This script remains as a placeholder for future ad-hoc seeders.
 */
require __DIR__ . '/../src/bootstrap.php';

echo "No additional seeding implemented – initial content is loaded by migrations.\n";
echo "Run `php scripts/migrate.php` to apply.\n";
