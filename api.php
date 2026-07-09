<?php
declare(strict_types=1);

/*
 * Badminton Tournament Manager - Synology Web Station API
 *
 * This intentionally uses guarded PHP data files instead of requiring MariaDB.
 * It is suitable for a small club installation and keeps all browser clients in
 * sync through one NAS-hosted data store.
 */

const SESSION_IDLE_SECONDS = 120;
const MAX_ACTIVE_SESSIONS = 3;
const STORE_GUARD = "<?php http_response_code(403); exit; ?>\n";

header('Cache-Control: no-store, private');
header('X-Content-Type-Options: nosniff');

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'OPTIONS') {
    http_response_code(204);
    exit;
}

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
    fail('POST requests only.', 405);
}

$storageDirectory = __DIR__ . DIRECTORY_SEPARATOR . 'storage';
if (!is_dir($storageDirectory) && !mkdir($storageDirectory, 0770, true) && !is_dir($storageDirectory)) {
    fail('The storage directory could not be created. Check Web Station permissions.', 500);
}

function fail(string $message, int $status = 400)
{
    http_response_code($status);
    header('Content-Type: text/plain; charset=utf-8');
    echo $message;
    exit;
}

function respond($value = null, int $status = 200)
{
    http_response_code($status);
    header('Content-Type: application/json; charset=utf-8');
    echo jsonEncodeValue($value);
    exit;
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

function storePath(string $name): string
{
    global $storageDirectory;
    return $storageDirectory . DIRECTORY_SEPARATOR . $name . '.php';
}

function decodeStore(string $raw, array $default): array
{
    if (startsWith($raw, STORE_GUARD)) {
        $raw = substr($raw, strlen(STORE_GUARD));
    }
    if (trim($raw) === '') {
        return $default;
    }
    $decoded = json_decode($raw, true);
    return is_array($decoded) ? $decoded : $default;
}

function encodeStore(array $value): string
{
    return STORE_GUARD . jsonEncodeValue($value, JSON_PRETTY_PRINT);
}

function readStore(string $name, array $default): array
{
    $path = storePath($name);
    $handle = fopen($path, 'c+');
    if ($handle === false) {
        fail('Cannot open the Web Station data store. Check folder permissions.', 500);
    }
    try {
        if (!flock($handle, LOCK_SH)) {
            fail('Cannot lock the data store.', 500);
        }
        rewind($handle);
        $raw = stream_get_contents($handle);
        return decodeStore($raw === false ? '' : $raw, $default);
    } finally {
        flock($handle, LOCK_UN);
        fclose($handle);
    }
}

function mutateStore(string $name, array $default, callable $callback)
{
    $path = storePath($name);
    $handle = fopen($path, 'c+');
    if ($handle === false) {
        fail('Cannot open the Web Station data store. Check folder permissions.', 500);
    }
    try {
        if (!flock($handle, LOCK_EX)) {
            fail('Cannot lock the data store.', 500);
        }
        rewind($handle);
        $raw = stream_get_contents($handle);
        $value = decodeStore($raw === false ? '' : $raw, $default);
        $result = $callback($value);
        $encoded = encodeStore($value);
        rewind($handle);
        if (!ftruncate($handle, 0) || fwrite($handle, $encoded) === false || !fflush($handle)) {
            fail('Cannot save the Web Station data store. Check folder permissions.', 500);
        }
        return $result;
    } finally {
        flock($handle, LOCK_UN);
        fclose($handle);
    }
}

function initialUsers(): array
{
    $now = gmdate(DATE_ATOM);
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

function loadUsers(): array
{
    $users = readStore('users', []);
    if ($users !== []) {
        return $users;
    }
    mutateStore('users', [], function (array &$current) {
        if ($current === []) {
            $current = initialUsers();
        }
    });
    return readStore('users', []);
}

function normalizeUsername($username): string
{
    return strtolower(trim((string) $username));
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

function validateNewPassword(string $password)
{
    if (strlen($password) < 8) {
        fail('Password must contain at least 8 characters.', 400);
    }
}

function deleteSessionsForUser(string $username)
{
    mutateStore('sessions', [], function (array &$sessions) use ($username) {
        foreach ($sessions as $token => $session) {
            if (($session['username'] ?? null) === $username) {
                unset($sessions[$token]);
            }
        }
    });
}

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
            $sessions[$token] = ['username' => $username, 'expires_at' => $expiresAt];
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
            $now = gmdate(DATE_ATOM);
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
            $users[$username]['updated_at'] = gmdate(DATE_ATOM);
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
            $users[$username]['updated_at'] = gmdate(DATE_ATOM);
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
            $users[$username]['updated_at'] = gmdate(DATE_ATOM);
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
