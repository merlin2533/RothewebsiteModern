-- =============================================================================
-- 004_audit_attribution_security.sql
-- Audit-Log fuer Admin-Aktionen, Lead-Attribution (UTM-Tracking),
-- DB-basierter Brute-Force-Schutz (statt JSON-Datei).
-- =============================================================================

-- Wer hat im Admin was wann geaendert?
CREATE TABLE IF NOT EXISTS admin_audit_log (
    id          INTEGER PRIMARY KEY AUTOINCREMENT,
    user_id     INTEGER REFERENCES users(id) ON DELETE SET NULL,
    username    TEXT,
    action      TEXT NOT NULL,           -- z.B. 'page.update', 'vehicle.delete'
    entity      TEXT,                    -- z.B. 'page', 'vehicle', 'faq'
    entity_id   INTEGER,
    ip          TEXT,
    user_agent  TEXT,
    payload     TEXT,                    -- JSON mit relevanten Feldern (gekuerzt)
    created_at  TEXT NOT NULL DEFAULT (strftime('%Y-%m-%d %H:%M:%S', 'now'))
);
CREATE INDEX IF NOT EXISTS idx_audit_user      ON admin_audit_log(user_id);
CREATE INDEX IF NOT EXISTS idx_audit_entity    ON admin_audit_log(entity, entity_id);
CREATE INDEX IF NOT EXISTS idx_audit_created   ON admin_audit_log(created_at);

-- Lead-Attribution: woher kam der Besucher, der angerufen / gemailt hat?
CREATE TABLE IF NOT EXISTS lead_attributions (
    id           INTEGER PRIMARY KEY AUTOINCREMENT,
    session_key  TEXT NOT NULL UNIQUE,    -- random Cookie 'rt_attr'
    first_seen   TEXT NOT NULL DEFAULT (strftime('%Y-%m-%d %H:%M:%S', 'now')),
    last_seen    TEXT NOT NULL DEFAULT (strftime('%Y-%m-%d %H:%M:%S', 'now')),
    landing_url  TEXT,                    -- erste angeklickte URL
    referrer     TEXT,                    -- HTTP_REFERER beim ersten Besuch
    utm_source   TEXT,
    utm_medium   TEXT,
    utm_campaign TEXT,
    utm_term     TEXT,
    utm_content  TEXT,
    gclid        TEXT,
    fbclid       TEXT,
    msclkid      TEXT,
    visits       INTEGER NOT NULL DEFAULT 1,
    user_agent   TEXT,
    ip_hash      TEXT                     -- SHA1(IP+salt) – pseudonymisiert
);
CREATE INDEX IF NOT EXISTS idx_attr_session  ON lead_attributions(session_key);
CREATE INDEX IF NOT EXISTS idx_attr_source   ON lead_attributions(utm_source);
CREATE INDEX IF NOT EXISTS idx_attr_campaign ON lead_attributions(utm_campaign);

-- Brute-Force-Schutz in SQLite (statt JSON). Laeuft mit Indizes performanter.
CREATE TABLE IF NOT EXISTS login_failures (
    id         INTEGER PRIMARY KEY AUTOINCREMENT,
    ip         TEXT NOT NULL,
    occurred_at TEXT NOT NULL DEFAULT (strftime('%Y-%m-%d %H:%M:%S', 'now'))
);
CREATE INDEX IF NOT EXISTS idx_login_failures_ip   ON login_failures(ip);
CREATE INDEX IF NOT EXISTS idx_login_failures_when ON login_failures(occurred_at);
