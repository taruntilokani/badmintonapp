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
```

## Files To Upload

Upload these files to your InfinityFree site root, usually `htdocs`:

```text
.htaccess
index.php
api.php
database.php
db-config.php
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
