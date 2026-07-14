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

const SESSION_IDLE_SECONDS = 300;
const MAX_ACTIVE_SESSIONS = 3;
const TOURNAMENT_KEY_PREFIX = 'bt_tournament_v1_';
const PLAYER_LIST_KEY_PREFIX = 'bt_playerlist_v1_';
const TOURNAMENTS_INDEX_KEY = 'bt_tournaments_index_v1';
const PLAYER_LISTS_INDEX_KEY = 'bt_playerlists_index_v1';
const PLAYER_PHOTO_PUBLIC_PREFIX = 'uploads/player-photos';
const PLAYER_PHOTO_SIZE = 144;
const PLAYER_PHOTO_MAX_UPLOAD_BYTES = 1048576;

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
        migrateLegacyAppState();
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

function findSessionByToken(string $token): ?array
{
    cleanupExpiredSessions();
    $statement = db()->prepare('SELECT username, expires_at FROM bt_sessions WHERE token = ? LIMIT 1');
    $statement->execute([$token]);
    $session = $statement->fetch();
    if (!is_array($session)) {
        return null;
    }
    return [
        'username' => (string) $session['username'],
        'expires_at' => (int) $session['expires_at'],
    ];
}

function countActiveSessionsForUser(string $username): int
{
    cleanupExpiredSessions();
    $statement = db()->prepare('SELECT COUNT(*) FROM bt_sessions WHERE username = ?');
    $statement->execute([$username]);
    return (int) $statement->fetchColumn();
}

function createSessionRow(string $token, string $username, int $expiresAt): void
{
    $statement = db()->prepare('
        INSERT INTO bt_sessions (token, username, expires_at, created_at)
        VALUES (?, ?, ?, ?)
    ');
    $statement->execute([$token, $username, $expiresAt, nowIso()]);
}

function updateSessionExpiry(string $token, int $expiresAt): void
{
    $statement = db()->prepare('UPDATE bt_sessions SET expires_at = ? WHERE token = ?');
    $statement->execute([$expiresAt, $token]);
}

function deleteSessionToken(string $token): void
{
    if ($token === '') {
        return;
    }
    $statement = db()->prepare('DELETE FROM bt_sessions WHERE token = ?');
    $statement->execute([$token]);
}

function deleteOtherSessionsForUser(string $username, string $keepToken): void
{
    $statement = db()->prepare('DELETE FROM bt_sessions WHERE username = ? AND token <> ?');
    $statement->execute([$username, $keepToken]);
}

function jsMillisecondsFromIso(string $value): int
{
    $timestamp = strtotime($value);
    return $timestamp === false ? 0 : $timestamp * 1000;
}

function boundedString($value, int $maxLength): string
{
    return substr(trim((string) $value), 0, $maxLength);
}

function databaseId($value, string $label): string
{
    $id = trim((string) $value);
    if ($id === '') {
        fail($label . ' is required.', 400);
    }
    if (strlen($id) > 191) {
        fail($label . ' is too long.', 400);
    }
    return $id;
}

function ownerUsername($username): string
{
    return substr(normalizeUsername($username), 0, 40);
}

function userOwnsAllData(array $user): bool
{
    return !empty($user['is_admin']);
}

function playerPhotoStorageRoot(): string
{
    return __DIR__ . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, PLAYER_PHOTO_PUBLIC_PREFIX);
}

function safePhotoOwnerSegment(array $user): string
{
    $owner = ownerUsername($user['username'] ?? '');
    $owner = preg_replace('/[^a-z0-9._-]+/', '-', $owner) ?: 'user';
    return substr(trim($owner, '.-'), 0, 48) ?: 'user';
}

function playerPhotoFilename(string $playerName): string
{
    $normalized = strtolower(trim($playerName));
    if ($normalized === '') {
        fail('Player name is required.', 400);
    }
    $slug = preg_replace('/[^a-z0-9]+/', '-', $normalized) ?: 'player';
    $slug = substr(trim($slug, '-'), 0, 48) ?: 'player';
    return $slug . '-' . substr(sha1($normalized), 0, 12) . '.jpg';
}

function ensurePlayerPhotoDirectory(array $user): string
{
    $root = playerPhotoStorageRoot();
    if (!is_dir($root) && !mkdir($root, 0755, true) && !is_dir($root)) {
        fail('Cannot create player photo storage folder.', 500);
    }
    $ownerDir = $root . DIRECTORY_SEPARATOR . safePhotoOwnerSegment($user);
    if (!is_dir($ownerDir) && !mkdir($ownerDir, 0755, true) && !is_dir($ownerDir)) {
        fail('Cannot create account player photo folder.', 500);
    }
    if (!is_writable($ownerDir)) {
        fail('Player photo folder is not writable.', 500);
    }
    return $ownerDir;
}

function decodePlayerPhotoDataUrl(string $dataUrl): string
{
    if (!preg_match('/^data:image\/(?:jpeg|jpg|png|webp);base64,([a-z0-9+\/=\r\n]+)$/i', trim($dataUrl), $matches)) {
        fail('A valid image data URL is required.', 400);
    }
    $binary = base64_decode($matches[1], true);
    if ($binary === false || $binary === '') {
        fail('The uploaded image could not be decoded.', 400);
    }
    if (strlen($binary) > PLAYER_PHOTO_MAX_UPLOAD_BYTES) {
        fail('The uploaded image is too large.', 413);
    }
    if (@getimagesizefromstring($binary) === false) {
        fail('The uploaded file is not a valid image.', 400);
    }
    return $binary;
}

function encodePlayerPhotoJpeg(string $binary): string
{
    if (!function_exists('imagecreatefromstring')) {
        return $binary;
    }
    $source = @imagecreatefromstring($binary);
    if (!$source) {
        return $binary;
    }

    $sourceWidth = imagesx($source);
    $sourceHeight = imagesy($source);
    $sourceSize = max(1, min($sourceWidth, $sourceHeight));
    $sourceX = (int) max(0, ($sourceWidth - $sourceSize) / 2);
    $sourceY = (int) max(0, ($sourceHeight - $sourceSize) / 2);

    $canvas = imagecreatetruecolor(PLAYER_PHOTO_SIZE, PLAYER_PHOTO_SIZE);
    $white = imagecolorallocate($canvas, 255, 255, 255);
    imagefill($canvas, 0, 0, $white);
    imagecopyresampled(
        $canvas,
        $source,
        0,
        0,
        $sourceX,
        $sourceY,
        PLAYER_PHOTO_SIZE,
        PLAYER_PHOTO_SIZE,
        $sourceSize,
        $sourceSize
    );

    ob_start();
    imagejpeg($canvas, null, 84);
    $jpeg = (string) ob_get_clean();
    imagedestroy($source);
    imagedestroy($canvas);
    return $jpeg !== '' ? $jpeg : $binary;
}

