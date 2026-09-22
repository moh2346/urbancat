# Urban Cats — Premium Cat-Selling (Paystack) — XAMPP

## Requirements
- XAMPP PHP 8.1+, MySQL/MariaDB, Apache mod_rewrite + curl

## Placement
Place folder as `C:\xampp\htdocs\urbancat`, start Apache/MySQL.

## Database
1. `http://localhost/phpmyadmin` → Create `urban_cats` or import directly.
2. Import `database/urban_cats.sql` (creates all tables, breeds/cats/faqs demo).
3. `config/config.php` `db.user/pass` if needed; `site.base_url=http://localhost/urbancat`.

## Admin
- `http://localhost/urbancat/admin/setup.php` → create first admin (hashed) → **delete file** → `admin/login.php`.
- Manage Cats/Breeds (price/deposit/mode, images 4MB jpg/png/webp, unique names), Orders/Payments, Customers, Enquiries, FAQs, Testimonials, Care Guide, Newsletter, Settings (contact/social/paystack).

## Paystack test
- Admin → Settings → Paystack `pk_test_…`/`sk_test_…` (default `pk_test_placeholder` = simulated). 
- Callback `http://localhost/urbancat/callback.php?reference={ref}`; Webhook `http://localhost/urbancat/payment/webhook.php` (SHA512).
- Test: cart → checkout → Paystack test card `4084084084084081` → callback verifies server amount/currency/reference → `sold`/`reserved` + cart cleared. Duplicate callback idempotent.
- Switch live: replace keys with `pk_live_…`, set `app.env=production`, HTTPS required.

## Images
Replace SVG placeholders in `assets/images/{brand,breeds,cats,site}` with Unsplash/Pexels WebP, update `IMAGE_CREDITS.md`, set alt/dims, lazy-load.

## Contact editing
Admin → Settings: `contact_email` `urbankitty0@gmail.com`, `contact_phone` `09122037945`, `whatsapp` `2349122037945`, `address` `Gwarinpa, Abuja`.

## Production
- `base_url` → `https://yourdomain.com`, `display_errors Off`, `uploads/cats` 755 (block php in `.htaccess`), HTTPS, backup DB + uploads.

## Structure
`admin/`, `assets/{css,js,images/brand,breeds,cats,site}`, `config/`, `database/`, `includes/`, `payment/`, `uploads/`, public pages.

## Tests
`php -l` all, nav/mobile, filters/pagination, auth/favs/cart, checkout validation, payment states, duplicate callback, webhook idempotency, unavailable block, server price, 320–1440, overflow, keyboard, homepage vs reference.

## Limitations
Placeholder SVGs, simulated Paystack in dev, mail stub.
