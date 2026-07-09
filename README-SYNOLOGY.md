# Badminton Tournament Manager for Synology Web Station

This folder is a complete Web Station site. It contains the latest tournament interface, a PHP API, protected file-based NAS storage, multi-device synchronization, and administrator/user login support.

## Included files

- `index.html` - complete tournament application and responsive interface.
- `api.php` - login, sessions, users, and shared tournament data API.
- `storage/` - writable server data directory. Its guarded `.php` data files are created automatically on first use.
- `storage/.htaccess` and `storage/index.php` - direct-access protection for the data directory.

No database package, Docker container, Composer install, or internet connection is required.

## Requirements

- Synology DSM 7.x
- Web Station
- PHP 7.0 or newer for compatibility (PHP 8.2 is strongly recommended)
- Apache HTTP Server 2.4 or the Web Station Nginx backend

## Installation

1. In **Package Center**, install **Web Station**, **PHP 8.2**, and optionally **Apache HTTP Server 2.4**.
2. Copy the contents of the `synology-webstation` folder to your NAS destination:

   ```text
   /volume1/web/tournament/badminton
   ```

   In Synology File Station this normally appears as `/web/tournament/badminton`.

3. Give the Web Station service read/write permission to only the storage directory:

   ```text
   /volume1/web/tournament/badminton/storage
   ```

   In File Station, open **Properties > Permission**, add the local system user/group used by Web Station (normally `http`), and grant **Read** and **Write**. Apply the permission to this folder, subfolders, and files.

   From an SSH administrator shell, the equivalent is usually:

   ```bash
   sudo chown -R http:http /volume1/web/tournament/badminton/storage
   sudo chmod -R 770 /volume1/web/tournament/badminton/storage
   ```

4. Open **Web Station > Web Service Portal** and choose **Create > Web service portal**.
5. Select a **Native script language website** or equivalent PHP-enabled service.
6. Set its document root to `/volume1/web/tournament/badminton`, select the PHP 8.2 profile, and assign a hostname or port.
7. Save the portal and open the URL shown by Web Station.

Before logging in, open `health.php` in the same portal. For example:

```text
http://NAS-IP/tournament/badminton/health.php
```

It must show **READY**. If it shows source code or downloads the file, PHP is not enabled for the portal.

> PHP 7.0 is supported by this package for older Synology systems, but PHP 7.0 is end-of-life. Keep a PHP 7.0 installation restricted to your trusted LAN and upgrade to PHP 8.2 before exposing it to the internet.

If you use Web Station's default portal instead, placing the folder under `/volume1/web` normally makes it available at:

```text
http://NAS-IP/tournament/badminton/
```

## First login

```text
Username: admin
Password: admin
```

Change this default immediately, especially before making the site accessible outside your LAN:

1. Sign in as `admin`.
2. Open **Users**.
3. Click **Reset** beside the `admin` account and enter a temporary password of at least 8 characters.
4. Sign in with that temporary password and complete the forced password change.

## Data and backups

Application records are stored in guarded files under `storage/`. Back up the whole folder, including `storage`, using Hyper Backup or Snapshot Replication.

For a manual backup, copy these generated files while the site is idle:

- `storage/app-state.php`
- `storage/users.php`
- `storage/sessions.php` (optional; active sessions only)

Restoring `app-state.php` and `users.php` restores tournament data and user accounts. Passwords are stored as one-way PHP password hashes, never as plain text.

## HTTPS and remote access

Use **Control Panel > Login Portal > Advanced > Reverse Proxy** or a Web Station hostname portal, then assign a certificate in **Control Panel > Security > Certificate**. Do not expose the default `admin/admin` account to the internet.

For remote access, prefer Synology's reverse proxy with HTTPS, a VPN, or Tailscale. Avoid directly forwarding an unencrypted HTTP port from your router.

## Troubleshooting

### Login shows an error or data does not save

Open `health.php`. The usual cause is missing write permission on `storage`; confirm the Web Station `http` account can create files there. The health page also reports whether the selected PHP version is compatible.

### `api.php` downloads instead of running

The portal is using a static profile. Edit the portal and attach a PHP 8.x script-language profile.

For an older Synology installation with PHP 7.0, open **Web Station > General Settings** and select **PHP 7.0** as the PHP profile for the default server. On versions that show **Virtual Host**, edit the host serving `/web/tournament/badminton` and assign its PHP profile there. Merely installing the PHP 7.0 package is not enough; it must be assigned to the website.

### HTTP 500 from `api.php`

Upload and open `diagnose.php`:

```text
http://NAS-IP/tournament/badminton/diagnose.php
```

If that page also returns HTTP 500, PHP 7.0 is not correctly assigned or running for this Web Station portal. Check **Web Station > General Settings / Virtual Host**, select the PHP 7.0 profile, and review the Web Station/PHP logs.

If the page opens but **Actual file write test** says `FAIL`, grant the Synology Web Station `http` user/group read and write permission on `/web/tournament/badminton/storage`.

If the write test says `PASS`, replace `api.php` and `index.html` with the latest package copies and retry.

### Sessions expire

For safety, inactive sessions expire after two minutes, matching the tournament application's existing session behavior. Each account can have up to three active sessions.

## Updating the app later

Keep the generated files in `storage`. Replace `index.html` and `api.php` with newer package versions, then test login and a sample save. Never overwrite `storage/users.php` or `storage/app-state.php` during an update.
