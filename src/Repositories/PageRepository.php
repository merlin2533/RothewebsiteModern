<?php

declare(strict_types=1);

namespace App\Repositories;

final class PageRepository
{
    public function __construct(private \PDO $pdo) {}

    public function all(): array
    {
        return $this->pdo->query('SELECT * FROM pages ORDER BY sort_order, id')->fetchAll();
    }

    public function findBySlug(string $slug): ?array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM pages WHERE slug = ? LIMIT 1');
        $stmt->execute([$slug]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function find(int $id): ?array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM pages WHERE id = ? LIMIT 1');
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function save(array $data): int
    {
        $now = date('Y-m-d H:i:s');
        if (!empty($data['id'])) {
            $stmt = $this->pdo->prepare('
                UPDATE pages SET
                  slug = :slug, title = :title,
                  meta_title = :meta_title, meta_description = :meta_description,
                  og_image_id = :og_image_id, hero_headline = :hero_headline,
                  hero_sub = :hero_sub, hero_image_id = :hero_image_id,
                  content_html = :content_html, is_published = :is_published,
                  sort_order = :sort_order, updated_at = :updated_at
                WHERE id = :id
            ');
            $stmt->execute([
                ':id'               => (int) $data['id'],
                ':slug'             => $data['slug'],
                ':title'            => $data['title'],
                ':meta_title'       => $data['meta_title'] ?? null,
                ':meta_description' => $data['meta_description'] ?? null,
                ':og_image_id'      => $data['og_image_id'] ?? null,
                ':hero_headline'    => $data['hero_headline'] ?? null,
                ':hero_sub'         => $data['hero_sub'] ?? null,
                ':hero_image_id'    => $data['hero_image_id'] ?? null,
                ':content_html'     => $data['content_html'] ?? '',
                ':is_published'     => (int) ($data['is_published'] ?? 1),
                ':sort_order'       => (int) ($data['sort_order'] ?? 0),
                ':updated_at'       => $now,
            ]);
            return (int) $data['id'];
        }
        $stmt = $this->pdo->prepare('
            INSERT INTO pages
              (slug, title, meta_title, meta_description, og_image_id, hero_headline,
               hero_sub, hero_image_id, content_html, is_published, sort_order, updated_at)
            VALUES
              (:slug, :title, :meta_title, :meta_description, :og_image_id, :hero_headline,
               :hero_sub, :hero_image_id, :content_html, :is_published, :sort_order, :updated_at)
        ');
        $stmt->execute([
            ':slug'             => $data['slug'],
            ':title'            => $data['title'],
            ':meta_title'       => $data['meta_title'] ?? null,
            ':meta_description' => $data['meta_description'] ?? null,
            ':og_image_id'      => $data['og_image_id'] ?? null,
            ':hero_headline'    => $data['hero_headline'] ?? null,
            ':hero_sub'         => $data['hero_sub'] ?? null,
            ':hero_image_id'    => $data['hero_image_id'] ?? null,
            ':content_html'     => $data['content_html'] ?? '',
            ':is_published'     => (int) ($data['is_published'] ?? 1),
            ':sort_order'       => (int) ($data['sort_order'] ?? 0),
            ':updated_at'       => $now,
        ]);
        return (int) $this->pdo->lastInsertId();
    }

    public function delete(int $id): void
    {
        $this->pdo->prepare('DELETE FROM pages WHERE id = ?')->execute([$id]);
    }
}
