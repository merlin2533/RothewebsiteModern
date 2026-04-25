<?php

declare(strict_types=1);

namespace App\Repositories;

final class ServiceRepository
{
    public function __construct(private \PDO $pdo) {}

    public function all(): array
    {
        return $this->pdo->query('SELECT * FROM services ORDER BY sort_order, id')->fetchAll();
    }

    public function allActive(): array
    {
        return $this->pdo->query('SELECT * FROM services WHERE is_active = 1 ORDER BY sort_order, id')->fetchAll();
    }

    public function findBySlug(string $slug): ?array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM services WHERE slug = ? LIMIT 1');
        $stmt->execute([$slug]);
        return $stmt->fetch() ?: null;
    }

    public function find(int $id): ?array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM services WHERE id = ? LIMIT 1');
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    public function save(array $data): int
    {
        $now = date('Y-m-d H:i:s');
        $params = [
            ':slug'       => $data['slug'],
            ':title'      => $data['title'],
            ':summary'    => $data['summary'] ?? null,
            ':body_html'  => $data['body_html'] ?? null,
            ':icon_key'   => $data['icon_key'] ?? null,
            ':is_active'  => (int) ($data['is_active'] ?? 1),
            ':sort_order' => (int) ($data['sort_order'] ?? 0),
            ':updated_at' => $now,
        ];
        if (!empty($data['id'])) {
            $params[':id'] = (int) $data['id'];
            $sql = 'UPDATE services SET slug=:slug, title=:title, summary=:summary,
                body_html=:body_html, icon_key=:icon_key, is_active=:is_active,
                sort_order=:sort_order, updated_at=:updated_at WHERE id=:id';
            $this->pdo->prepare($sql)->execute($params);
            return (int) $data['id'];
        }
        $sql = 'INSERT INTO services (slug, title, summary, body_html, icon_key, is_active, sort_order, updated_at)
            VALUES (:slug, :title, :summary, :body_html, :icon_key, :is_active, :sort_order, :updated_at)';
        $this->pdo->prepare($sql)->execute($params);
        return (int) $this->pdo->lastInsertId();
    }

    public function delete(int $id): void
    {
        $this->pdo->prepare('DELETE FROM services WHERE id = ?')->execute([$id]);
    }
}
