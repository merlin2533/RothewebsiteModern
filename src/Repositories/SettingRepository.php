<?php

declare(strict_types=1);

namespace App\Repositories;

final class SettingRepository
{
    /** @var array<string,string>|null */
    private ?array $cache = null;

    public function __construct(private \PDO $pdo) {}

    public function all(): array
    {
        if ($this->cache === null) {
            $rows = $this->pdo->query('SELECT key, value FROM settings')->fetchAll();
            $out = [];
            foreach ($rows as $r) {
                $out[$r['key']] = (string) ($r['value'] ?? '');
            }
            $this->cache = $out;
        }
        return $this->cache;
    }

    public function get(string $key, string $default = ''): string
    {
        $all = $this->all();
        return $all[$key] ?? $default;
    }

    public function set(string $key, string $value): void
    {
        $stmt = $this->pdo->prepare('
            INSERT INTO settings (key, value, updated_at) VALUES (:key, :value, :now)
            ON CONFLICT(key) DO UPDATE SET value = :value, updated_at = :now
        ');
        $stmt->execute([':key' => $key, ':value' => $value, ':now' => date('Y-m-d H:i:s')]);
        $this->cache = null;
    }

    public function setMany(array $kv): void
    {
        $this->pdo->beginTransaction();
        try {
            foreach ($kv as $k => $v) {
                $this->set((string) $k, (string) $v);
            }
            $this->pdo->commit();
        } catch (\Throwable $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }
}
