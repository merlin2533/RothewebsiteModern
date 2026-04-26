<?php

declare(strict_types=1);

namespace App\Repositories;

final class FaqRepository
{
    public function __construct(private \PDO $pdo) {}

    public function all(): array
    {
        return $this->pdo->query('SELECT * FROM faqs ORDER BY page_slug, sort_order, id')->fetchAll();
    }

    public function activeForPage(string $pageSlug): array
    {
        $stmt = $this->pdo->prepare(
            'SELECT * FROM faqs WHERE is_active = 1 AND page_slug = ? ORDER BY sort_order, id'
        );
        $stmt->execute([$pageSlug]);
        return $stmt->fetchAll();
    }

    public function find(int $id): ?array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM faqs WHERE id = ? LIMIT 1');
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    public function save(array $data): int
    {
        $now = date('Y-m-d H:i:s');
        $params = [
            ':question'    => (string) $data['question'],
            ':answer_html' => (string) $data['answer_html'],
            ':page_slug'   => (string) ($data['page_slug'] ?? 'home'),
            ':is_active'   => (int) ($data['is_active'] ?? 1),
            ':sort_order'  => (int) ($data['sort_order'] ?? 0),
            ':updated_at'  => $now,
        ];
        if (!empty($data['id'])) {
            $params[':id'] = (int) $data['id'];
            $sql = 'UPDATE faqs SET question=:question, answer_html=:answer_html,
                page_slug=:page_slug, is_active=:is_active, sort_order=:sort_order,
                updated_at=:updated_at WHERE id=:id';
            $this->pdo->prepare($sql)->execute($params);
            return (int) $data['id'];
        }
        $sql = 'INSERT INTO faqs (question, answer_html, page_slug, is_active, sort_order, updated_at)
            VALUES (:question, :answer_html, :page_slug, :is_active, :sort_order, :updated_at)';
        $this->pdo->prepare($sql)->execute($params);
        return (int) $this->pdo->lastInsertId();
    }

    public function delete(int $id): void
    {
        $this->pdo->prepare('DELETE FROM faqs WHERE id = ?')->execute([$id]);
    }
}
