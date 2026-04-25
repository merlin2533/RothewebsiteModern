<?php

declare(strict_types=1);

namespace App\Repositories;

final class VehicleRepository
{
    public function __construct(private \PDO $pdo) {}

    public function all(): array
    {
        $rows = $this->pdo->query('
            SELECT v.*, m.filename AS image_filename, m.alt_text AS image_alt
            FROM vehicles v LEFT JOIN media m ON v.image_id = m.id
            ORDER BY v.sort_order, v.id
        ')->fetchAll();
        return $rows;
    }

    public function allActive(): array
    {
        $rows = $this->pdo->query('
            SELECT v.*, m.filename AS image_filename, m.alt_text AS image_alt
            FROM vehicles v LEFT JOIN media m ON v.image_id = m.id
            WHERE v.is_active = 1
            ORDER BY v.sort_order, v.id
        ')->fetchAll();
        return $rows;
    }

    public function findBySlug(string $slug): ?array
    {
        $stmt = $this->pdo->prepare('
            SELECT v.*, m.filename AS image_filename, m.alt_text AS image_alt
            FROM vehicles v LEFT JOIN media m ON v.image_id = m.id
            WHERE v.slug = ? LIMIT 1
        ');
        $stmt->execute([$slug]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function find(int $id): ?array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM vehicles WHERE id = ? LIMIT 1');
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function save(array $data): int
    {
        $now = date('Y-m-d H:i:s');
        $params = [
            ':slug'             => $data['slug'],
            ':name'             => $data['name'],
            ':marketing_text'   => $data['marketing_text'] ?? null,
            ':length_m'         => isset($data['length_m']) && $data['length_m'] !== '' ? (float) $data['length_m'] : null,
            ':width_m'          => isset($data['width_m']) && $data['width_m'] !== '' ? (float) $data['width_m'] : null,
            ':height_m'         => isset($data['height_m']) && $data['height_m'] !== '' ? (float) $data['height_m'] : null,
            ':payload_kg'       => isset($data['payload_kg']) && $data['payload_kg'] !== '' ? (int) $data['payload_kg'] : null,
            ':euro_pallets'     => isset($data['euro_pallets']) && $data['euro_pallets'] !== '' ? (int) $data['euro_pallets'] : null,
            ':axles'            => isset($data['axles']) && $data['axles'] !== '' ? (int) $data['axles'] : null,
            ':special_features' => $data['special_features'] ?? null,
            ':image_id'         => !empty($data['image_id']) ? (int) $data['image_id'] : null,
            ':is_active'        => (int) ($data['is_active'] ?? 1),
            ':sort_order'       => (int) ($data['sort_order'] ?? 0),
            ':updated_at'       => $now,
        ];
        if (!empty($data['id'])) {
            $params[':id'] = (int) $data['id'];
            $sql = 'UPDATE vehicles SET
                slug=:slug, name=:name, marketing_text=:marketing_text,
                length_m=:length_m, width_m=:width_m, height_m=:height_m,
                payload_kg=:payload_kg, euro_pallets=:euro_pallets, axles=:axles,
                special_features=:special_features, image_id=:image_id,
                is_active=:is_active, sort_order=:sort_order, updated_at=:updated_at
                WHERE id=:id';
            $this->pdo->prepare($sql)->execute($params);
            return (int) $data['id'];
        }
        $sql = 'INSERT INTO vehicles
            (slug, name, marketing_text, length_m, width_m, height_m, payload_kg,
             euro_pallets, axles, special_features, image_id, is_active, sort_order, updated_at)
            VALUES
            (:slug, :name, :marketing_text, :length_m, :width_m, :height_m, :payload_kg,
             :euro_pallets, :axles, :special_features, :image_id, :is_active, :sort_order, :updated_at)';
        $this->pdo->prepare($sql)->execute($params);
        return (int) $this->pdo->lastInsertId();
    }

    public function delete(int $id): void
    {
        $this->pdo->prepare('DELETE FROM vehicles WHERE id = ?')->execute([$id]);
    }
}
