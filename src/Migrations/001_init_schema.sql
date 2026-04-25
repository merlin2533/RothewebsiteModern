-- =============================================================================
-- 001_init_schema.sql
-- Initial database schema for rothe-transporte.de
-- SQLite 3 syntax; foreign keys must be ON before using.
-- =============================================================================

-- Admin users
CREATE TABLE IF NOT EXISTS users (
    id             INTEGER PRIMARY KEY AUTOINCREMENT,
    username       TEXT    NOT NULL UNIQUE COLLATE NOCASE,
    password_hash  TEXT    NOT NULL,
    role           TEXT    NOT NULL DEFAULT 'admin',
    created_at     TEXT    NOT NULL DEFAULT (strftime('%Y-%m-%d %H:%M:%S', 'now')),
    last_login_at  TEXT
);

-- Sitewide key/value settings (contact data, SEO defaults, JSON-LD)
CREATE TABLE IF NOT EXISTS settings (
    key        TEXT PRIMARY KEY,
    value      TEXT,
    updated_at TEXT NOT NULL DEFAULT (strftime('%Y-%m-%d %H:%M:%S', 'now'))
);

-- Uploaded media / images
CREATE TABLE IF NOT EXISTS media (
    id            INTEGER PRIMARY KEY AUTOINCREMENT,
    filename      TEXT    NOT NULL,
    original_name TEXT,
    mime          TEXT,
    width         INTEGER,
    height        INTEGER,
    alt_text      TEXT,
    size_bytes    INTEGER,
    uploaded_at   TEXT    NOT NULL DEFAULT (strftime('%Y-%m-%d %H:%M:%S', 'now'))
);

-- Static / CMS pages
CREATE TABLE IF NOT EXISTS pages (
    id               INTEGER PRIMARY KEY AUTOINCREMENT,
    slug             TEXT    NOT NULL UNIQUE,
    title            TEXT    NOT NULL,
    meta_title       TEXT,
    meta_description TEXT,
    og_image_id      INTEGER REFERENCES media(id) ON DELETE SET NULL,
    hero_headline    TEXT,
    hero_sub         TEXT,
    hero_image_id    INTEGER REFERENCES media(id) ON DELETE SET NULL,
    content_html     TEXT,
    is_published     INTEGER NOT NULL DEFAULT 1,
    sort_order       INTEGER NOT NULL DEFAULT 0,
    created_at       TEXT    NOT NULL DEFAULT (strftime('%Y-%m-%d %H:%M:%S', 'now')),
    updated_at       TEXT    NOT NULL DEFAULT (strftime('%Y-%m-%d %H:%M:%S', 'now'))
);

CREATE INDEX IF NOT EXISTS idx_pages_slug        ON pages(slug);
CREATE INDEX IF NOT EXISTS idx_pages_is_published ON pages(is_published);

-- Vehicles / Fuhrpark
CREATE TABLE IF NOT EXISTS vehicles (
    id               INTEGER PRIMARY KEY AUTOINCREMENT,
    slug             TEXT    NOT NULL UNIQUE,
    name             TEXT    NOT NULL,
    marketing_text   TEXT,
    -- Technical specs (treat as read-only in production)
    length_m         REAL,
    width_m          REAL,
    height_m         REAL,
    payload_kg       INTEGER,
    euro_pallets     INTEGER,
    axles            INTEGER,
    special_features TEXT,
    image_id         INTEGER REFERENCES media(id) ON DELETE SET NULL,
    is_active        INTEGER NOT NULL DEFAULT 1,
    sort_order       INTEGER NOT NULL DEFAULT 0,
    created_at       TEXT    NOT NULL DEFAULT (strftime('%Y-%m-%d %H:%M:%S', 'now')),
    updated_at       TEXT    NOT NULL DEFAULT (strftime('%Y-%m-%d %H:%M:%S', 'now'))
);

CREATE INDEX IF NOT EXISTS idx_vehicles_slug      ON vehicles(slug);
CREATE INDEX IF NOT EXISTS idx_vehicles_is_active ON vehicles(is_active);

-- Services / Leistungen
CREATE TABLE IF NOT EXISTS services (
    id         INTEGER PRIMARY KEY AUTOINCREMENT,
    slug       TEXT    NOT NULL UNIQUE,
    title      TEXT    NOT NULL,
    summary    TEXT,
    body_html  TEXT,
    icon_key   TEXT,
    is_active  INTEGER NOT NULL DEFAULT 1,
    sort_order INTEGER NOT NULL DEFAULT 0,
    created_at TEXT    NOT NULL DEFAULT (strftime('%Y-%m-%d %H:%M:%S', 'now')),
    updated_at TEXT    NOT NULL DEFAULT (strftime('%Y-%m-%d %H:%M:%S', 'now'))
);

CREATE INDEX IF NOT EXISTS idx_services_slug ON services(slug);

-- Timeline events / Firmengeschichte
CREATE TABLE IF NOT EXISTS timeline_events (
    id          INTEGER PRIMARY KEY AUTOINCREMENT,
    year        INTEGER NOT NULL,
    title       TEXT    NOT NULL,
    description TEXT,
    is_active   INTEGER NOT NULL DEFAULT 1,
    sort_order  INTEGER NOT NULL DEFAULT 0,
    created_at  TEXT    NOT NULL DEFAULT (strftime('%Y-%m-%d %H:%M:%S', 'now')),
    updated_at  TEXT    NOT NULL DEFAULT (strftime('%Y-%m-%d %H:%M:%S', 'now'))
);

CREATE INDEX IF NOT EXISTS idx_timeline_year ON timeline_events(year);
