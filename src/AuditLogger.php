<?php

declare(strict_types=1);

namespace App\Core;

final class AuditLogger
{
    /**
     * Logs an admin write action. Cheap (single INSERT) – call freely.
     *
     * @param string $action e.g. 'page.update', 'vehicle.delete', 'auth.login'
     */
    public static function log(string $action, ?string $entity = null, ?int $entityId = null, array $payload = []): void
    {
        try {
            $pdo  = Database::getInstance();
            $user = Auth::user();
            $stmt = $pdo->prepare('
                INSERT INTO admin_audit_log
                  (user_id, username, action, entity, entity_id, ip, user_agent, payload)
                VALUES
                  (:uid, :uname, :action, :entity, :entity_id, :ip, :ua, :payload)
            ');
            // Trim payload to avoid huge entries
            $json = json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            if (is_string($json) && strlen($json) > 4096) {
                $json = substr($json, 0, 4093) . '...';
            }
            $stmt->execute([
                ':uid'       => $user['id'] ?? null,
                ':uname'     => $user['username'] ?? null,
                ':action'    => $action,
                ':entity'    => $entity,
                ':entity_id' => $entityId,
                ':ip'        => substr((string) ($_SERVER['REMOTE_ADDR'] ?? ''), 0, 64),
                ':ua'        => substr((string) ($_SERVER['HTTP_USER_AGENT'] ?? ''), 0, 255),
                ':payload'   => $json ?: null,
            ]);
        } catch (\Throwable) {
            // never block the request because of logging
        }
    }

    /** @return array<int,array<string,mixed>> */
    public static function recent(int $limit = 100): array
    {
        try {
            $pdo = Database::getInstance();
            $stmt = $pdo->prepare('SELECT * FROM admin_audit_log ORDER BY id DESC LIMIT ?');
            $stmt->bindValue(1, $limit, \PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (\Throwable) {
            return [];
        }
    }
}
