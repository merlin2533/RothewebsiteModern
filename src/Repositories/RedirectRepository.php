<?php

declare(strict_types=1);

namespace App\Repositories;

final class RedirectRepository
{
    public function __construct(private \PDO $pdo) {}

    public function all(): array
    {
        return $this->pdo->query('SELECT * FROM redirects ORDER BY from_path')->fetchAll();
    }

    public function find(int $id): ?array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM redirects WHERE id = ? LIMIT 1');
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    /**
     * Returns the redirect target if a matching ACTIVE rule exists for $path
     * (path only – query string is preserved by the caller).
     */
    public function lookupActive(string $path): ?array
    {
        $stmt = $this->pdo->prepare(
            'SELECT id, to_path, status_code FROM redirects
             WHERE is_active = 1 AND from_path = ? LIMIT 1'
        );
        $stmt->execute([$path]);
        return $stmt->fetch() ?: null;
    }

    public function recordHit(int $id): void
    {
        try {
            $this->pdo->prepare(
                'UPDATE redirects SET hits = hits + 1,
                 last_hit_at = strftime("%Y-%m-%d %H:%M:%S", "now") WHERE id = ?'
            )->execute([$id]);
        } catch (\Throwable) {
            // never block a redirect on logging
        }
    }

    public function save(array $data): int
    {
        $now = date('Y-m-d H:i:s');
        $params = [
            ':from'   => '/' . ltrim((string) $data['from_path'], '/'),
            ':to'     => '/' . ltrim((string) $data['to_path'], '/'),
            ':status' => (int) ($data['status_code'] ?? 301),
            ':active' => (int) ($data['is_active']   ?? 1),
            ':now'    => $now,
        ];
        if (!empty($data['id'])) {
            $params[':id'] = (int) $data['id'];
            $this->pdo->prepare(
                'UPDATE redirects SET from_path=:from, to_path=:to, status_code=:status,
                 is_active=:active, updated_at=:now WHERE id=:id'
            )->execute($params);
            return (int) $data['id'];
        }
        $this->pdo->prepare(
            'INSERT INTO redirects (from_path, to_path, status_code, is_active, updated_at)
             VALUES (:from, :to, :status, :active, :now)'
        )->execute($params);
        return (int) $this->pdo->lastInsertId();
    }

    public function delete(int $id): void
    {
        $this->pdo->prepare('DELETE FROM redirects WHERE id = ?')->execute([$id]);
    }
}