function playerPhotoPublicUrl(array $user, string $filename): string
{
    return PLAYER_PHOTO_PUBLIC_PREFIX . '/' . rawurlencode(safePhotoOwnerSegment($user)) . '/' . rawurlencode($filename) . '?v=' . time();
}

function savePlayerPhotoFile(array $user, string $playerName, string $dataUrl): array
{
    $directory = ensurePlayerPhotoDirectory($user);
    $filename = playerPhotoFilename($playerName);
    $binary = encodePlayerPhotoJpeg(decodePlayerPhotoDataUrl($dataUrl));
    $target = $directory . DIRECTORY_SEPARATOR . $filename;
    if (file_put_contents($target, $binary, LOCK_EX) === false) {
        fail('Could not save player photo.', 500);
    }
    @chmod($target, 0644);
    return [
        'ok' => true,
        'url' => playerPhotoPublicUrl($user, $filename),
        'bytes' => strlen($binary),
    ];
}

function deletePlayerPhotoFile(array $user, string $photoUrl, string $playerName = ''): void
{
    $filename = '';
    $path = parse_url($photoUrl, PHP_URL_PATH);
    if (is_string($path) && $path !== '') {
        $filename = rawurldecode(basename($path));
    }
    if ($filename === '' && trim($playerName) !== '') {
        $filename = playerPhotoFilename($playerName);
    }
    if (!preg_match('/^[a-z0-9][a-z0-9._-]*\.jpg$/', $filename)) {
        return;
    }

    $target = playerPhotoStorageRoot()
        . DIRECTORY_SEPARATOR
        . safePhotoOwnerSegment($user)
        . DIRECTORY_SEPARATOR
        . $filename;
    $resolvedTarget = realpath($target);
    $resolvedOwnerDir = realpath(playerPhotoStorageRoot() . DIRECTORY_SEPARATOR . safePhotoOwnerSegment($user));
    if (!$resolvedTarget || !$resolvedOwnerDir || strncmp($resolvedTarget, $resolvedOwnerDir, strlen($resolvedOwnerDir)) !== 0) {
        return;
    }
    if (is_file($resolvedTarget)) {
        @unlink($resolvedTarget);
    }
}

function decodeStoredArray(string $json): ?array
{
    $decoded = json_decode($json, true);
    return is_array($decoded) ? $decoded : null;
}

function nullableScore($value): ?int
{
    if ($value === null || $value === '') {
        return null;
    }
    if (!is_numeric($value)) {
        return null;
    }
    $score = (int) $value;
    return $score >= 0 && $score <= 32767 ? $score : null;
}

function requestScore($value): ?int
{
    if ($value === null || $value === '') {
        return null;
    }
    if (!is_numeric($value)) {
        fail('Score must be a number or blank.', 400);
    }
    $score = (int) $value;
    if ($score < 0 || $score > 32767) {
        fail('Score must be between 0 and 32767.', 400);
    }
    return $score;
}

function addTournamentMatch(array &$matches, $candidate): void
{
    if (!is_array($candidate)) {
        return;
    }
    $matchId = trim((string) ($candidate['id'] ?? ''));
    if ($matchId === '' || strlen($matchId) > 191) {
        return;
    }
    if (!matchHasParticipants($candidate)) {
        return;
    }
    $matches[$matchId] = $candidate;
}

function matchHasParticipants(array $match): bool
{
    return trim((string) ($match['team1'] ?? '')) !== ''
        || trim((string) ($match['team2'] ?? '')) !== '';
}

function tournamentMatches(array $tournament): array
{
    $matches = [];
    if (isset($tournament['matches']) && is_array($tournament['matches'])) {
        foreach ($tournament['matches'] as $match) {
            addTournamentMatch($matches, $match);
        }
    }
    addTournamentMatch($matches, $tournament['finalMatch'] ?? null);
    $knockout = $tournament['knockout'] ?? null;
    if (is_array($knockout)) {
        foreach (['semifinal1', 'semifinal2', 'qualifier1', 'eliminator', 'qualifier2', 'final'] as $key) {
            addTournamentMatch($matches, $knockout[$key] ?? null);
        }
    }
    return $matches;
}

function countScoredMatches(array $matches): int
{
    $count = 0;
    foreach ($matches as $match) {
        if (!is_array($match)) {
            continue;
        }
        if (nullableScore($match['score1'] ?? null) !== null || nullableScore($match['score2'] ?? null) !== null) {
            $count++;
        }
    }
    return $count;
}

function tournamentTeamDisplayName(array $tournament, $team): string
{
    $teamKey = trim((string) $team);
    if ($teamKey === '') {
        return '';
    }
    $teamPlayers = $tournament['teamPlayers'] ?? null;
    $players = is_array($teamPlayers) && isset($teamPlayers[$teamKey]) && is_array($teamPlayers[$teamKey])
        ? $teamPlayers[$teamKey]
        : [];
    $names = [];
    foreach ($players as $player) {
        $name = trim((string) $player);
        if ($name !== '') {
            $names[] = $name;
        }
    }
    return $names === [] ? $teamKey : implode(' / ', $names);
}

function storedMatchTeamKeys(array $row): array
{
    $stored = decodeStoredArray((string) ($row['data_json'] ?? '')) ?: [];
    return [
        (string) ($stored['team1'] ?? $row['team1'] ?? ''),
        (string) ($stored['team2'] ?? $row['team2'] ?? ''),
    ];
}

function matchParticipantsChanged(array $row, array $match): bool
{
    [$rowTeam1, $rowTeam2] = storedMatchTeamKeys($row);
    if ($rowTeam1 === '' && $rowTeam2 === '') {
        return false;
    }
    return $rowTeam1 !== (string) ($match['team1'] ?? '')
        || $rowTeam2 !== (string) ($match['team2'] ?? '');
}

