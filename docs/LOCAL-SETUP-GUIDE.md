# Local Setup Guide — Bagisto + Custom Modules

## Prerequisites (Ubuntu/Debian)

### Step 1: Install PHP 8.2+ and extensions

```bash
sudo apt update
sudo apt install -y php8.2 php8.2-cli php8.2-common php8.2-mysql php8.2-xml \
  php8.2-curl php8.2-gd php8.2-mbstring php8.2-zip php8.2-bcmath \
  php8.2-intl php8.2-readline php8.2-tokenizer php8.2-fileinfo
```

If `php8.2` isn't available, add the PPA first:

```bash
sudo add-apt-repository ppa:ondrej/php -y
sudo apt update
```

Verify:

```bash
php -v
# Should show PHP 8.2.x or 8.3.x
```

### Step 2: Install Composer

```bash
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer
```

Verify:

```bash
composer --version
```

### Step 3: Install MySQL 8

```bash
sudo apt install -y mysql-server
sudo systemctl start mysql
sudo mysql_secure_installation
```

Create the database and user:

```bash
sudo mysql -u root -p
```

Inside MySQL:

```sql
CREATE DATABASE bagisto;
CREATE USER 'bagisto'@'localhost' IDENTIFIED BY 'your_password_here';
GRANT ALL PRIVILEGES ON bagisto.* TO 'bagisto'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

### Step 4: Install Node.js (for frontend assets)

```bash
curl -fsSL https://deb.nodesource.com/setup_20.x | sudo -E bash -
sudo apt install -y nodejs
```

Verify:

```bash
node -v
npm -v
```

---

## Install Bagisto

### Step 5: Create Bagisto project

```bash
composer create-project bagisto/bagisto my-store
cd my-store
```

### Step 6: Run the Bagisto installer

```bash
php artisan bagisto:install
```

It will ask you:

- **App URL**: `http://localhost:8000`
- **Database**: `mysql`
- **DB Host**: `127.0.0.1`
- **DB Port**: `3306`
- **DB Name**: `bagisto`
- **DB User**: `bagisto`
- **DB Password**: `your_password_here`
- **Admin email**: pick one (e.g., `admin@yourstore.com`)
- **Admin password**: pick one

This creates tables, seeds data, and builds assets.

### Step 7: Start the dev server

```bash
php artisan serve
```

Visit:

- Storefront: http://localhost:8000
- Admin: http://localhost:8000/admin

Login with the admin credentials you set during install.

---

## Install Custom Modules

### Step 8: Copy the packages

From this workspace, copy the `packages/Agency/` folder into your Bagisto project:

```bash
# From the workspace root (where you see packages/ folder)
cp -r packages/Agency /path/to/my-store/packages/
```

So your project has:

```
my-store/
├── packages/
│   └── Agency/
│       ├── SiteVisit/
│       └── ProductSample/
├── app/
├── config/
└── ...
```

### Step 9: Register the packages

Edit `my-store/composer.json` — find the `"autoload"` → `"psr-4"` section and add:

```json
"Agency\\SiteVisit\\": "packages/Agency/SiteVisit/src/",
"Agency\\ProductSample\\": "packages/Agency/ProductSample/src/"
```

### Step 10: Register Service Providers

Edit `my-store/bootstrap/providers.php` and add these to the array:

```php
Agency\SiteVisit\Providers\SiteVisitServiceProvider::class,
Agency\SiteVisit\Providers\ModuleServiceProvider::class,
Agency\ProductSample\Providers\ProductSampleServiceProvider::class,
Agency\ProductSample\Providers\ModuleServiceProvider::class,
```

### Step 11: Dump autoload and migrate

```bash
cd my-store
composer dump-autoload
php artisan migrate
php artisan config:clear
php artisan cache:clear
```

### Step 12: Verify

- Visit http://localhost:8000/admin
- You should see "Site Visits" and "Product Samples" in the admin menu
- Visit http://localhost:8000/site-visits (as a logged-in customer) for the storefront side

---

## Quick Reference Commands

```bash
# Start server
php artisan serve

# Clear all caches
php artisan optimize:clear

# Run migrations after code changes
php artisan migrate

# Re-dump autoload after namespace changes
composer dump-autoload
```

---

## Troubleshooting

| Problem                        | Fix                                                   |
| ------------------------------ | ----------------------------------------------------- |
| "Class not found" errors       | Run `composer dump-autoload`                          |
| Menu items not showing         | Run `php artisan config:clear`                        |
| Migration errors               | Check MySQL is running: `sudo systemctl status mysql` |
| "Permission denied" on storage | `chmod -R 775 storage bootstrap/cache`                |
| Port 8000 in use               | Use `php artisan serve --port=8080`                   |
