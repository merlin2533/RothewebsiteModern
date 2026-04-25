<?php

declare(strict_types=1);

namespace App\Repositories;

final class TimelineRepository
{
    public function __construct(private \PDO $pdo) {}

    public function all(): array
    {
        return $this->pdo->query('SELECT * FROM timeline_events ORDER BY year, sort_order')->fetchAll();
    }

    public function allActive(): array
    {
        return $this->pdo->query('SELECT * FROM timeline_events WHERE is_active = 1 ORDER BY year, sort_order')->fetchAll();
    }

    public function find(int $id): ?array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM timeline_events WHERE id = ? LIMIT 1');
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    public function save(array $data): int
    {
        $now = date('Y-m-d H:i:s');
        $params = [
            ':year'        => (int) $data['year'],
            ':title'       => $data['title'],
            ':description' => $data['description'] ?? null,
            ':is_active'   => (int) ($data['is_active'] ?? 1),
            ':sort_order'  => (int) ($data['sort_order'] ?? 0),
            ':updated_at'  => $now,
        ];
        if (!empty($data['id'])) {
            $params[':id'] = (int) $data['id'];
            $sql = 'UPDATE timeline_events SET year=:year, title=:title, description=:description,
                is_active=:is_active, sort_order=:sort_order, updated_at=:updated_at WHERE id=:id';
            $this->pdo->prepare($sql)->execute($params);
            return (int) $data['id'];
        }
        $sql = 'INSERT INTO timeline_events (year, title, description, is_active, sort_order, updated_at)
            VALUES (:year, :title, :description, :is_active, :sort_order, :updated_at)';
        $this->pdo->prepare($sql)->execute($params);
        return (int) $this->pdo->lastInsertId();
    }

    public function delete(int $id): void
    {
        $this->pdo->prepare('DELETE FROM timeline_events WHERE id = ?')->execute([$id]);
    }
}
