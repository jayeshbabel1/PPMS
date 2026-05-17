# Aaraji Registry — Setup & Hosting Guide

## Requirements
- PHP 8.0+ with extensions: `pdo`, `pdo_mysql`, `fileinfo`, `mbstring`
- MariaDB 10.5+ or MySQL 8+
- Apache with `mod_rewrite` enabled (or Nginx)

---

## 1. Database Setup

Login to MariaDB and run the schema:

```bash
mysql -u root -p < database.sql
```

This creates:
- Database: `aaraji_registry`
- Tables: `users`, `plans`, `revenue_villages`, `audit_log`
- Default admin: username `admin`, password `admin@123`

---

## 2. Configure Database Connection

Edit `includes/config.php`:

```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'aaraji_registry');
define('DB_USER', 'aaraji_user');      // your DB username
define('DB_PASS', 'YOUR_DB_PASSWORD'); // your DB password
```

Create a dedicated DB user (recommended):

```sql
CREATE USER 'aaraji_user'@'localhost' IDENTIFIED BY 'StrongPassword123!';
GRANT SELECT, INSERT, UPDATE, DELETE ON aaraji_registry.* TO 'aaraji_user'@'localhost';
FLUSH PRIVILEGES;
```

---

## 3. File & Folder Permissions

```bash
# Allow PHP to write uploaded files
chmod 755 uploads/
chown www-data:www-data uploads/   # or your web server user
```

---

## 4. Upload to Server

Copy all files to your web root (e.g. `/var/www/html/aaraji/`):

```
aaraji/
├── index.php
├── database.sql
├── .htaccess
├── includes/
│   ├── config.php
│   ├── auth.php
│   ├── upload.php
│   └── layout.php
└── uploads/           ← writable by web server
```

---

## 5. Apache VirtualHost (example)

```apache
<VirtualHost *:80>
    ServerName aaraji.yourdomain.com
    DocumentRoot /var/www/html/aaraji

    <Directory /var/www/html/aaraji>
        Options -Indexes +FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>

    ErrorLog  ${APACHE_LOG_DIR}/aaraji-error.log
    CustomLog ${APACHE_LOG_DIR}/aaraji-access.log combined
</VirtualHost>
```

Enable and restart:
```bash
a2enmod rewrite
a2ensite aaraji
systemctl restart apache2
```

---

## 6. Nginx Config (alternative)

```nginx
server {
    listen 80;
    server_name aaraji.yourdomain.com;
    root /var/www/html/aaraji;
    index index.php;

    # Block includes directory
    location ~ ^/includes/ { deny all; }

    # Block SQL and sensitive files
    location ~* \.(sql|md|log|sh)$ { deny all; }

    location / { try_files $uri $uri/ /index.php?$query_string; }

    location ~ \.php$ {
        fastcgi_pass unix:/run/php/php8.1-fpm.sock;
        fastcgi_index index.php;
        include fastcgi_params;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
    }

    # Upload size
    client_max_body_size 15M;
}
```

---

## 7. First Login

- URL: `http://yourdomain.com/aaraji/`
- Username: `admin`
- Password: `admin@123`

**Change the password immediately after first login** (Profile → Change Password).

---

## Default Admin Hash

The password `admin@123` uses bcrypt (cost 12). To set a different default password,
generate a hash in PHP and update `database.sql`:

```php
echo password_hash('YourNewPassword', PASSWORD_BCRYPT, ['cost' => 12]);
```

---

## Features

| Feature | Description |
|---|---|
| Login / Session | PHP sessions, bcrypt passwords, CSRF protection |
| Plans | Register, edit, delete land plans with file upload |
| Revenue Villages | Manage village list with taluka & district |
| File Upload | Images (JPG, PNG, WEBP) and PDFs up to 10MB |
| Google Maps | Paste share link — map embedded on detail page |
| Search | By plan name, aaraji number, village |
| Filters | By file type, location, village |
| Pagination | 12 plans per page |
| Audit Log | All create/update/delete/login actions logged |
| Admin role | Only admins can delete records |

---

## Troubleshooting

**Blank page / 500 error**
- Check PHP error log: `/var/log/apache2/aaraji-error.log`
- Enable errors temporarily in `config.php`: `ini_set('display_errors', 1);`

**Uploads not saving**
- Check `uploads/` folder exists and is writable: `chmod 755 uploads/`

**Session expires too fast**
- Change `SESSION_LIFETIME` in `config.php` (default: 8 hours)

**Database connection failed**
- Verify credentials in `config.php`
- Test: `mysql -u aaraji_user -p aaraji_registry`
