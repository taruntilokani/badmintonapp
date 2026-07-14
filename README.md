# Badminton Tournament Manager - InfinityFree MySQL Edition

This folder is a complete PHP 8.3.x web app for hosting on InfinityFree or another shared PHP host. It uses MySQL for users, sessions, tournament data, scores, player lists, shuttle management, leaderboard data, and history.

No Composer install, Node build, or Docker container is required.

## Runtime

- Target PHP version: `8.3.19`
- Minimum supported runtime: PHP `8.3.x`
- Required PHP extension: `pdo_mysql`
- Database: MySQL on InfinityFree
- Initial login: `admin` / `admin`

Change the default password immediately after first login.

## Database Configuration

The live credentials are stored in `db-config.php`. This file is intentionally ignored by git.

The current configured database is:

```text
Database name: if0_42373389_badmintonleaderboard
MySQL user: if0_42373389
MySQL host: sql201.infinityfree.com
MySQL port: 3306
```

The app creates these tables automatically when `health.php`, `diagnose.php`, or `api.php` runs:

```text
bt_users
bt_sessions
bt_app_state
bt_tournaments
bt_tournament_matches
bt_player_lists
bt_app_settings
```

`bt_app_state` is kept only for automatic migration from older uploads. New writes use separate indexed tables for faster retrieval and safer multi-session updates:

- `bt_tournaments`: one row per tournament snapshot.
- `bt_tournament_matches`: one row per match score, used for fast score updates.
- `bt_player_lists`: one row per saved player list per account.
- `bt_app_settings`: small app preferences per account, such as active view, filters, shuttle settings, and indexes.

Ownership is enforced on the server. Regular users only export, load, update, and delete rows where `owner_username` matches their login. New users receive empty tournament and player-list indexes, so they start fresh even on a shared browser. Admin users can load all tournaments and player lists, and the dropdown labels include the row owner for clarity. Legacy unowned rows use a blank owner and remain admin-visible for migration.

When multiple sessions are logged in, session refresh/logout writes only touch `bt_sessions`, tournament saves only touch the relevant tournament rows, and score entry uses a small `patch_match_score` API call against the match-score table. The tournament snapshot still exists for compatibility, but exported data overlays the latest match-score rows before loading the app.

Player lists are account-level, not tournament-level. Adding or loading players inside a tournament also updates that account's player draft, so the next new tournament starts with the same roster and saved lists stay available for that same login.

The app uses write-through sync: tournament, player-list, score, and setting changes start a MySQL save immediately. Saves are queued per record so newer edits cannot be overwritten by older in-flight requests, and logout waits for the queue to finish. `health.php`, `diagnose.php`, and `api.php?action=ping` show row counts so you can confirm data is present in MySQL after saving.

When loading a tournament, the API also repairs the exported tournament payload from `bt_tournament_matches` if the tournament snapshot is missing match/schedule rows. This keeps the schedule recoverable from the normalized match table.

Player photos are optimized as small avatar files and saved under `uploads/player-photos/<username>/`. The app stores the photo URL in the player list/tournament data, which keeps login and database loading faster than storing large base64 images in MySQL. Upload the `uploads` folder structure with the app; the actual player image files are created on the server and are ignored by git.

## Files To Upload

Upload these files to your InfinityFree site root, usually `htdocs`:

```text
.htaccess
index.php
api.php
database.php
db-config.php
bs-optimized.png
uploads/
health.php
diagnose.php
VERSION.txt
```

Do not upload old generated `storage/*.php` files for the MySQL version.

`schema.sql` is included for reference or manual phpMyAdmin import. The app normally creates the tables automatically.

## InfinityFree Setup

1. Create or open your InfinityFree hosting account.
2. Confirm your MySQL database exists in the InfinityFree control panel.
3. Open the file manager or connect by FTP.
4. Go to your website root, normally `htdocs`.
5. Upload the files listed above.
6. In the hosting control panel, select PHP `8.3.19` or the available PHP `8.3.x` version.
7. Open:

   ```text
   https://your-site.example/health.php
   ```

8. Confirm the health page shows `READY`.
9. Open:

   ```text
   https://your-site.example/
   ```

10. Login with `admin` / `admin`.
11. Open **Users**, reset the admin password, then sign in with the new password.

## Troubleshooting

### Health page says `db-config.php` is missing

Upload `db-config.php` beside `index.php`, `api.php`, and `database.php`.

### Health page says `pdo_mysql` is missing

Confirm the site is using PHP 8.3.x in the InfinityFree control panel. InfinityFree PHP hosting normally includes MySQL/PDO support.

### Database connection fails

Check these values in `db-config.php`:

```text
host
port
database
username
password
```

Also confirm the database exists in the InfinityFree control panel.

### Login fails with a 405 or HTML error

Confirm `api.php` is uploaded beside `index.php`. The app expects same-folder API calls like:

```text
api.php?action=login_user
```

You can also open this URL directly in the browser:

```text
https://your-site.example/api.php?action=ping
```

It should return JSON with `"ok": true`.

### Tables are missing

Open:

```text
https://your-site.example/diagnose.php
```

The diagnostic page attempts to create/check the MySQL tables and prints the result.

## Backups

Use InfinityFree phpMyAdmin or the hosting control panel to export the MySQL database:

```text
if0_42373389_badmintonleaderboard
```

Back up `db-config.php` privately as well.

## Security Notes

- Do not leave the default `admin` / `admin` password active.
- Keep `db-config.php` private.
- Do not publish `db-config.php` or database exports to a public source repository.
- Use HTTPS for the live site.
