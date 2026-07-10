<?php
declare(strict_types=1);

/*
 * Badminton Tournament Manager - PHP 8.3.19 MySQL API
 *
 * InfinityFree deployment:
 * - index.php and api.php live in the same folder.
 * - db-config.php stores the MySQL credentials.
 * - database.php creates and opens the MySQL tables.
 */

require_once __DIR__ . DIRECTORY_SEPARATOR . 'database.php';

const SESSION_IDLE_SECONDS = 120;
const MAX_ACTIVE_SESSIONS = 3;

header('Cache-Control: no-store, private');
header('X-Content-Type-Options: nosniff');

function fail(string $message, int $status = 400): never
{
    http_response_code($status);
    header('Content-Type: text/plain; charset=utf-8');
    echo $message;
    exit;
}

function respond(mixed $value = null, int $status = 200): never
{
    http_response_code($status);
    header('Content-Type: application/json; charset=utf-8');
    echo jsonEncodeValue($value);
    exit;
}

function db(): PDO
{
    try {
        return btPdo();
    } catch (Throwable $error) {
        fail('Database connection failed: ' . $error->getMessage(), 500);
    }
}

function initializeDatabase(): void
{
    try {
        $pdo = db();
        btCreateSchema($pdo);
        ensureInitialAdmin();
        cleanupExpiredSessions();
    } catch (Throwable $error) {
        fail('Database initialization failed: ' . $error->getMessage(), 500);
    }
}

function startsWith(string $value, string $prefix): bool
{
    return strncmp($value, $prefix, strlen($prefix)) === 0;
}

function jsonEncodeValue($value, int $flags = 0): string
{
    $encoded = json_encode($value, $flags | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    if ($encoded === false) {
        fail('The server could not encode application data.', 500);
    }
    return $encoded;
}

function requestPayload(): array
{
    $raw = file_get_contents('php://input');
    if ($raw === false || trim($raw) === '') {
        return [];
    }
    $decoded = json_decode($raw, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        fail('Invalid JSON request.', 400);
    }
    if (!is_array($decoded)) {
        fail('The request body must be a JSON object.', 400);
    }
    return $decoded;
}

function nowIso(): string
{
    return gmdate(DATE_ATOM);
}

function normalizeUsername($username): string
{
    return strtolower(trim((string) $username));
}

function normalizeUserRow(array $row): array
{
    return [
        'username' => (string) $row['username'],
        'display_name' => (string) $row['display_name'],
        'password_hash' => (string) $row['password_hash'],
        'is_admin' => (bool) $row['is_admin'],
        'must_reset_password' => (bool) $row['must_reset_password'],
        'is_active' => (bool) $row['is_active'],
        'created_at' => (string) $row['created_at'],
        'updated_at' => (string) $row['updated_at'],
    ];
}

function initialUsers(): array
{
    $now = nowIso();
    return [
        'admin' => [
            'username' => 'admin',
            'display_name' => 'Administrator',
            'password_hash' => password_hash('admin', PASSWORD_DEFAULT),
            'is_admin' => true,
            'must_reset_password' => false,
            'is_active' => true,
            'created_at' => $now,
            'updated_at' => $now,
        ],
    ];
}

function ensureInitialAdmin(): void
{
    $count = (int) db()->query('SELECT COUNT(*) FROM bt_users')->fetchColumn();
    if ($count > 0) {
        return;
    }
    replaceStoreInDatabase('users', initialUsers());
}

function cleanupExpiredSessions(): void
{
    $statement = db()->prepare('DELETE FROM bt_sessions WHERE expires_at <= ?');
    $statement->execute([time()]);
}

function readStore(string $name, array $default): array
{
    try {
        return readStoreFromDatabase($name, $default);
    } catch (Throwable $error) {
        fail('Cannot read MySQL application data: ' . $error->getMessage(), 500);
    }
}

function mutateStore(string $name, array $default, callable $callback)
{
    $pdo = db();
    try {
        $pdo->beginTransaction();
        $value = readStoreFromDatabase($name, $default);
        $result = $callback($value);
        replaceStoreInDatabase($name, $value);
        $pdo->commit();
        return $result;
    } catch (Throwable $error) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        fail('Cannot save MySQL application data: ' . $error->getMessage(), 500);
    }
}

