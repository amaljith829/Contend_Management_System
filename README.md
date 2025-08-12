## Articles CMS (PHP/MySQL)

A simple articles CMS built with vanilla PHP and MySQL. It includes a public listing, single-article pages, categories, image uploads, an admin dashboard with CRUD, scheduled publishing, pagination, and a contact form powered by PHPMailer.

### Features
- **Public site**: Paginated list of published articles, single-article view, categories, images
- **Admin dashboard**: Create, edit, delete, and publish/schedule articles; image upload/replace/delete
- **Authentication**: Basic login/logout for admin area
- **Contact form**: Sends email via SMTP using PHPMailer
- **Pagination**: Simple paginator utility
- **Styling**: Bootstrap 5 + minimal custom CSS

### Tech stack
- **PHP**: 8.x suggested (works with PDO + prepared statements)
- **Database**: MySQL/MariaDB
- **Frontend**: Bootstrap 5, jQuery (incl. jQuery Validate)
- **Email**: PHPMailer (vendored in `vendor/PHPMailer-master`)

---

## Project structure
```
www/
├─ admin/                 # Admin UI (dashboard, CRUD, image upload)
│  ├─ includes/article_form.php
│  ├─ index.php
│  ├─ new_article.php
│  ├─ edit_article.php
│  ├─ edit_article_image.php
│  ├─ delete_article.php
│  ├─ delete_article_image.php
│  └─ publish_article.php
├─ classes/               # Domain classes (Article, Auth, etc.)
├─ includes/              # Autoloader, header/footer, pagination, DB bootstrap
├─ uploads/               # Uploaded images (writable)
├─ vendor/PHPMailer-master
├─ css/styles.css
├─ js/script.js
├─ index.php              # Public article list
├─ article.php            # Single article page
├─ contact.php            # Contact form (SMTP)
├─ login.php / logout.php
├─ config.php             # Local configuration (DB + SMTP)
└─ .htaccess              # Protects config.php
```

---

## Prerequisites
- PHP 8.0+ with `pdo_mysql`
- MySQL/MariaDB
- Web server (Apache/Nginx) or PHP built-in server
- Composer (optional; PHPMailer already vendored)

---

## Quick start (local)
1. **Clone** the repository and open the `www/` directory.
2. **Create database** and tables (see schema below).
3. **Configure** `config.php` with your DB and SMTP settings.
4. **Ensure uploads is writable**:
   - macOS/Linux: `chmod -R 755 uploads` (or `775/777` depending on your environment)
5. **Run** with PHP built-in server for local dev:
   ```bash
   cd www
   php -S localhost:8000 -t .
   ```
   Visit `http://localhost:8000`.

---

## Configuration
Edit `www/config.php`. Example:
```php
<?php
// Database
define('DB_HOST', 'localhost');
define('DB_NAME', 'cms');
define('DB_USER', 'cms_user');
define('DB_PASS', 'your_password');

// SMTP (for contact form)
define('SMTP_HOST', 'smtp.example.com');
define('SMTP_USER', 'no-reply@example.com');
define('SMTP_PASS', 'your_smtp_password');
define('SMTP_PORT', 587); // TLS: 587, SSL: 465
```
Notes:
- `www/.htaccess` denies direct access to `config.php`.
- Do not commit real credentials to version control.

---

## Database schema
Minimal schema used by the app (adjust types/lengths as needed):
```sql
CREATE TABLE user (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(50) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL
);

CREATE TABLE article (
  id INT AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(255) NOT NULL,
  content MEDIUMTEXT NOT NULL,
  published_at DATETIME NULL,
  image_file VARCHAR(255) NULL
);

CREATE TABLE category (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL UNIQUE
);

CREATE TABLE article_category (
  article_id INT NOT NULL,
  category_id INT NOT NULL,
  PRIMARY KEY (article_id, category_id),
  FOREIGN KEY (article_id) REFERENCES article(id) ON DELETE CASCADE,
  FOREIGN KEY (category_id) REFERENCES category(id) ON DELETE CASCADE
);
```

### Create an admin user
Generate a bcrypt hash and insert it:
```bash
php -r 'echo password_hash("change-me", PASSWORD_BCRYPT), PHP_EOL;'
```
```sql
INSERT INTO user (username, password) VALUES ('admin', '<paste_bcrypt_hash_here>');
```

---

## Usage notes
- **Login**: `/login.php` (creates a session on success)
- **Admin**: `/admin/index.php` after login
- **Publishing/scheduling**: Leave "Publish Date" empty to keep a draft. Set a future date to schedule. Only published articles with `published_at <= NOW()` appear on the public homepage.
- **Image uploads**: GIF/PNG/JPEG only. Files are written to `www/uploads/`. Replacing an image deletes the previous file.
- **Categories**: Select multiple categories when creating/editing.

---

## Email (PHPMailer)
- PHPMailer is included in `vendor/PHPMailer-master`.
- Ensure SMTP settings in `config.php` are correct.
- Contact form posts to `www/contact.php` and sends to `SMTP_USER`.
- If you prefer Composer: `composer require phpmailer/phpmailer` and update the `require` paths in `contact.php` accordingly.

---

## Deployment
- Point your web server document root to the `www/` directory.
- Ensure `uploads/` is writable by the web server user.
- Serve over HTTPS. Consider setting `session.cookie_secure=1` and `session.cookie_httponly=1`.
- Keep `config.php` out of VCS or use environment-specific values.

---

## Security considerations
- Do not expose real credentials. Rotate secrets if they were committed.
- Session is started in `includes/autoLoader.php`. Consider SameSite/secure cookie flags in production.
- Input is escaped where rendered; continue to validate/sanitize user inputs on any new features.

---

## License
Add your preferred license (e.g., MIT) to a `LICENSE` file.

## Acknowledgements
- PHPMailer
- Bootstrap, jQuery, jQuery Validate
