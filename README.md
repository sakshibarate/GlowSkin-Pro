# GlowSkin Pro — Skin Profile & Skincare Recommendation System

**MCA-level PHP/MySQL academic project**  
Stack: PHP 8+, MySQL/MariaDB, HTML5, CSS3, JavaScript, Apache (XAMPP).

## What this project does
GlowSkin Pro is a polished, responsive web application for questionnaire-based skin-profile classification and educational skincare recommendations. It is a new, organised implementation inspired by the uploaded GlowSkin/Skin Analyzer project, retaining its core idea: user accounts, a skin questionnaire, recommendations, saved results and an admin area.

### Modules
- Public landing page and responsive navigation
- User registration, secure login/logout and profile editing
- Expanded questionnaire with validated multiple-choice responses (skin feel, concerns, sensitivity, climate and routine)
- Transparent rule-based profile estimate (Dry, Oily, Combination or Balanced)
- Result page with routine guidance, disclaimer and uploaded photo reference
- Required JPG/PNG/WebP photo upload (5 MB cap), client-side preview, server-side MIME/dimension checks and random filenames
- Photo storage folder with directory listing disabled and script execution denied
- Product/recommendation library filtered by skin profile
- Personal analysis history
- Contact form and admin message inbox
- Admin login and dashboard with counts
- Admin user activation/blocking, result overview and product visibility controls
- Admin add-product form
- MySQL schema with foreign keys, indexes and seeded sample recommendations
- Password hashing, prepared statements, output escaping, CSRF tokens and session ID regeneration

## Important academic/health limitation
The photo upload is integrated into the assessment and the image is saved and shown in the result. The current build does **not** run a trained computer-vision model on the photo: the suggested skin-feel profile is derived from questionnaire answers only. It is not accurate to claim that the uploaded image is being medically analysed by AI. For genuine image classification, train/evaluate a separate model on a suitable, consented and labelled dataset and connect it as a separately tested component. The current photo feature is an image-reference workflow, not a skin-condition detector.

## Installation on Windows using XAMPP
1. Install and open XAMPP. Start **Apache** and **MySQL**.
2. Extract the `GlowSkin_Pro` folder into `C:\xampp\htdocs\` so the path is `C:\xampp\htdocs\GlowSkin_Pro`.
3. Open `http://localhost/phpmyadmin`.
4. Choose **Import**, select `database/glowskin_pro.sql`, and run the import. Alternatively, open the SQL file and execute it in phpMyAdmin.
5. Check `config.php`. Default local settings are host `127.0.0.1`, database `glowskin_pro`, user `root`, blank password. Update them if your local MySQL setup differs.
6. Visit `http://localhost/GlowSkin_Pro/` in your browser.
7. Register a normal user at `register.php`.
8. Create the administrator at `http://localhost/GlowSkin_Pro/setup_admin.php`. Use a unique password of at least 12 characters.
9. **Immediately delete `setup_admin.php` after the admin is created.** Sign in at `admin_login.php`.
10. If you already imported an earlier version of the database, run the migration below (or re-import into a fresh database).
11. Try the flow: register → sign in → photo + questionnaire → result → recommendations → history → profile → contact → admin dashboard.

## Requirements
- PHP 8.0+ (PDO MySQL extension enabled)
- MySQL 5.7+/MariaDB with JSON support
- Apache with `.php` execution (XAMPP recommended)
- Internet is only needed to load the optional Google Fonts; the layout works without them.

## Updating an existing database
If you installed an earlier ZIP before the photo feature, open phpMyAdmin, select `glowskin_pro`, open the SQL tab and run:

```sql
ALTER TABLE analyses ADD COLUMN image_path VARCHAR(255) NULL AFTER summary;
```
Run this only once. Fresh installs already have the column in `database/glowskin_pro.sql`.

## Photo upload notes
- Apache/PHP must have write permission for `uploads/skin_photos/`.
- PHP settings must permit uploads of at least 5 MB (`upload_max_filesize` and `post_max_size`).
- The photo is saved on the local server with a random filename. This demo does not yet include a delete-photo control, automated retention, encryption at rest or a trained image model; do not deploy with real sensitive photos without adding these controls.

## Database overview
- `users`: user/admin accounts and account status
- `analyses`: questionnaire answers and generated profile; linked to `users`
- `products`: recommendation examples and visibility; managed by admin
- `contact_messages`: user messages; optional link to user

## Folder structure
```text
GlowSkin_Pro/
├── assets/
│   ├── css/style.css
│   └── js/app.js
├── database/glowskin_pro.sql
├── uploads/skin_photos/ (uploaded assessment photos)
├── includes/
│   ├── header.php
│   └── footer.php
├── config.php
├── index.php
├── register.php
├── login.php
├── logout.php
├── analysis.php
├── result.php
├── recommendations.php
├── history.php
├── profile.php
├── contact.php
├── admin_login.php
├── admin_dashboard.php
├── admin_product.php
├── setup_admin.php
└── README.md
```

## Demo/report suggestions
- Title: **GlowSkin Pro: A Questionnaire-Based Skin Profiling and Skincare Recommendation System**
- Use case actors: Guest, Registered User, Administrator
- Main entities: User, Analysis, Product, Contact Message
- Test cases: duplicate email, wrong password, blocked account, incomplete questionnaire, CSRF mismatch, profile update, result ownership, admin-only access, product activation, contact submission.
- Suggested future enhancement: consent-based image upload and a separately evaluated computer-vision model. Keep the questionnaire method clearly labelled unless the model is genuinely built and tested.

## Security note
This is a learning/demo project, not production-hardened software. Before deployment, configure HTTPS, environment-based secrets, rate limiting, audit logging, backups, privacy retention/deletion controls and stronger operational protections. Delete `setup_admin.php` after initial setup.
