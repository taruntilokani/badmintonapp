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