function readStoreFromDatabase(string $name, array $default): array
{
    $pdo = db();
    if ($name === 'users') {
        $users = [];
        $statement = $pdo->query('SELECT username, display_name, password_hash, is_admin, must_reset_password, is_active, created_at, updated_at FROM bt_users ORDER BY username');
        foreach ($statement->fetchAll() as $row) {
            $user = normalizeUserRow($row);
            $users[$user['username']] = $user;
        }
        return $users ?: $default;
    }

    if ($name === 'sessions') {
        $sessions = [];
        $statement = $pdo->query('SELECT token, username, expires_at FROM bt_sessions');
        foreach ($statement->fetchAll() as $row) {
            $token = (string) $row['token'];
            $sessions[$token] = [
                'username' => (string) $row['username'],
                'expires_at' => (int) $row['expires_at'],
            ];
        }
        return $sessions ?: $default;
    }

    if ($name === 'app-state') {
        $state = [];
        $statement = $pdo->query('SELECT storage_key, storage_value FROM bt_app_state');
        foreach ($statement->fetchAll() as $row) {
            $state[(string) $row['storage_key']] = (string) $row['storage_value'];
        }
        return $state ?: $default;
    }

    return $default;
}

function replaceStoreInDatabase(string $name, array $value): void
{
    $pdo = db();
    $now = nowIso();

    if ($name === 'users') {
        $pdo->exec('DELETE FROM bt_users');
        $statement = $pdo->prepare('
            INSERT INTO bt_users
                (username, display_name, password_hash, is_admin, must_reset_password, is_active, created_at, updated_at)
            VALUES
                (?, ?, ?, ?, ?, ?, ?, ?)
        ');
        foreach ($value as $user) {
            if (!is_array($user) || trim((string) ($user['username'] ?? '')) === '') {
                continue;
            }
            $statement->execute([
                normalizeUsername($user['username']),
                trim((string) ($user['display_name'] ?? $user['username'])),
                (string) ($user['password_hash'] ?? ''),
                !empty($user['is_admin']) ? 1 : 0,
                !empty($user['must_reset_password']) ? 1 : 0,
                !empty($user['is_active']) ? 1 : 0,
                (string) ($user['created_at'] ?? $now),
                (string) ($user['updated_at'] ?? $now),
            ]);
        }
        return;
    }

    if ($name === 'sessions') {
        $pdo->exec('DELETE FROM bt_sessions');
        $statement = $pdo->prepare('
            INSERT INTO bt_sessions (token, username, expires_at, created_at)
            VALUES (?, ?, ?, ?)
        ');
        foreach ($value as $token => $session) {
            if (!is_array($session) || trim((string) $token) === '' || trim((string) ($session['username'] ?? '')) === '') {
                continue;
            }
            $expiresAt = (int) ($session['expires_at'] ?? 0);
            if ($expiresAt <= time()) {
                continue;
            }
            $statement->execute([
                (string) $token,
                normalizeUsername($session['username']),
                $expiresAt,
                (string) ($session['created_at'] ?? $now),
            ]);
        }
        return;
    }

    if ($name === 'app-state') {
        $pdo->exec('DELETE FROM bt_app_state');
        $statement = $pdo->prepare('
            INSERT INTO bt_app_state (storage_key, storage_value, updated_at)
            VALUES (?, ?, ?)
        ');
        foreach ($value as $key => $storageValue) {
            $key = trim((string) $key);
            if ($key === '') {
                continue;
            }
            $statement->execute([$key, (string) $storageValue, $now]);
        }
    }
}

function loadUsers(): array
{
    $users = readStore('users', []);
    if ($users !== []) {
        return $users;
    }
    ensureInitialAdmin();
    return readStore('users', []);
}

function sessionResponse(array $user, string $token, int $expiresAt): array
{
    return [
        'token' => $token,
        'username' => $user['username'],
        'displayName' => $user['display_name'],
        'isAdmin' => (bool) $user['is_admin'],
        'mustResetPassword' => (bool) $user['must_reset_password'],
        'expiresAt' => gmdate(DATE_ATOM, $expiresAt),
    ];
}

function requireUser($rawToken, bool $adminOnly = false): array
{
    $token = trim((string) $rawToken);
    if ($token === '') {
        fail('Invalid or expired session.', 401);
    }

    $session = mutateStore('sessions', [], function (array &$sessions) use ($token) {
        $now = time();
        foreach ($sessions as $key => $candidate) {
            if (!is_array($candidate) || (int) ($candidate['expires_at'] ?? 0) <= $now) {
                unset($sessions[$key]);
            }
        }
        return isset($sessions[$token]) && is_array($sessions[$token]) ? $sessions[$token] : null;
    });
    if (!is_array($session)) {
        fail('Invalid or expired session.', 401);
    }

    $users = loadUsers();
    $username = (string) ($session['username'] ?? '');
    $user = $users[$username] ?? null;
    if (!is_array($user) || empty($user['is_active'])) {
        fail('Invalid or expired session.', 401);
    }
    if ($adminOnly && empty($user['is_admin'])) {
        fail('Administrator access required.', 403);
    }
    return [$user, $token, $session];
}

function validateNewPassword(string $password): void
{
    if (strlen($password) < 8) {
        fail('Password must contain at least 8 characters.', 400);
    }
}

function deleteSessionsForUser(string $username): void
{
    mutateStore('sessions', [], function (array &$sessions) use ($username) {
        foreach ($sessions as $token => $session) {
            if (($session['username'] ?? null) === $username) {
                unset($sessions[$token]);
            }
        }
    });
}

if (version_compare(PHP_VERSION, MINIMUM_PHP_VERSION, '<')) {
    fail('PHP ' . MINIMUM_PHP_VERSION . '+ is required. Configure the hosting account for PHP ' . TARGET_PHP_VERSION . '.', 500);
}

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'OPTIONS') {
    http_response_code(204);
    exit;
}

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
    fail('POST requests only.', 405);
}

initializeDatabase();

$payload = requestPayload();
$action = trim((string) ($_GET['action'] ?? ''));

switch ($action) {
    case 'login_user':
        $username = normalizeUsername($payload['username'] ?? '');
        $password = (string) ($payload['password'] ?? '');
        $users = loadUsers();
        $user = $users[$username] ?? null;
        if (!is_array($user) || empty($user['is_active']) || !password_verify($password, (string) $user['password_hash'])) {
            fail('Invalid username or password.', 401);
        }
        $token = bin2hex(random_bytes(32));
        $expiresAt = time() + SESSION_IDLE_SECONDS;
        mutateStore('sessions', [], function (array &$sessions) use ($username, $token, $expiresAt) {
            $now = time();
            foreach ($sessions as $key => $session) {
                if ((int) ($session['expires_at'] ?? 0) <= $now) {
                    unset($sessions[$key]);
                }
            }
            $active = count(array_filter($sessions, function ($session) use ($username): bool {
                return is_array($session) && ($session['username'] ?? null) === $username;
            }));
            if ($active >= MAX_ACTIVE_SESSIONS) {
                fail('Login session limit exceeded.', 409);
            }
            $sessions[$token] = ['username' => $username, 'expires_at' => $expiresAt, 'created_at' => nowIso()];
        });
        respond(sessionResponse($user, $token, $expiresAt));

    case 'logout_user':
        $token = trim((string) ($payload['auth_token'] ?? ''));
        mutateStore('sessions', [], function (array &$sessions) use ($token) {
            unset($sessions[$token]);
        });
        respond(['ok' => true]);

    case 'refresh_session':
        list($user, $token) = requireUser($payload['auth_token'] ?? '');
        $expiresAt = time() + SESSION_IDLE_SECONDS;
        mutateStore('sessions', [], function (array &$sessions) use ($token, $expiresAt) {
            if (isset($sessions[$token])) {
                $sessions[$token]['expires_at'] = $expiresAt;
            }
        });
        respond(['expiresAt' => gmdate(DATE_ATOM, $expiresAt)]);

    case 'export_app_state':
        requireUser($payload['auth_token'] ?? '');
        respond(readStore('app-state', []));

    case 'save_tournament':
        requireUser($payload['auth_token'] ?? '');
        $tournament = $payload['payload'] ?? null;
        if (!is_array($tournament) || trim((string) ($tournament['id'] ?? '')) === '') {
            fail('A tournament payload with an id is required.', 400);
        }
        $key = 'bt_tournament_v1_' . (string) $tournament['id'];
        mutateStore('app-state', [], function (array &$state) use ($key, $tournament) {
            $state[$key] = jsonEncodeValue($tournament);
        });
        respond(['ok' => true]);

    case 'delete_tournament':
        requireUser($payload['auth_token'] ?? '');
        $key = 'bt_tournament_v1_' . trim((string) ($payload['tournament_id'] ?? ''));
        mutateStore('app-state', [], function (array &$state) use ($key) {
            unset($state[$key]);
        });
        respond(['ok' => true]);

    case 'save_player_list':
        requireUser($payload['auth_token'] ?? '');
        $key = trim((string) ($payload['storage_key'] ?? ''));
        $value = $payload['payload'] ?? null;
        if (!startsWith($key, 'bt_playerlist_v1_') || !is_array($value)) {
            fail('A valid player list is required.', 400);
        }
        mutateStore('app-state', [], function (array &$state) use ($key, $value) {
            $state[$key] = jsonEncodeValue($value);
        });
        respond(['ok' => true]);

    case 'delete_player_list':
        requireUser($payload['auth_token'] ?? '');
        $key = 'bt_playerlist_v1_' . trim((string) ($payload['player_list_id'] ?? ''));
        mutateStore('app-state', [], function (array &$state) use ($key) {
            unset($state[$key]);
        });
        respond(['ok' => true]);

    case 'save_app_setting':
        requireUser($payload['auth_token'] ?? '');
        $key = trim((string) ($payload['storage_key'] ?? ''));
        if (!startsWith($key, 'bt_')) {
            fail('Invalid application storage key.', 400);
        }
        $value = (string) ($payload['storage_value'] ?? '');
        mutateStore('app-state', [], function (array &$state) use ($key, $value) {
            $state[$key] = $value;
        });
        respond(['ok' => true]);

    case 'delete_app_setting':
        requireUser($payload['auth_token'] ?? '');
        $key = trim((string) ($payload['storage_key'] ?? ''));
        mutateStore('app-state', [], function (array &$state) use ($key) {
            unset($state[$key]);
        });
        respond(['ok' => true]);

    case 'list_users':
        requireUser($payload['auth_token'] ?? '', true);
        $users = array_values(array_map(function (array $user): array {
            unset($user['password_hash']);
            return $user;
        }, loadUsers()));
        usort($users, function (array $a, array $b): int {
            return strcmp($a['username'], $b['username']);
        });
        respond($users);

    case 'create_user':
        requireUser($payload['auth_token'] ?? '', true);
        $username = normalizeUsername($payload['username'] ?? '');
        $displayName = trim((string) ($payload['display_name'] ?? ''));
        $password = (string) ($payload['temporary_password'] ?? '');
        if (!preg_match('/^[a-z0-9._-]{3,40}$/', $username)) {
            fail('Username must be 3-40 characters using letters, numbers, dot, dash, or underscore.', 400);
        }
        if ($displayName === '') {
            fail('Display name is required.', 400);
        }
        validateNewPassword($password);
        mutateStore('users', [], function (array &$users) use ($username, $displayName, $password, $payload) {
            if (isset($users[$username])) {
                fail('Username already exists.', 409);
            }
            $now = nowIso();
            $users[$username] = [
                'username' => $username,
                'display_name' => $displayName,
                'password_hash' => password_hash($password, PASSWORD_DEFAULT),
                'is_admin' => !empty($payload['is_admin']),
                'must_reset_password' => true,
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        });
        respond(['ok' => true]);

    case 'change_my_password':
        list($user, $token) = requireUser($payload['auth_token'] ?? '');
        $current = (string) ($payload['current_password'] ?? '');
        $new = (string) ($payload['new_password'] ?? '');
        if (!password_verify($current, (string) $user['password_hash'])) {
            fail('Current password is incorrect.', 401);
        }
        validateNewPassword($new);
        $username = $user['username'];
        mutateStore('users', [], function (array &$users) use ($username, $new) {
            $users[$username]['password_hash'] = password_hash($new, PASSWORD_DEFAULT);
            $users[$username]['must_reset_password'] = false;
            $users[$username]['updated_at'] = nowIso();
        });
        mutateStore('sessions', [], function (array &$sessions) use ($username, $token) {
            foreach ($sessions as $key => $session) {
                if (($session['username'] ?? null) === $username && $key !== $token) {
                    unset($sessions[$key]);
                }
            }
        });
        respond(['ok' => true]);

    case 'reset_user_password':
        requireUser($payload['auth_token'] ?? '', true);
        $username = normalizeUsername($payload['username'] ?? '');
        $password = (string) ($payload['temporary_password'] ?? '');
        validateNewPassword($password);
        mutateStore('users', [], function (array &$users) use ($username, $password) {
            if (!isset($users[$username])) {
                fail('User not found.', 404);
            }
            $users[$username]['password_hash'] = password_hash($password, PASSWORD_DEFAULT);
            $users[$username]['must_reset_password'] = true;
            $users[$username]['updated_at'] = nowIso();
        });
        deleteSessionsForUser($username);
        respond(['ok' => true]);

    case 'set_user_active':
        list($admin) = requireUser($payload['auth_token'] ?? '', true);
        $username = normalizeUsername($payload['username'] ?? '');
        $isActive = filter_var($payload['is_active'] ?? false, FILTER_VALIDATE_BOOL);
        if ($username === $admin['username'] && !$isActive) {
            fail('You cannot disable your own account.', 400);
        }
        mutateStore('users', [], function (array &$users) use ($username, $isActive) {
            if (!isset($users[$username])) {
                fail('User not found.', 404);
            }
            if (!$isActive && !empty($users[$username]['is_admin'])) {
                $activeAdmins = count(array_filter($users, function ($user): bool {
                    return is_array($user) && !empty($user['is_admin']) && !empty($user['is_active']);
                }));
                if ($activeAdmins <= 1) {
                    fail('You cannot disable the last active administrator.', 400);
                }
            }
            $users[$username]['is_active'] = $isActive;
            $users[$username]['updated_at'] = nowIso();
        });
        if (!$isActive) {
            deleteSessionsForUser($username);
        }
        respond(['ok' => true]);

    case 'delete_user':
        list($admin) = requireUser($payload['auth_token'] ?? '', true);
        $username = normalizeUsername($payload['username'] ?? '');
        if ($username === $admin['username']) {
            fail('You cannot delete your own account.', 400);
        }
        mutateStore('users', [], function (array &$users) use ($username) {
            if (!isset($users[$username])) {
                fail('User not found.', 404);
            }
            if (!empty($users[$username]['is_admin'])) {
                $activeAdmins = count(array_filter($users, function ($user): bool {
                    return is_array($user) && !empty($user['is_admin']) && !empty($user['is_active']);
                }));
                if ($activeAdmins <= 1) {
                    fail('You cannot delete the last active administrator.', 400);
                }
            }
            unset($users[$username]);
        });
        deleteSessionsForUser($username);
        respond(['ok' => true]);

    default:
        fail('Unknown API action.', 404);
}
