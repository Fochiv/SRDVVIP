# SRDVVIP

## Stack

The public restaurant website remains the existing HTML/CSS/JavaScript/Bootstrap
vitrine. PHP 8.2 now serves the cart, checkout API, and protected admin area.
Orders are persisted with PDO/MySQL before the customer is redirected to
WhatsApp.

## Run

The Replit workflow runs:

```sh
php -S 0.0.0.0:5000 -t .
```

Open `/` for the public website, `/cart.php` for the cart, and
`/admin/login.php` for administration.

## MySQL setup

1. Create a MySQL database and import `database/schema.sql`.
2. Configure either `DATABASE_URL` with a `mysql://...` connection string, or
   the `DB_HOST`, `DB_PORT`, `DB_NAME`, `DB_USER`, and `DB_PASSWORD` variables
   from `.env.example`.
3. Generate an admin password hash without putting the password in source:

```sh
php -r "echo password_hash('your-password', PASSWORD_DEFAULT), PHP_EOL;"
```

4. Set `SRDVVIP_ADMIN_USER` and `SRDVVIP_ADMIN_PASSWORD_HASH` in the Replit
   environment.

The public static menu intentionally remains available while MySQL is not
configured. Once the database is available, `api/menu.php` synchronizes
availability, names, prices, and images with the public add-to-cart buttons.

## Important routes

- `index.html`: existing public vitrine
- `cart.php`: persistent localStorage cart and checkout form
- `api/order.php`: server-side validation, MySQL transaction, order number, and WhatsApp URL
- `admin/dashboard.php`: statistics and recent orders
- `admin/menus.php`: menu/category CRUD and protected image uploads
- `admin/orders.php`: search, filters, status updates, and polling count
- `admin/order.php`: order details, WhatsApp contact, status, and printing