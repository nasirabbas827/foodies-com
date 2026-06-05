# foodies.com  

**A PHP‑based platform for discovering, sharing, and ordering delicious food.**  

---  

## Overview  

`foodies.com` is a lightweight web application that lets users browse restaurant menus, place orders, and receive email confirmations. The project demonstrates clean PHP architecture, MySQL integration, and reliable email delivery using **PHPMailer**.  

---  

## Features  

| ✅ | Feature |
|---|---------|
| ✔️ | User‑friendly restaurant and menu browsing |
| ✔️ | Secure order placement with MySQL persistence |
| ✔️ | Automatic email notifications (order receipt, status updates) |
| ✔️ | Multilingual email templates (PHPMailer language packs) |
| ✔️ | Easy database import/export |
| ✔️ | Comprehensive documentation (`Foodies.com.docx`) |

---  

## Tech Stack  

| Layer | Technology |
|-------|------------|
| Backend | PHP 8.x |
| Database | MySQL (see `Database/foodies_db.sql`) |
| Email | PHPMailer (bundled in `PHPMailer/`) |
| Dependency Management | Composer |
| Documentation | Markdown (`README.md`) & Word (`Foodies.com.docx`) |

---  

## Installation  

1. **Clone the repository**  

   ```bash
   git clone https://github.com/yourusername/foodies.com.git
   cd foodies.com
   ```

2. **Install PHP dependencies**  

   ```bash
   composer install        # reads PHPMailer/composer.json
   ```

3. **Create the database**  

   ```bash
   # Log into MySQL and run the script
   mysql -u root -p < Database/foodies_db.sql
   ```

4. **Configure the application**  

   Copy the example config and edit the values to match your environment:  

   ```bash
   cp config.example.php config.php
   ```

   ```php
   // config.php (excerpt)
   define('DB_HOST', 'localhost');
   define('DB_NAME', 'foodies');
   define('DB_USER', 'your_db_user');
   define('DB_PASS', 'your_db_password');

   // PHPMailer settings
   define('MAIL_HOST', 'smtp.example.com');
   define('MAIL_USERNAME', 'your_email@example.com');
   define('MAIL_PASSWORD', 'YOUR_OWN_API_KEY'); // replace with your SMTP password / API key
   define('MAIL_PORT', 587);
   ```

5. **Set up a web server**  

   - Place the project in your web root (e.g., `public_html/foodies.com`).  
   - Ensure the server points to `index.php` as the entry point.  
   - Enable `mod_rewrite` (or equivalent) if you use clean URLs.

---  

## Usage  

### Running the site  

Open your browser and navigate to the domain or localhost where you installed the project, e.g.:

```
http://localhost/foodies.com/
```

You should see the homepage with a list of restaurants.

### Placing an order  

1. Browse a restaurant’s menu.  
2. Add items to the cart and proceed to checkout.  
3. Fill in the required delivery