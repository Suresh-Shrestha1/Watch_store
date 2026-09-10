# Watch E-Commerce Website

A PHP + MySQL watch e-commerce application with storefront and admin panel, cart/wishlist, order handling, and eSewa payment integration. This README documents the project based only on the repository sources.

---

## Overview

This project is a PHP-based watch e-commerce application (store name "ChronoNest" appears in templates). It includes:

- Customer storefront for browsing watches, viewing product details, managing cart and wishlist, and placing orders.
- Checkout with Cash on Delivery (COD) and eSewa digital wallet.
- Admin panel for product, brand, customer and order management.
- Product image uploads stored in `assets/uploads/`.
- MySQL database persistence.

Key entry points:
- Storefront: `index.php`
- Admin: `admin/index.php`

---

## Features

### Customer features
- Registration and login (passwords hashed with PHP `password_hash`).
- Browse products and product detail pages.
- Add products to wishlist and cart (strap-size selection supported where applicable).
- Update cart quantities; server validates stock before updates and order placement.
- Checkout with shipping info and payment method selection (COD or eSewa).
- Order history and order detail pages in account area.

### Admin features
- Admin login (users with `role = 'admin'`).
- Dashboard and lists for products, brands, customers and orders.
- Product management: add, edit, delete, upload images, toggle active status.
- Order management: update `order_status`, update COD payment status, add admin notes, delete pending unpaid orders.
- Admin actions are protected by session-based checks (see `admin/includes/auth_check.php`).

---

## Technology stack

- PHP (plain PHP pages and controllers).
- MySQL / MariaDB (schema in `schema.sql`).
- HTML/CSS (Tailwind used via CDN on some pages).
- JavaScript (small frontend scripts in `assets/js/`).
- eSewa digital wallet integration for online payments.

Notable files:
- `config/db.php` — DB connection and `SHIPPING_CHARGE` constant.
- `schema.sql` — Database schema.
- `pages/actions/checkout-action.php` — Order creation.
- `pages/actions/esewa/*` — eSewa integration (config, checkout, success/failure handlers).
- `pages/actions/cart-action.php`, `pages/actions/wishlist-action.php` — Cart/wishlist logic.
- `admin/actions/product-action.php`, `admin/actions/order-action.php` — Admin controllers.

---

## Database

- Default DB name used in code: `watch_store` (see `config/db.php`).
- Important tables (from `schema.sql`):
  - `users` — user records (`role` = `customer` or `admin`).
  - `brands` — brand metadata.
  - `products` — product data (model number, price, stock, strap fields, movement type, etc.).
  - `product_images` — uploaded image filenames with `is_main` flag.
  - `cart` — user cart items (includes `selected_strap_size`).
  - `wishlists` — user wishlist items.
  - `orders` — orders with `payment_method` (COD|eSewa), `payment_status`, `order_status` and timestamps.
  - `order_items` — snapshot of items at order time.

Relationships follow foreign keys defined in `schema.sql` (e.g., `products.brand_id` → `brands.id`, `order_items.order_id` → `orders.id`).

Import `schema.sql` to create tables (see Installation section). Do not reference or import `data.sql` as per project guidelines.

---

## Authentication & Authorization

- Customers register and log in via `pages/actions/auth-action.php`. Passwords are hashed and verified with `password_hash`/`password_verify`.
- Admins log in through `admin/actions/auth-action.php`. Admins are implemented as users with `role = 'admin'`.
- Sessions store `user_id` / `admin_id` and role indicators. Admin-only pages check session values and redirect unauthenticated users.

---

## Demo / Test Accounts

Use these demo/test credentials for testing (provided explicitly for this repository):

- Customer
  - Email: `av@gmail.com`
  - Password: `avikumar`

- Admin
  - Email: `suresh@gmail.com`
  - Password: `admin`

These are demo/test credentials only. You can also register a new customer account through the registration page.

---

## eSewa payment integration (confirmed)

Files:
- `pages/actions/esewa/esewa_config.php` — constants and signature helper.
- `pages/actions/esewa/checkout.php` — local checkout page that builds a signed form and posts to eSewa.
- `pages/actions/esewa/payment_success.php` — decodes response, verifies signature, calls eSewa verify API and updates the order as paid.
- `pages/actions/esewa/payment_failure.php` — failure notice.

