PRAGMA foreign_keys = off;
BEGIN TRANSACTION;

-- Table: db_users
CREATE TABLE IF NOT EXISTS db_users (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    username TEXT NOT NULL UNIQUE,
    password TEXT NOT NULL,
    isAdmin INTEGER DEFAULT 0
);

-- Table: levels
CREATE TABLE IF NOT EXISTS levels (
    id            INTEGER PRIMARY KEY AUTOINCREMENT,
    name          TEXT,
    description   TEXT,
    userName      TEXT,
    gameVersion   INTEGER,
    userID        INTEGER UNIQUE,
    levelVersion  INTEGER,
    levelLength   INTEGER,
    audioTrack    INTEGER,
    created_at    DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at    DATETIME DEFAULT CURRENT_TIMESTAMP,
    udid          TEXT,
    rating        INTEGER DEFAULT 0,
    downloads     INTEGER DEFAULT 0,
    likes         INTEGER DEFAULT 0,
    featured      INTEGER DEFAULT 0
);

-- Table: likes
CREATE TABLE IF NOT EXISTS likes (
    id      INTEGER PRIMARY KEY AUTOINCREMENT,
    levelID INTEGER,
    ip      TEXT,
    UNIQUE(levelID, ip)
);

-- Table: users
CREATE TABLE IF NOT EXISTS users (
    id       INTEGER PRIMARY KEY AUTOINCREMENT,
    udid     TEXT UNIQUE,
    username TEXT
);

COMMIT TRANSACTION;
PRAGMA foreign_keys = on;
