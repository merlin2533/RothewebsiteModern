<?php

declare(strict_types=1);

namespace App\Repositories;

final class MediaRepository
{
    public function __construct(private \PDO $pdo) {}

    public function all(): array
    {
        return $this->pdo->query('SELECT * FROM media ORDER BY uploaded_at DESC, id DESC')->fetchAll();
    }

    public function find(int $id): ?array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM media WHERE id = ? LIMIT 1');
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    public function save(array $data): int
    {
        $stmt = $this->pdo->prepare('
            INSERT INTO media (filename, original_name, mime, width, height, alt_text, size_bytes, uploaded_at)
            VALUES (:filename, :original_name, :mime, :width, :height, :alt_text, :size_bytes, :uploaded_at)
        ');
        $stmt->execute([
            ':filename'      => $data['filename'],
            ':original_name' => $data['original_name'] ?? null,
            ':mime'          => $data['mime'] ?? null,
            ':width'         => isset($data['width']) ? (int) $data['width'] : null,
            ':height'        => isset($data['height']) ? (int) $data['height'] : null,
            ':alt_text'      => $data['alt_text'] ?? '',
            ':size_bytes'    => isset($data['size_bytes']) ? (int) $data['size_bytes'] : null,
            ':uploaded_at'   => date('Y-m-d H:i:s'),
        ]);
        return (int) $this->pdo->lastInsertId();
    }

    public function updateAlt(int $id, string $alt): void
    {
        $this->pdo->prepare('UPDATE media SET alt_text = ? WHERE id = ?')->execute([$alt, $id]);
    }

    public function delete(int $id): void
    {
        $this->pdo->prepare('DELETE FROM media WHERE id = ?')->execute([$id]);
    }
}
