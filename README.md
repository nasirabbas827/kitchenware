# kitchenware_final  

A lightweight PHP application for managing kitchen‑ware inventory, orders, and notifications. It ships with a bundled **PHPMailer** library for reliable e‑mail delivery (e.g., order confirmations, low‑stock alerts).

---

## Overview  

`kitchenware_final` provides a simple back‑end to store kitchen‑ware items, track stock levels, and send automated e‑mail notifications. The project includes:

- **Database schema** (`Database/kitchen_db.sql`) – ready‑to‑import MySQL dump.  
- **PHPMailer integration** – full source tree under `PHPMailer/` (license, docs, language packs, etc.).  
- **Configuration** – a single `config.php` (not in the repo) where you set DB credentials and e‑mail API keys.

> **Note:** This README is a template. Replace placeholders (e.g., `YOUR_OWN_API_KEY`) with your actual values before deployment.

---

## Features  

| ✅ | Feature |
|---|---------|
| ✔️ | CRUD operations for kitchen‑ware items (name, description, quantity, price). |
| ✔️ | Automatic low‑stock detection with e‑mail alerts. |
| ✔️ | Order processing and confirmation e‑mails using PHPMailer. |
| ✔️ | Multilingual e‑mail templates (PHPMailer language packs). |
| ✔️ | Simple SQL schema for rapid setup. |
| ✔️ | Composer‑based dependency management. |

---

## Tech Stack  

| Component | Description |
|-----------|-------------|
| **PHP** | 7.4+ (core language) |
| **MySQL** | Relational database for inventory data |
| **PHPMailer** | Robust e‑mail library (included in the repo) |
| **Composer** | Dependency manager for PHP packages |
| **HTML / CSS** | Front‑end scaffolding (optional) |

---

## Installation  

1. **Clone the repository**  

   ```bash
   git clone https://github.com/yourusername/kitchenware_final.git
   cd kitchenware_final
   ```

2. **Install PHP dependencies**  

   ```bash
   composer install
   ```

   *The `composer.json` inside `PHPMailer/` is already present; Composer will resolve the PHPMailer package automatically.*

3. **Create the database**  

   ```bash
   mysql -u root -p < Database/kitchen_db.sql
   ```

   Adjust the credentials in `config.php` (create this file if it does not exist).

4. **Configure the application**  

   Create a `config.php` in the project root:

   ```php
   <?php
   // Database
   define('DB_HOST', 'localhost');
   define('DB_NAME', 'kitchen_db');
   define('DB_USER', 'your_db_user');
   define('DB_PASS', 'your_db_password');

   // PHPMailer / SMTP
   define('SMTP_HOST', 'smtp.example.com');
   define('SMTP_PORT', 587);
   define('SMTP_USER', 'your_email@example.com');
   define('SMTP_PASS', 'YOUR_OWN_API_KEY');   // <-- replace with your SMTP password / API key
   define('SMTP_FROM', 'no-reply@example.com');
   define('SMTP_FROM_NAME', 'Kitchenware System');
   ?>
   ```

5. **Web server setup**  

   - Place the project inside your web‑server document root (e.g., `public_html/kitchenware_final`).  
   - Ensure the server runs PHP 7.4+ and has the `mysqli` extension enabled.  

6. **(Optional) Set up cron for periodic stock checks**  

   ```bash