function normalizeStoredMatchRow(array $row): array
{
    $match = decodeStoredArray((string) ($row['data_json'] ?? '')) ?: [];
    $match['id'] = (string) ($match['id'] ?? $row['match_id'] ?? '');
    $match['team1'] = (string) ($match['team1'] ?? $row['team1'] ?? '');
    $match['team2'] = (string) ($match['team2'] ?? $row['team2'] ?? '');
    $match['stage'] = (string) ($match['stage'] ?? $row['stage'] ?? '');
    if (!array_key_exists('groupIndex', $match)) {
        $match['groupIndex'] = $row['group_index'] === null ? null : (int) $row['group_index'];
    }
    if (!array_key_exists('knockoutRound', $match) && $row['knockout_round'] !== null) {
        $match['knockoutRound'] = (int) $row['knockout_round'];
    }
    $match['score1'] = $row['score1'] === null ? null : (int) $row['score1'];
    $match['score2'] = $row['score2'] === null ? null : (int) $row['score2'];
    return $match;
}

function matchSortValue(array $match): int
{
    $id = $match['id'] ?? '';
    return is_numeric($id) ? (int) $id : PHP_INT_MAX;
}

function applyStoredScoreToMatch(&$match, array $rowsById, bool $onlyFillMissing = false): void
{
    if (!is_array($match)) {
        return;
    }
    $matchId = trim((string) ($match['id'] ?? ''));
    if ($matchId === '' || !isset($rowsById[$matchId]) || matchParticipantsChanged($rowsById[$matchId], $match)) {
        return;
    }
    $row = $rowsById[$matchId];
    if (!$onlyFillMissing || !hasNumericScoreValue($match['score1'] ?? null)) {
        $match['score1'] = $row['score1'] === null ? null : (int) $row['score1'];
    }
    if (!$onlyFillMissing || !hasNumericScoreValue($match['score2'] ?? null)) {
        $match['score2'] = $row['score2'] === null ? null : (int) $row['score2'];
    }
}

function hasNumericScoreValue($value): bool
{
    return $value !== null && $value !== '' && is_numeric($value);
}

