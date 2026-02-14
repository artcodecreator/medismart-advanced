# MediSmart Online Pharmacy — Advanced (PHP + MySQL + Bootstrap)

## Features
- User registration with email verification.
- Secure login with optional 2FA (email OTP).
- Browse, search, filter, and sort products.
- Cart, checkout, orders, order status tracking.
- Prescription upload and admin verification.
- Payment: COD and demo card gateway (FakePay). Use `4242424242424242` to succeed.
- AI-style product recommendations (content-based) and fraud risk scoring (heuristics).
- Secure messaging: user tickets and admin replies.
- Admin panel: manage products, users, orders, prescriptions, tickets, analytics with charts.
- Security: password hashing, CSRF tokens, prepared statements, XSS-safe output.

## Quick Setup (XAMPP on Windows)
1. Copy the `medismart_advanced` folder to `C:\xampp\htdocs\medismart_advanced`.
2. Start Apache and MySQL.
3. Open `http://localhost/medismart_advanced/public/index.php` once to auto-create DB.
4. In phpMyAdmin, select DB `medismart_advanced` → Import `sql/schema.sql`.
5. Admin login: `admin` / `Admin@123`.
6. Registration: sign up, then open the `/mails` folder in project root and click the verification link from the latest `.txt` file.
7. Optional: enable 2FA in Profile. The OTP code will also appear in `/mails` on each login where 2FA is required.

## Payment (Demo)
- Select Card. Enter: `4242424242424242`, any future expiry, any CVC.
- This writes a record to `payment_attempts`. No real gateway used.

## Folders
- `/config` DB and app config.
- `/includes` shared layout.
- `/lib` helpers: Mailer, Recommend, Fraud, FakePay.
- `/public` user-facing pages.
- `/admin` admin pages.
- `/sql/schema.sql` schema and seed data.
- `/mails` dev outbox for verification emails and OTPs.

## Notes
- For HTTPS, configure SSL in Apache for production.
- Do not enter real card data. This is a student demo.
- Recommendations and fraud detection are heuristic demos, not medical or financial advice.
