<?php

declare(strict_types=1);

namespace App\Repositories;

final class LandingPageRepository
{
    public function __construct(private \PDO $pdo) {}

    public function all(): array
    {
        return $this->pdo->query('SELECT * FROM landing_pages ORDER BY kind, sort_order, id')->fetchAll();
    }

    public function allPublished(): array
    {
        return $this->pdo->query(
            'SELECT * FROM landing_pages WHERE is_published = 1 ORDER BY kind, sort_order, id'
        )->fetchAll();
    }

    public function findBySlug(string $slug): ?array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM landing_pages WHERE slug = ? LIMIT 1');
        $stmt->execute([$slug]);
        return $stmt->fetch() ?: null;
    }

    public function find(int $id): ?array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM landing_pages WHERE id = ? LIMIT 1');
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    public function save(array $data): int
    {
        $now = date('Y-m-d H:i:s');
        $params = [
            ':slug'             => (string) $data['slug'],
            ':kind'             => (string) ($data['kind'] ?? 'industry'),
            ':title'            => (string) $data['title'],
            ':eyebrow'          => $data['eyebrow']          ?? null,
            ':headline'         => $data['headline']         ?? null,
            ':sub'              => $data['sub']              ?? null,
            ':intro_html'       => $data['intro_html']       ?? null,
            ':body_html'        => $data['body_html']        ?? null,
            ':meta_title'       => $data['meta_title']       ?? null,
            ':meta_description' => $data['meta_description'] ?? null,
            ':cta_label'        => (string) ($data['cta_label'] ?: 'Jetzt anfragen'),
            ':primary_phone'    => $data['primary_phone']    ?? null,
            ':primary_email'    => $data['primary_email']    ?? null,
            ':related_vehicles' => $data['related_vehicles'] ?? null,
            ':related_services' => $data['related_services'] ?? null,
            ':region_name'      => $data['region_name']      ?? null,
            ':is_published'     => (int) ($data['is_published'] ?? 1),
            ':sort_order'       => (int) ($data['sort_order'] ?? 0),
            ':updated_at'       => $now,
        ];
        if (!empty($data['id'])) {
            $params[':id'] = (int) $data['id'];
            $sql = 'UPDATE landing_pages SET
                slug=:slug, kind=:kind, title=:title, eyebrow=:eyebrow, headline=:headline, sub=:sub,
                intro_html=:intro_html, body_html=:body_html, meta_title=:meta_title,
                meta_description=:meta_description, cta_label=:cta_label,
                primary_phone=:primary_phone, primary_email=:primary_email,
                related_vehicles=:related_vehicles, related_services=:related_services,
                region_name=:region_name, is_published=:is_published, sort_order=:sort_order,
                updated_at=:updated_at WHERE id=:id';
            $this->pdo->prepare($sql)->execute($params);
            return (int) $data['id'];
        }
        $sql = 'INSERT INTO landing_pages
            (slug, kind, title, eyebrow, headline, sub, intro_html, body_html,
             meta_title, meta_description, cta_label, primary_phone, primary_email,
             related_vehicles, related_services, region_name, is_published, sort_order, updated_at)
            VALUES
            (:slug, :kind, :title, :eyebrow, :headline, :sub, :intro_html, :body_html,
             :meta_title, :meta_description, :cta_label, :primary_phone, :primary_email,
             :related_vehicles, :related_services, :region_name, :is_published, :sort_order, :updated_at)';
        $this->pdo->prepare($sql)->execute($params);
        return (int) $this->pdo->lastInsertId();
    }

    public function delete(int $id): void
    {
        $this->pdo->prepare('DELETE FROM landing_pages WHERE id = ?')->execute([$id]);
    }
}