function overlayStoredMatchScores(array $tournament, string $tournamentId): array
{
    $statement = db()->prepare('
        SELECT match_id, stage, group_index, knockout_round, team1, team2, score1, score2, data_json
        FROM bt_tournament_matches
        WHERE tournament_id = ?
    ');
    $statement->execute([$tournamentId]);
    $rowsById = [];
    foreach ($statement->fetchAll() as $row) {
        $rowsById[(string) $row['match_id']] = $row;
    }
    if ($rowsById === []) {
        return $tournament;
    }

    $storedMatches = [];
    foreach ($rowsById as $matchId => $row) {
        $storedMatch = normalizeStoredMatchRow($row);
        if (matchHasParticipants($storedMatch)) {
            $storedMatches[$matchId] = $storedMatch;
        }
    }

    if (isset($tournament['matches']) && is_array($tournament['matches'])) {
        $cleanMatches = [];
        foreach ($tournament['matches'] as $match) {
            if (!is_array($match) || !matchHasParticipants($match)) {
                continue;
            }
            applyStoredScoreToMatch($match, $rowsById);
            $cleanMatches[] = $match;
            $matchId = (string) ($match['id'] ?? '');
            if ($matchId !== '') {
                unset($storedMatches[$matchId]);
            }
        }
        $tournament['matches'] = $cleanMatches;
    } else {
        $tournament['matches'] = [];
    }

    if ($storedMatches !== []) {
        foreach ($storedMatches as $storedMatch) {
            $tournament['matches'][] = $storedMatch;
        }
        usort($tournament['matches'], function (array $a, array $b): int {
            $left = matchSortValue($a);
            $right = matchSortValue($b);
            if ($left !== $right) {
                return $left <=> $right;
            }
            return strcmp((string) ($a['id'] ?? ''), (string) ($b['id'] ?? ''));
        });
    }

    if (isset($tournament['finalMatch']) && is_array($tournament['finalMatch'])) {
        $match = $tournament['finalMatch'];
        applyStoredScoreToMatch($match, $rowsById);
        $tournament['finalMatch'] = $match;
    }
    if (isset($tournament['knockout']) && is_array($tournament['knockout'])) {
        foreach (['semifinal1', 'semifinal2', 'qualifier1', 'eliminator', 'qualifier2', 'final'] as $key) {
            if (isset($tournament['knockout'][$key]) && is_array($tournament['knockout'][$key])) {
                $match = $tournament['knockout'][$key];
                applyStoredScoreToMatch($match, $rowsById);
                $tournament['knockout'][$key] = $match;
            }
        }
    }

    return $tournament;
}

function fillMissingTournamentScoresFromStoredRows(array $tournament, string $tournamentId): array
{
    $statement = db()->prepare('
        SELECT match_id, team1, team2, score1, score2, data_json
        FROM bt_tournament_matches
        WHERE tournament_id = ?
    ');
    $statement->execute([$tournamentId]);
    $rowsById = [];
    foreach ($statement->fetchAll() as $row) {
        $rowsById[(string) $row['match_id']] = $row;
    }
    if ($rowsById === []) {
        return $tournament;
    }

    if (isset($tournament['matches']) && is_array($tournament['matches'])) {
        foreach ($tournament['matches'] as $index => $match) {
            applyStoredScoreToMatch($match, $rowsById, true);
            $tournament['matches'][$index] = $match;
        }
    }
    if (isset($tournament['finalMatch']) && is_array($tournament['finalMatch'])) {
        $match = $tournament['finalMatch'];
        applyStoredScoreToMatch($match, $rowsById, true);
        $tournament['finalMatch'] = $match;
    }
    if (isset($tournament['knockout']) && is_array($tournament['knockout'])) {
        foreach (['semifinal1', 'semifinal2', 'qualifier1', 'eliminator', 'qualifier2', 'final'] as $key) {
            if (isset($tournament['knockout'][$key]) && is_array($tournament['knockout'][$key])) {
                $match = $tournament['knockout'][$key];
                applyStoredScoreToMatch($match, $rowsById, true);
                $tournament['knockout'][$key] = $match;
            }
        }
    }

    return $tournament;
}

function syncTournamentMatchRows(string $tournamentId, array $tournament): void
{
    $matches = tournamentMatches($tournament);
    $pdo = db();

    if ($matches === []) {
        $statement = $pdo->prepare('DELETE FROM bt_tournament_matches WHERE tournament_id = ?');
        $statement->execute([$tournamentId]);
        return;
    }

    $matchIds = array_keys($matches);
    $placeholders = implode(', ', array_fill(0, count($matchIds), '?'));
    $delete = $pdo->prepare("DELETE FROM bt_tournament_matches WHERE tournament_id = ? AND match_id NOT IN ($placeholders)");
    $delete->execute(array_merge([$tournamentId], $matchIds));

    $now = nowIso();
    $statement = $pdo->prepare('
        INSERT INTO bt_tournament_matches
            (tournament_id, match_id, stage, group_index, knockout_round, team1, team2, score1, score2, data_json, version, updated_at)
        VALUES
            (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 1, ?)
        ON DUPLICATE KEY UPDATE
            score1 = COALESCE(VALUES(score1), score1),
            score2 = COALESCE(VALUES(score2), score2),
            stage = VALUES(stage),
            group_index = VALUES(group_index),
            knockout_round = VALUES(knockout_round),
            team1 = VALUES(team1),
            team2 = VALUES(team2),
            data_json = VALUES(data_json),
            version = version + 1,
            updated_at = VALUES(updated_at)
    ');

    foreach ($matches as $matchId => $match) {
        $statement->execute([
            $tournamentId,
            $matchId,
            boundedString($match['stage'] ?? '', 80),
            isset($match['groupIndex']) && is_numeric($match['groupIndex']) ? (int) $match['groupIndex'] : null,
            isset($match['knockoutRound']) && is_numeric($match['knockoutRound']) ? (int) $match['knockoutRound'] : null,
            boundedString(tournamentTeamDisplayName($tournament, $match['team1'] ?? ''), 255),
            boundedString(tournamentTeamDisplayName($tournament, $match['team2'] ?? ''), 255),
            nullableScore($match['score1'] ?? null),
            nullableScore($match['score2'] ?? null),
            jsonEncodeValue($match),
            $now,
        ]);
    }
}

function tournamentExists(string $tournamentId): bool
{
    $statement = db()->prepare('SELECT 1 FROM bt_tournaments WHERE tournament_id = ? LIMIT 1');
    $statement->execute([$tournamentId]);
    return (bool) $statement->fetchColumn();
}

function tournamentOwner(string $tournamentId): ?string
{
    $statement = db()->prepare('SELECT owner_username FROM bt_tournaments WHERE tournament_id = ? LIMIT 1');
    $statement->execute([$tournamentId]);
    $owner = $statement->fetchColumn();
    return is_string($owner) ? $owner : null;
}

function requireTournamentAccess(array $user, string $tournamentId): string
{
    $tournamentId = databaseId($tournamentId, 'Tournament id');
    $owner = tournamentOwner($tournamentId);
    if ($owner === null) {
        fail('Tournament not found.', 404);
    }
    if (!userOwnsAllData($user) && $owner !== ownerUsername($user['username'] ?? '')) {
        fail('Tournament not found.', 404);
    }
    return $owner;
}

function playerListExists(string $ownerUsername, string $listId): bool
{
    $statement = db()->prepare('SELECT 1 FROM bt_player_lists WHERE owner_username = ? AND list_id = ? LIMIT 1');
    $statement->execute([ownerUsername($ownerUsername), $listId]);
    return (bool) $statement->fetchColumn();
}

function upsertTournamentRecord(string $ownerUsername, array $tournament, bool $deleteLegacy = true): int
{
    $ownerUsername = ownerUsername($ownerUsername);
    $tournamentId = databaseId($tournament['id'] ?? '', 'Tournament id');
    $tournament = fillMissingTournamentScoresFromStoredRows($tournament, $tournamentId);
    $name = boundedString($tournament['name'] ?? 'Untitled Tournament', 255) ?: 'Untitled Tournament';
    $scheduledDate = boundedString($tournament['scheduledDate'] ?? '', 32);
    $dataJson = jsonEncodeValue($tournament);
    $now = nowIso();
    $hash = hash('sha256', $dataJson);

    $statement = db()->prepare('
        INSERT INTO bt_tournaments
            (owner_username, tournament_id, name, scheduled_date, data_json, data_hash, version, created_at, updated_at)
        VALUES
            (?, ?, ?, ?, ?, ?, 1, ?, ?)
        ON DUPLICATE KEY UPDATE
            owner_username = VALUES(owner_username),
            name = VALUES(name),
            scheduled_date = VALUES(scheduled_date),
            data_json = VALUES(data_json),
            data_hash = VALUES(data_hash),
            version = version + 1,
            updated_at = VALUES(updated_at)
    ');
    $statement->execute([$ownerUsername, $tournamentId, $name, $scheduledDate, $dataJson, $hash, $now, $now]);
    syncTournamentMatchRows($tournamentId, $tournament);

    if ($deleteLegacy) {
        $legacyKey = TOURNAMENT_KEY_PREFIX . $tournamentId;
        db()->prepare('DELETE FROM bt_app_state WHERE storage_key = ?')->execute([$legacyKey]);
        db()->prepare('DELETE FROM bt_app_settings WHERE owner_username = ? AND storage_key = ?')->execute([$ownerUsername, $legacyKey]);
    }

    $version = db()->prepare('SELECT version FROM bt_tournaments WHERE tournament_id = ?');
    $version->execute([$tournamentId]);
    return (int) $version->fetchColumn();
}

function saveTournamentForUser(array $user, array $tournament): int
{
    $tournamentId = databaseId($tournament['id'] ?? '', 'Tournament id');
    $ownerUsername = ownerUsername($user['username'] ?? '');
    $existingOwner = tournamentOwner($tournamentId);
    if ($existingOwner !== null) {
        if (!userOwnsAllData($user) && $existingOwner !== $ownerUsername) {
            fail('Tournament not found.', 404);
        }
        $ownerUsername = userOwnsAllData($user) ? $existingOwner : $ownerUsername;
    }
    return upsertTournamentRecord($ownerUsername, $tournament);
}

function deleteTournamentRecord(array $user, string $tournamentId): void
{
    $tournamentId = databaseId($tournamentId, 'Tournament id');
    $ownerUsername = requireTournamentAccess($user, $tournamentId);
    $pdo = db();
    $pdo->prepare('DELETE FROM bt_tournament_matches WHERE tournament_id = ?')->execute([$tournamentId]);
    $pdo->prepare('DELETE FROM bt_tournaments WHERE tournament_id = ?')->execute([$tournamentId]);
    $legacyKey = TOURNAMENT_KEY_PREFIX . $tournamentId;
    $pdo->prepare('DELETE FROM bt_app_state WHERE storage_key = ?')->execute([$legacyKey]);
    $pdo->prepare('DELETE FROM bt_app_settings WHERE owner_username = ? AND storage_key = ?')->execute([$ownerUsername, $legacyKey]);
}

function upsertPlayerListRecord(string $ownerUsername, string $storageKey, array $value, bool $deleteLegacy = true): int
{
    if (!startsWith($storageKey, PLAYER_LIST_KEY_PREFIX)) {
        fail('A valid player list key is required.', 400);
    }
    $ownerUsername = ownerUsername($ownerUsername);
    $listId = databaseId(substr($storageKey, strlen(PLAYER_LIST_KEY_PREFIX)), 'Player list id');
    $name = boundedString($value['name'] ?? $listId, 255) ?: $listId;
    $players = isset($value['players']) && is_array($value['players']) ? $value['players'] : [];
    $playerCount = min(count($players), 65535);
    $dataJson = jsonEncodeValue($value);
    $now = nowIso();
    $hash = hash('sha256', $dataJson);

    $statement = db()->prepare('
        INSERT INTO bt_player_lists
            (owner_username, list_id, name, player_count, data_json, data_hash, version, created_at, updated_at)
        VALUES
            (?, ?, ?, ?, ?, ?, 1, ?, ?)
        ON DUPLICATE KEY UPDATE
            name = VALUES(name),
            player_count = VALUES(player_count),
            data_json = VALUES(data_json),
            data_hash = VALUES(data_hash),
            version = version + 1,
            updated_at = VALUES(updated_at)
    ');
    $statement->execute([$ownerUsername, $listId, $name, $playerCount, $dataJson, $hash, $now, $now]);

    if ($deleteLegacy) {
        db()->prepare('DELETE FROM bt_app_state WHERE storage_key = ?')->execute([$storageKey]);
        db()->prepare('DELETE FROM bt_app_settings WHERE owner_username = ? AND storage_key = ?')->execute([$ownerUsername, $storageKey]);
    }

    $version = db()->prepare('SELECT version FROM bt_player_lists WHERE owner_username = ? AND list_id = ?');
    $version->execute([$ownerUsername, $listId]);
    return (int) $version->fetchColumn();
}

function deletePlayerListRecord(string $ownerUsername, string $listId): void
{
    $ownerUsername = ownerUsername($ownerUsername);
    $listId = databaseId($listId, 'Player list id');
    $storageKey = PLAYER_LIST_KEY_PREFIX . $listId;
    $pdo = db();
    $pdo->prepare('DELETE FROM bt_player_lists WHERE owner_username = ? AND list_id = ?')->execute([$ownerUsername, $listId]);
    if ($ownerUsername === '') {
        $pdo->prepare('DELETE FROM bt_app_state WHERE storage_key = ?')->execute([$storageKey]);
        $pdo->prepare('DELETE FROM bt_app_settings WHERE owner_username = ? AND storage_key = ?')->execute([$ownerUsername, $storageKey]);
    }
}

function upsertAppSetting(string $ownerUsername, string $key, string $value): void
{
    $ownerUsername = ownerUsername($ownerUsername);
    $key = databaseId($key, 'Storage key');
    $statement = db()->prepare('
        INSERT INTO bt_app_settings (owner_username, storage_key, storage_value, updated_at)
        VALUES (?, ?, ?, ?)
        ON DUPLICATE KEY UPDATE
            storage_value = VALUES(storage_value),
            updated_at = VALUES(updated_at)
    ');
    $statement->execute([$ownerUsername, $key, $value, nowIso()]);
    db()->prepare('DELETE FROM bt_app_state WHERE storage_key = ?')->execute([$key]);
}

function deleteAppSetting(string $ownerUsername, string $key): void
{
    $ownerUsername = ownerUsername($ownerUsername);
    $key = databaseId($key, 'Storage key');
    db()->prepare('DELETE FROM bt_app_settings WHERE owner_username = ? AND storage_key = ?')->execute([$ownerUsername, $key]);
    if ($ownerUsername === '') {
        db()->prepare('DELETE FROM bt_app_state WHERE storage_key = ?')->execute([$key]);
    }
}

function upsertAppStateKey(string $key, string $value): void
{
    upsertAppSetting('', $key, $value);
}

function deleteAppStateKey(string $key): void
{
    deleteAppSetting('', $key);
}

function applyScorePatchToTournament(array &$tournament, string $matchId, string $scoreKey, ?int $scoreValue): bool
{
    $updated = false;
    if (isset($tournament['matches']) && is_array($tournament['matches'])) {
        foreach ($tournament['matches'] as $index => $match) {
            if (is_array($match) && (string) ($match['id'] ?? '') === $matchId) {
                $match[$scoreKey] = $scoreValue;
                $tournament['matches'][$index] = $match;
                $updated = true;
            }
        }
    }
    if (isset($tournament['finalMatch']) && is_array($tournament['finalMatch']) && (string) ($tournament['finalMatch']['id'] ?? '') === $matchId) {
        $tournament['finalMatch'][$scoreKey] = $scoreValue;
        $updated = true;
    }
    if (isset($tournament['knockout']) && is_array($tournament['knockout'])) {
        foreach (['semifinal1', 'semifinal2', 'qualifier1', 'eliminator', 'qualifier2', 'final'] as $key) {
            if (isset($tournament['knockout'][$key]) && is_array($tournament['knockout'][$key]) && (string) ($tournament['knockout'][$key]['id'] ?? '') === $matchId) {
                $tournament['knockout'][$key][$scoreKey] = $scoreValue;
                $updated = true;
            }
        }
    }
    return $updated;
}

function patchTournamentMatchScore(array $user, string $tournamentId, string $matchId, int $scoreSide, ?int $scoreValue): int
{
    $tournamentId = databaseId($tournamentId, 'Tournament id');
    $matchId = databaseId($matchId, 'Match id');
    if ($scoreSide !== 1 && $scoreSide !== 2) {
        fail('Score side must be 1 or 2.', 400);
    }
    $scoreColumn = $scoreSide === 1 ? 'score1' : 'score2';
    $scoreKey = $scoreColumn;
    $pdo = db();

    try {
        $pdo->beginTransaction();
        $statement = $pdo->prepare('SELECT owner_username, data_json FROM bt_tournaments WHERE tournament_id = ? FOR UPDATE');
        $statement->execute([$tournamentId]);
        $row = $statement->fetch();
        $dataJson = is_array($row) ? (string) ($row['data_json'] ?? '') : '';
        if (!is_string($dataJson) || $dataJson === '') {
            $pdo->rollBack();
            fail('Tournament not found.', 404);
        }
        if (!userOwnsAllData($user) && (string) ($row['owner_username'] ?? '') !== ownerUsername($user['username'] ?? '')) {
            $pdo->rollBack();
            fail('Tournament not found.', 404);
        }

        $tournament = decodeStoredArray($dataJson);
        if (!is_array($tournament)) {
            $pdo->rollBack();
            fail('Stored tournament data is invalid.', 500);
        }

        $rowExists = $pdo->prepare('SELECT 1 FROM bt_tournament_matches WHERE tournament_id = ? AND match_id = ? LIMIT 1');
        $rowExists->execute([$tournamentId, $matchId]);
        $hasMatchRow = (bool) $rowExists->fetchColumn();

        $tournament = fillMissingTournamentScoresFromStoredRows($tournament, $tournamentId);
        $matchedTournamentJson = applyScorePatchToTournament($tournament, $matchId, $scoreKey, $scoreValue);
        if (!$matchedTournamentJson && !$hasMatchRow) {
            $pdo->rollBack();
            fail('Match not found.', 404);
        }

        if ($matchedTournamentJson) {
            syncTournamentMatchRows($tournamentId, $tournament);
        }

        $updateScore = $pdo->prepare("UPDATE bt_tournament_matches SET $scoreColumn = ?, version = version + 1, updated_at = ? WHERE tournament_id = ? AND match_id = ?");
        $updateScore->execute([$scoreValue, nowIso(), $tournamentId, $matchId]);

        if ($matchedTournamentJson) {
            $dataJson = jsonEncodeValue($tournament);
            $updateTournament = $pdo->prepare('
                UPDATE bt_tournaments
                SET data_json = ?, data_hash = ?, version = version + 1, updated_at = ?
                WHERE tournament_id = ?
            ');
            $updateTournament->execute([$dataJson, hash('sha256', $dataJson), nowIso(), $tournamentId]);
        }

        $version = $pdo->prepare('SELECT version FROM bt_tournaments WHERE tournament_id = ?');
        $version->execute([$tournamentId]);
        $newVersion = (int) $version->fetchColumn();
        $pdo->commit();
        return $newVersion;
    } catch (Throwable $error) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        fail('Cannot patch match score: ' . $error->getMessage(), 500);
    }
}

function syncTournamentMatchScoreBatch(array $user, string $tournamentId, array $scores): int
{
    $tournamentId = databaseId($tournamentId, 'Tournament id');
    $pdo = db();

    try {
        $pdo->beginTransaction();
        $statement = $pdo->prepare('SELECT owner_username, data_json FROM bt_tournaments WHERE tournament_id = ? FOR UPDATE');
        $statement->execute([$tournamentId]);
        $row = $statement->fetch();
        $dataJson = is_array($row) ? (string) ($row['data_json'] ?? '') : '';
        if (!is_string($dataJson) || $dataJson === '') {
            $pdo->rollBack();
            fail('Tournament not found.', 404);
        }
        if (!userOwnsAllData($user) && (string) ($row['owner_username'] ?? '') !== ownerUsername($user['username'] ?? '')) {
            $pdo->rollBack();
            fail('Tournament not found.', 404);
        }

        $tournament = decodeStoredArray($dataJson);
        if (!is_array($tournament)) {
            $pdo->rollBack();
            fail('Stored tournament data is invalid.', 500);
        }

        $now = nowIso();
        $update = $pdo->prepare('
            UPDATE bt_tournament_matches
            SET
                score1 = COALESCE(?, score1),
                score2 = COALESCE(?, score2),
                version = version + 1,
                updated_at = ?
            WHERE tournament_id = ? AND match_id = ?
        ');

        $updated = 0;
        foreach ($scores as $score) {
            if (!is_array($score)) {
                continue;
            }
            $matchId = trim((string) ($score['match_id'] ?? $score['id'] ?? ''));
            if ($matchId === '' || strlen($matchId) > 191) {
                continue;
            }
            $score1 = nullableScore($score['score1'] ?? null);
            $score2 = nullableScore($score['score2'] ?? null);
            if ($score1 === null && $score2 === null) {
                continue;
            }

            $update->execute([$score1, $score2, $now, $tournamentId, $matchId]);
            if ($update->rowCount() > 0) {
                $updated++;
                if ($score1 !== null) {
                    applyScorePatchToTournament($tournament, $matchId, 'score1', $score1);
                }
                if ($score2 !== null) {
                    applyScorePatchToTournament($tournament, $matchId, 'score2', $score2);
                }
            }
        }

        if ($updated > 0) {
            $dataJson = jsonEncodeValue($tournament);
            $updateTournament = $pdo->prepare('
                UPDATE bt_tournaments
                SET data_json = ?, data_hash = ?, version = version + 1, updated_at = ?
                WHERE tournament_id = ?
            ');
            $updateTournament->execute([$dataJson, hash('sha256', $dataJson), $now, $tournamentId]);
        }

        $version = $pdo->prepare('SELECT version FROM bt_tournaments WHERE tournament_id = ?');
        $version->execute([$tournamentId]);
        $newVersion = (int) $version->fetchColumn();
        $pdo->commit();
        return $newVersion;
    } catch (Throwable $error) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        fail('Cannot sync match scores: ' . $error->getMessage(), 500);
    }
}

function migrateLegacyAppState(): void
{
    static $ran = false;
    if ($ran) {
        return;
    }
    $ran = true;

    $pdo = db();
    $statement = $pdo->query('SELECT storage_key, storage_value FROM bt_app_state');
    foreach ($statement->fetchAll() as $row) {
        $key = (string) $row['storage_key'];
        $value = (string) $row['storage_value'];
        if (startsWith($key, TOURNAMENT_KEY_PREFIX)) {
            $tournament = decodeStoredArray($value);
            $tournamentId = trim((string) ($tournament['id'] ?? substr($key, strlen(TOURNAMENT_KEY_PREFIX))));
            if (is_array($tournament) && $tournamentId !== '' && strlen($tournamentId) <= 191 && !tournamentExists($tournamentId)) {
                $tournament['id'] = $tournamentId;
                upsertTournamentRecord('', $tournament, false);
            }
            continue;
        }
        if (startsWith($key, PLAYER_LIST_KEY_PREFIX)) {
            $list = decodeStoredArray($value);
            $listId = trim(substr($key, strlen(PLAYER_LIST_KEY_PREFIX)));
            if (is_array($list) && $listId !== '' && strlen($listId) <= 191 && !playerListExists('', $listId)) {
                upsertPlayerListRecord('', $key, $list, false);
            }
            continue;
        }
        $insert = $pdo->prepare('
            INSERT IGNORE INTO bt_app_settings (owner_username, storage_key, storage_value, updated_at)
            VALUES (?, ?, ?, ?)
        ');
        $insert->execute(['', $key, $value, nowIso()]);
    }
}

function exportApplicationState(array $default, ?array $user = null): array
{
    $pdo = db();
    $ownerUsername = is_array($user) ? ownerUsername($user['username'] ?? '') : null;
    $canSeeAll = is_array($user) && userOwnsAllData($user);
    $state = [];

    if ($canSeeAll) {
        $legacy = $pdo->query('SELECT storage_key, storage_value FROM bt_app_state');
        foreach ($legacy->fetchAll() as $row) {
            $state[(string) $row['storage_key']] = (string) $row['storage_value'];
        }
    }

    if ($ownerUsername === null) {
        $settingRows = [];
    } elseif ($canSeeAll) {
        $settings = $pdo->prepare('
            SELECT storage_key, storage_value, owner_username
            FROM bt_app_settings
            WHERE owner_username = "" OR owner_username = ?
            ORDER BY owner_username ASC
        ');
        $settings->execute([$ownerUsername]);
        $settingRows = $settings->fetchAll();
    } else {
        $settings = $pdo->prepare('
            SELECT storage_key, storage_value, owner_username
            FROM bt_app_settings
            WHERE owner_username = ?
            ORDER BY owner_username ASC
        ');
        $settings->execute([$ownerUsername]);
        $settingRows = $settings->fetchAll();
    }
    foreach ($settingRows as $row) {
        $state[(string) $row['storage_key']] = (string) $row['storage_value'];
    }

    $tournamentIndex = [];
    if ($canSeeAll) {
        $tournaments = $pdo->query('
            SELECT owner_username, tournament_id, name, scheduled_date, data_json, version, created_at, updated_at
            FROM bt_tournaments
            ORDER BY updated_at DESC
        ');
        $tournamentRows = $tournaments->fetchAll();
    } else {
        $tournaments = $pdo->prepare('
            SELECT owner_username, tournament_id, name, scheduled_date, data_json, version, created_at, updated_at
            FROM bt_tournaments
            WHERE owner_username = ?
            ORDER BY updated_at DESC
        ');
        $tournaments->execute([$ownerUsername]);
        $tournamentRows = $tournaments->fetchAll();
    }
    foreach ($tournamentRows as $row) {
        $tournamentId = (string) $row['tournament_id'];
        $tournament = decodeStoredArray((string) $row['data_json']);
        if (!is_array($tournament)) {
            continue;
        }
        $tournament['id'] = (string) ($tournament['id'] ?? $tournamentId);
        $tournament = overlayStoredMatchScores($tournament, $tournamentId);
        $state[TOURNAMENT_KEY_PREFIX . $tournamentId] = jsonEncodeValue($tournament);
        $tournamentIndex[] = [
            'id' => $tournamentId,
            'name' => (string) ($tournament['name'] ?? $row['name']),
            'ownerUsername' => (string) ($row['owner_username'] ?? ''),
            'scheduledDate' => (string) ($tournament['scheduledDate'] ?? $row['scheduled_date']),
            'createdAt' => jsMillisecondsFromIso((string) $row['created_at']),
            'updatedAt' => jsMillisecondsFromIso((string) $row['updated_at']),
            'version' => (int) $row['version'],
        ];
    }
    $state[TOURNAMENTS_INDEX_KEY] = jsonEncodeValue($tournamentIndex);

    $playerListsById = [];
    if ($canSeeAll) {
        $playerLists = $pdo->query('
            SELECT owner_username, list_id, name, data_json
            FROM bt_player_lists
            ORDER BY owner_username ASC, name ASC, list_id ASC
        ');
        $playerListRows = $playerLists->fetchAll();
    } else {
        $playerLists = $pdo->prepare('
            SELECT owner_username, list_id, name, data_json
            FROM bt_player_lists
            WHERE owner_username = ?
            ORDER BY owner_username ASC, name ASC, list_id ASC
        ');
        $playerLists->execute([$ownerUsername]);
        $playerListRows = $playerLists->fetchAll();
    }
    foreach ($playerListRows as $row) {
        $listId = (string) $row['list_id'];
        if ($listId === '') {
            continue;
        }
        $rowOwner = (string) ($row['owner_username'] ?? '');
        $exportListId = $canSeeAll && $rowOwner !== '' ? $rowOwner . '__' . $listId : $listId;
        $playerListsById[$exportListId] = [
            'name' => (string) $row['name'],
            'owner_username' => $rowOwner,
            'data_json' => (string) $row['data_json'],
        ];
    }
    uasort($playerListsById, function (array $a, array $b): int {
        return strcasecmp($a['name'], $b['name']);
    });

    $playerListIndex = [];
    foreach ($playerListsById as $listId => $row) {
        $list = decodeStoredArray((string) $row['data_json']);
        if (is_array($list) && $canSeeAll) {
            $list['ownerUsername'] = $row['owner_username'];
            $state[PLAYER_LIST_KEY_PREFIX . $listId] = jsonEncodeValue($list);
        } else {
            $state[PLAYER_LIST_KEY_PREFIX . $listId] = $row['data_json'];
        }
        $playerListIndex[] = (string) $listId;
    }
    $state[PLAYER_LISTS_INDEX_KEY] = jsonEncodeValue($playerListIndex);

    return $state ?: $default;
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
        return $default;
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
        $pdo->exec('DELETE FROM bt_tournament_matches');
        $pdo->exec('DELETE FROM bt_tournaments');
        $pdo->exec('DELETE FROM bt_player_lists');
        $pdo->exec('DELETE FROM bt_app_settings');
        foreach ($value as $key => $storageValue) {
            $key = trim((string) $key);
            if ($key === '') {
                continue;
            }
            if (startsWith($key, TOURNAMENT_KEY_PREFIX)) {
                $tournament = decodeStoredArray((string) $storageValue);
                if (is_array($tournament)) {
                    $tournament['id'] = (string) ($tournament['id'] ?? substr($key, strlen(TOURNAMENT_KEY_PREFIX)));
                    upsertTournamentRecord('', $tournament, false);
                }
                continue;
            }
            if (startsWith($key, PLAYER_LIST_KEY_PREFIX)) {
                $list = decodeStoredArray((string) $storageValue);
                if (is_array($list)) {
                    upsertPlayerListRecord('', $key, $list, false);
                }
                continue;
            }
            upsertAppSetting('', $key, (string) $storageValue);
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

    $session = findSessionByToken($token);
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
    $statement = db()->prepare('DELETE FROM bt_sessions WHERE username = ?');
    $statement->execute([$username]);
}

$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
$action = trim((string) ($_GET['action'] ?? ''));

if (version_compare(PHP_VERSION, MINIMUM_PHP_VERSION, '<')) {
    fail('PHP ' . MINIMUM_PHP_VERSION . '+ is required. Configure the hosting account for PHP ' . TARGET_PHP_VERSION . '.', 500);
}

if ($method === 'GET' && $action === 'ping') {
    initializeDatabase();
    respond([
        'ok' => true,
        'api' => 'Badminton Tournament Manager',
        'phpVersion' => PHP_VERSION,
        'databaseConnected' => true,
        'tables' => [
            'bt_users' => btTableExists(db(), 'bt_users'),
            'bt_sessions' => btTableExists(db(), 'bt_sessions'),
            'bt_app_state' => btTableExists(db(), 'bt_app_state'),
            'bt_tournaments' => btTableExists(db(), 'bt_tournaments'),
            'bt_tournament_matches' => btTableExists(db(), 'bt_tournament_matches'),
            'bt_player_lists' => btTableExists(db(), 'bt_player_lists'),
            'bt_app_settings' => btTableExists(db(), 'bt_app_settings'),
        ],
        'rowCounts' => [
            'bt_users' => (int) db()->query('SELECT COUNT(*) FROM bt_users')->fetchColumn(),
            'bt_tournaments' => (int) db()->query('SELECT COUNT(*) FROM bt_tournaments')->fetchColumn(),
            'bt_tournament_matches' => (int) db()->query('SELECT COUNT(*) FROM bt_tournament_matches')->fetchColumn(),
            'bt_player_lists' => (int) db()->query('SELECT COUNT(*) FROM bt_player_lists')->fetchColumn(),
            'bt_app_settings' => (int) db()->query('SELECT COUNT(*) FROM bt_app_settings')->fetchColumn(),
        ],
    ]);
}

if ($method === 'OPTIONS') {
    http_response_code(204);
    exit;
}

if ($method !== 'POST') {
    fail('POST requests only.', 405);
}

initializeDatabase();

$payload = requestPayload();

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
        if (countActiveSessionsForUser($username) >= MAX_ACTIVE_SESSIONS) {
            fail('Login session limit exceeded.', 409);
        }
        createSessionRow($token, $username, $expiresAt);
        respond(sessionResponse($user, $token, $expiresAt));

    case 'logout_user':
        $token = trim((string) ($payload['auth_token'] ?? ''));
        deleteSessionToken($token);
        respond(['ok' => true]);

    case 'refresh_session':
        list($user, $token) = requireUser($payload['auth_token'] ?? '');
        $expiresAt = time() + SESSION_IDLE_SECONDS;
        updateSessionExpiry($token, $expiresAt);
        respond(['expiresAt' => gmdate(DATE_ATOM, $expiresAt)]);

    case 'export_app_state':
        list($user) = requireUser($payload['auth_token'] ?? '');
        respond(exportApplicationState([], $user));

    case 'save_tournament':
        list($user) = requireUser($payload['auth_token'] ?? '');
        $tournament = $payload['payload'] ?? null;
        if (!is_array($tournament) || trim((string) ($tournament['id'] ?? '')) === '') {
            fail('A tournament payload with an id is required.', 400);
        }
        $version = saveTournamentForUser($user, $tournament);
        $matches = tournamentMatches($tournament);
        respond([
            'ok' => true,
            'version' => $version,
            'receivedMatches' => count($matches),
            'receivedScoredMatches' => countScoredMatches($matches),
        ]);

    case 'patch_match_score':
        list($user) = requireUser($payload['auth_token'] ?? '');
        $version = patchTournamentMatchScore(
            $user,
            (string) ($payload['tournament_id'] ?? ''),
            (string) ($payload['match_id'] ?? ''),
            (int) ($payload['score_side'] ?? 0),
            requestScore($payload['score_value'] ?? null)
        );
        respond(['ok' => true, 'version' => $version]);

    case 'save_match_scores':
        list($user) = requireUser($payload['auth_token'] ?? '');
        $scores = $payload['scores'] ?? null;
        if (!is_array($scores)) {
            fail('A score list is required.', 400);
        }
        $version = syncTournamentMatchScoreBatch($user, (string) ($payload['tournament_id'] ?? ''), $scores);
        respond(['ok' => true, 'version' => $version, 'updated' => count($scores)]);

    case 'delete_tournament':
        list($user) = requireUser($payload['auth_token'] ?? '');
        deleteTournamentRecord($user, trim((string) ($payload['tournament_id'] ?? '')));
        respond(['ok' => true]);

    case 'upload_player_photo':
        list($user) = requireUser($payload['auth_token'] ?? '');
        respond(savePlayerPhotoFile(
            $user,
            trim((string) ($payload['player_name'] ?? '')),
            (string) ($payload['image_data'] ?? '')
        ));

    case 'delete_player_photo':
        list($user) = requireUser($payload['auth_token'] ?? '');
        deletePlayerPhotoFile(
            $user,
            trim((string) ($payload['photo_url'] ?? '')),
            trim((string) ($payload['player_name'] ?? ''))
        );
        respond(['ok' => true]);

    case 'save_player_list':
        list($user) = requireUser($payload['auth_token'] ?? '');
        $key = trim((string) ($payload['storage_key'] ?? ''));
        $value = $payload['payload'] ?? null;
        if (!startsWith($key, 'bt_playerlist_v1_') || !is_array($value)) {
            fail('A valid player list is required.', 400);
        }
        $version = upsertPlayerListRecord((string) $user['username'], $key, $value);
        respond(['ok' => true, 'version' => $version]);

    case 'delete_player_list':
        list($user) = requireUser($payload['auth_token'] ?? '');
        deletePlayerListRecord((string) $user['username'], trim((string) ($payload['player_list_id'] ?? '')));
        respond(['ok' => true]);

    case 'save_app_setting':
        list($user) = requireUser($payload['auth_token'] ?? '');
        $key = trim((string) ($payload['storage_key'] ?? ''));
        if (!startsWith($key, 'bt_')) {
            fail('Invalid application storage key.', 400);
        }
        $value = (string) ($payload['storage_value'] ?? '');
        upsertAppSetting((string) $user['username'], $key, $value);
        respond(['ok' => true]);

    case 'delete_app_setting':
        list($user) = requireUser($payload['auth_token'] ?? '');
        $key = trim((string) ($payload['storage_key'] ?? ''));
        deleteAppSetting((string) $user['username'], $key);
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
        deleteOtherSessionsForUser($username, $token);
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
