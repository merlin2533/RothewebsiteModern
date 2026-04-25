<?php

declare(strict_types=1);

namespace App\Core;

/**
 * Database – PDO Singleton + Migration Runner.
 *
 * Usage:
 *   $pdo = Database::getInstance('/absolute/path/to/database.sqlite');
 *   $applied = Database::runMigrations();
 */
final class Database
{
    private static ?\PDO $instance = null;
    private static string $dbPath = '';

    /** Returns the PDO singleton, initialising it on first call. */
    public static function getInstance(string $dbPath = ''): \PDO
    {
        if (self::$instance === null) {
            if ($dbPath === '') {
                throw new \RuntimeException('Database::getInstance() requires a $dbPath on first call.');
            }
            self::$dbPath = $dbPath;

            // Ensure parent directory exists
            $dir = dirname($dbPath);
            if (!is_dir($dir)) {
                mkdir($dir, 0750, true);
            }

            $pdo = new \PDO('sqlite:' . $dbPath);
            $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
            $pdo->setAttribute(\PDO::ATTR_DEFAULT_FETCH_MODE, \PDO::FETCH_ASSOC);

            // SQLite performance & integrity pragmas
            $pdo->exec('PRAGMA foreign_keys = ON');
            $pdo->exec('PRAGMA journal_mode = WAL');

            self::$instance = $pdo;
        }

        return self::$instance;
    }

    /**
     * Runs all not-yet-applied *.sql files from src/Migrations/ in sort order.
     * Creates the migrations tracking table if it doesn't exist.
     *
     * @return string[] List of migration filenames applied in this run.
     */
    public static function runMigrations(): array
    {
        $pdo = self::getInstance();

        // Create tracking table
        $pdo->exec('
            CREATE TABLE IF NOT EXISTS migrations (
                id         INTEGER PRIMARY KEY AUTOINCREMENT,
                name       TEXT    NOT NULL UNIQUE,
                applied_at TEXT    NOT NULL
            )
        ');

        // Collect already-applied migrations
        $applied = $pdo->query('SELECT name FROM migrations')->fetchAll(\PDO::FETCH_COLUMN);
        $applied = array_flip($applied); // fast lookup

        // Locate migration files
        $migrationsDir = dirname(__DIR__) . '/src/Migrations';
        $files = glob($migrationsDir . '/*.sql');
        if ($files === false || $files === []) {
            return [];
        }
        sort($files);

        $newlyApplied = [];
        foreach ($files as $file) {
            $name = basename($file);
            if (isset($applied[$name])) {
                continue; // already done
            }

            $sql = file_get_contents($file);
            if ($sql === false || trim($sql) === '') {
                continue;
            }

            $pdo->beginTransaction();
            try {
                $pdo->exec($sql);
                $stmt = $pdo->prepare('INSERT INTO migrations (name, applied_at) VALUES (?, ?)');
                $stmt->execute([$name, date('Y-m-d H:i:s')]);
                $pdo->commit();
                $newlyApplied[] = $name;
            } catch (\Throwable $e) {
                $pdo->rollBack();
                throw new \RuntimeException("Migration failed [{$name}]: " . $e->getMessage(), 0, $e);
            }
        }

        return $newlyApplied;
    }
}
