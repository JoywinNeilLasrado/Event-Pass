# Event-Pass Cloud Master Commands

Keep this document safe! Since you cannot simply close your laptop to restart things anymore, these commands are all you will ever need to use on your AWS `ubuntu@ip...` terminal to keep the platform alive.

---

## 1. 🔌 Connecting Securely via SSH
Whenever you need to log into the physical AWS server from your laptop's terminal:
```bash
ssh -i ~/Downloads/eventpass-key.pem ubuntu@65.0.89.91
```

## 2. 🔄 The "Update My Website" Pipeline
Run these exactly in order every single time you push new code or bug fixes from your laptop to GitHub:
```bash
cd /var/www/eventpass
git pull origin main                 # Pulls the newest code
composer install --no-dev            # Installs new PHP packages if any
npm run build                        # Compiles new Frontend CSS/Javascript
php artisan migrate --force          # Updates Database columns safely
```

## 3. 📝 Editing the `.env` File (Nano Editor)
If you ever need to add production keys (like Cashfree or Mailtrap) or change database passwords, you must edit the environment file and wipe the cache immediately after so the server reads the new keys.

```bash
cd /var/www/eventpass                # Move into your project
nano .env                            # Opens the file in the text editor
```
* **Movement:** Use your keyboard's `Up`, `Down`, `Left`, `Right` arrows. Your mouse will not work!
* **Save Changes:** Press `Ctrl + O` -> hit `Enter`.
* **Exit Editor:** Press `Ctrl + X`.

**⚠️ The Golden Rule:**
Immediately after closing `nano`, you *must* force the server to read the new changes by wiping its memory cache:
```bash
php artisan optimize:clear
```

## 4. 🧹 The "Break The Cache" Commands
If you update a configuration, add a new variable to `.env`, or change a massive blade file and the live website *isn't showing the new changes*, run these to forcibly wipe Laravel's memory:
```bash
php artisan optimize:clear           # Nukes EVERYTHING (Views, Config, Routes)
```

## 5. 🤖 The Background Worker (Supervisor) Commands
If an automated email gets stuck, or if you completely change how the "Ticket Booked" email looks and you need to restart the background queue daemon so it reads the new code:
```bash
sudo supervisorctl status                  # Check if the worker is "RUNNING"
sudo supervisorctl restart eventpass-worker:*   # Kills and restarts the background queue
```

### 🕒 Initializing the Background Worker (One-Time Setup)
If you ever wipe your server and need to tell AWS how to run the background jobs again from absolute scratch:
```bash
# 1. Open the file to paste the configuration:
sudo nano /etc/supervisor/conf.d/eventpass-worker.conf
```
*Paste this exact code box in:*
```ini
[program:eventpass-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/eventpass/artisan queue:work --sleep=3 --tries=3
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=ubuntu
numprocs=1
redirect_stderr=true
stdout_logfile=/var/www/eventpass/storage/logs/worker.log
```

```bash
# 2. Tell the system to read the file and start exactly 1 invisible worker:
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start eventpass-worker:*
```

## 6. 🗄️ The Web Server (Nginx) Commands
If the server crashes with a `502 Bad Gateway` error, or if you modify the `/etc/nginx/sites-available/eventpass` file:
```bash
sudo nginx -t                        # Tests to make sure your Nginx code has 0 typos
sudo systemctl restart nginx         # Reboots Nginx completely
sudo tail -f /var/log/nginx/error.log  # Live streams the raw Server crash errors!
```

### 🌐 Initializing Nginx (One-Time Setup)
If you ever rebuild your server, this is the exact code block you must paste inside `sudo nano /etc/nginx/sites-available/eventpass` to tell the internet how to find your Laravel application:
```nginx
server {
    listen 80;
    server_name _;
    root /var/www/eventpass/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;
    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.3-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```
*After saving the file, you must run `sudo ln -s /etc/nginx/sites-available/eventpass /etc/nginx/sites-enabled/` and restart Nginx to turn it on!*

## 7. ☢️ The Log X-Ray (Emergency Only)
If the web application throws a massive `500 Server Error` on a specific page, you need to read the raw backend exceptions live as they happen:
```bash
cd /var/www/eventpass
tail -f storage/logs/laravel.log     # Streams the raw error code live-time
```
*(Press `Ctrl + C` to stop watching the live stream!)*

---

## 8. 🛠️ Essential Cloud Utilities 
These four commands act as your "mechanic's toolbox" when you need to diagnose the physical health of your Linux machine or fix stray platform bugs.

**The "Fix Broken Images" Command:**
If user-uploaded images suddenly stop rendering after a deployment, you likely need to physically map Laravel's secure storage to the public directory on the server.
```bash
cd /var/www/eventpass
php artisan storage:link
```

**The PC Health X-Ray (`htop`):**
Open a live dashboard tracking exact RAM and CPU usage natively if the application feels slow or unresponsive.
```bash
htop
```
*(Press `q` or `Ctrl + C` to kill the dashboard and return to the terminal).*

**The "Disk Space" Check (`df -h`):**
Always run this if your server randomly crashes. AWS free tier gives 20GB. Check what percentage is currently full here:
```bash
df -h
```

**The Nuclear Reboot (`sudo reboot`):**
If the server is absolutely frozen, SSH is disconnected/laggy, and Nginx won't reboot nicely, you can physically power cycle the entire AWS EC2 machine locally.
```bash
sudo reboot
```
