CREATE TABLE IF NOT EXISTS bt_users (
  username VARCHAR(40) NOT NULL,
  display_name VARCHAR(120) NOT NULL,
  password_hash VARCHAR(255) NOT NULL,
  is_admin TINYINT(1) NOT NULL DEFAULT 0,
  must_reset_password TINYINT(1) NOT NULL DEFAULT 0,
  is_active TINYINT(1) NOT NULL DEFAULT 1,
  created_at VARCHAR(40) NOT NULL,
  updated_at VARCHAR(40) NOT NULL,
  PRIMARY KEY (username)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS bt_sessions (
  token CHAR(64) NOT NULL,
  username VARCHAR(40) NOT NULL,
  expires_at INT UNSIGNED NOT NULL,
  created_at VARCHAR(40) NOT NULL,
  PRIMARY KEY (token),
  KEY idx_bt_sessions_username (username),
  KEY idx_bt_sessions_expires_at (expires_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS bt_app_state (
  storage_key VARCHAR(191) NOT NULL,
  storage_value LONGTEXT NOT NULL,
  updated_at VARCHAR(40) NOT NULL,
  PRIMARY KEY (storage_key)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS bt_tournaments (
  owner_username VARCHAR(40) NOT NULL DEFAULT '',
  tournament_id VARCHAR(191) NOT NULL,
  name VARCHAR(255) NOT NULL,
  scheduled_date VARCHAR(32) NOT NULL DEFAULT '',
  data_json LONGTEXT NOT NULL,
  data_hash CHAR(64) NOT NULL,
  version INT UNSIGNED NOT NULL DEFAULT 1,
  created_at VARCHAR(40) NOT NULL,
  updated_at VARCHAR(40) NOT NULL,
  PRIMARY KEY (tournament_id),
  KEY idx_bt_tournaments_owner_updated (owner_username, updated_at),
  KEY idx_bt_tournaments_scheduled_date (scheduled_date),
  KEY idx_bt_tournaments_updated_at (updated_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS bt_tournament_matches (
  tournament_id VARCHAR(191) NOT NULL,
  match_id VARCHAR(191) NOT NULL,
  stage VARCHAR(80) NOT NULL DEFAULT '',
  group_index SMALLINT NULL,
  knockout_round SMALLINT NULL,
  team1 VARCHAR(255) NOT NULL DEFAULT '',
  team2 VARCHAR(255) NOT NULL DEFAULT '',
  score1 SMALLINT UNSIGNED NULL,
  score2 SMALLINT UNSIGNED NULL,
  data_json LONGTEXT NOT NULL,
  version INT UNSIGNED NOT NULL DEFAULT 1,
  updated_at VARCHAR(40) NOT NULL,
  PRIMARY KEY (tournament_id, match_id),
  KEY idx_bt_tournament_matches_tournament (tournament_id),
  KEY idx_bt_tournament_matches_updated_at (updated_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS bt_player_lists (
  owner_username VARCHAR(40) NOT NULL DEFAULT '',
  list_id VARCHAR(191) NOT NULL,
  name VARCHAR(255) NOT NULL,
  player_count SMALLINT UNSIGNED NOT NULL DEFAULT 0,
  data_json LONGTEXT NOT NULL,
  data_hash CHAR(64) NOT NULL,
  version INT UNSIGNED NOT NULL DEFAULT 1,
  created_at VARCHAR(40) NOT NULL,
  updated_at VARCHAR(40) NOT NULL,
  PRIMARY KEY (owner_username, list_id),
  KEY idx_bt_player_lists_name (owner_username, name),
  KEY idx_bt_player_lists_owner_updated (owner_username, updated_at),
  KEY idx_bt_player_lists_updated_at (updated_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS bt_app_settings (
  owner_username VARCHAR(40) NOT NULL DEFAULT '',
  storage_key VARCHAR(191) NOT NULL,
  storage_value LONGTEXT NOT NULL,
  updated_at VARCHAR(40) NOT NULL,
  PRIMARY KEY (owner_username, storage_key),
  KEY idx_bt_app_settings_owner_updated (owner_username, updated_at),
  KEY idx_bt_app_settings_updated_at (updated_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