Flow (as implemented):
1. Customer chooses `eSewa` at checkout; an `orders` row is created with `payment_status = 'pending'`.
2. The app redirects to local `esewa/checkout.php` which constructs `transaction_uuid`, formats amounts, generates an HMAC-SHA256 Base64 `signature`, and renders a POST form to `ESEWA_PAYMENT_URL` with `signed_field_names`.
3. eSewa returns to the configured success/failure URL with a base64 `data` payload.
4. `payment_success.php` decodes the payload, verifies signature (rebuilding the signed message using `signed_field_names`), confirms `status === 'COMPLETE'`, calls eSewa verify API server-to-server, and updates the order (`payment_status = 'paid'`, `order_status = 'confirmed'`, stores `transaction_id`).

Configuration notes:
- Set your merchant/product code and secret key in `pages/actions/esewa/esewa_config.php` and update `SUCCESS_URL`/`FAILURE_URL` to your production URLs.
- Do not commit production secrets. For production, enable strict SSL verification when calling eSewa verify API (`CURLOPT_SSL_VERIFYPEER` should be `true`).

---

## Orders & Checkout specifics

- Orders are created in `pages/actions/checkout-action.php`: validates shipping, verifies stock, calculates totals, inserts into `orders` and `order_items`, decrements product stock, clears cart.
- Shipping charge constant is `SHIPPING_CHARGE` in `config/db.php`.
- Order number format generated in code is like `CN-YYYYMMDD-XXXXX`.
- Admins can progress `order_status` through `pending` → `confirmed` → `processing` → `shipped` → `delivered`.
- When `delivered` and payment method is COD, payment status can be set to `paid` by admin.

---

## Installation & local setup (quick)

Prerequisites: PHP (7.4+ recommended), MySQL/MariaDB, Apache or a local stack (XAMPP/WAMP/Laragon).

Steps:
1. Place the repository in your web root, e.g. `C:\xampp\htdocs\watch_store`.
2. Create a database (example `watch_store`) and import `schema.sql`.
   Example (MySQL CLI):
   ```powershell
   mysql -u root -p
   CREATE DATABASE watch_store CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
   USE watch_store;
   SOURCE C:\xampp\htdocs\watch_store\schema.sql;
   EXIT;
   ```
3. Update database connection in `config/db.php` if your credentials differ.
4. Configure eSewa in `pages/actions/esewa/esewa_config.php` (merchant/product code, secret key, success/failure URLs).
5. Ensure `assets/uploads/products` and `assets/uploads/brands` are writable for image uploads.
6. Start Apache and MySQL and open `http://localhost/watch_store/`.

Notes: Do not import `data.sql` as part of setup instructions for this README.

---

## Troubleshooting

- DB connection errors: verify `config/db.php`, DB name, user and password, and ensure MySQL is running.
- Missing tables: confirm you imported `schema.sql` into correct DB.
- eSewa errors: ensure `ESEWA_SECRET_KEY`, `ESEWA_PRODUCT_CODE` and callback URLs are correct; for public callbacks from eSewa you may need a publicly reachable URL or tunnel (ngrok) in development.
- Uploads failing: check folder permissions for `assets/uploads/`.

---

## Security considerations (observed)

- Passwords are hashed and database access uses prepared statements.
- Recommended improvements: add CSRF protection for forms, move secrets to environment variables, enable SSL verification for eSewa server calls in production, and harden file upload checks.

---

## UI / Design & future improvements (suggestions)

- Suggested improvements (not implemented in code):
  - Add consistent iconography (Heroicons / Font Awesome).
  - Enhance product-card hover effects, add micro-interactions, and improve loading states.
  - Make cart/wishlist actions AJAX-based for smoother UX.
  - Improve responsiveness and test on mobile devices.
  - Add admin reporting (sales, stock alerts) and better product filters.

---

## License

No license is currently specified for this project.

---

If you want, I can add an example `.env` template (without secrets) or a small script that checks required folders and DB connectivity.
