# README

## SYSTEM REQUIREMENT

* DB
    - MySQL 5.6
* Apache
    - 2.4
* PHP
    - 8.2
* Laravel
    - 8.x
* Composer
    - 1.4.1
* Node
    - v16.18.1
* Npm
    - 8.19.2


## Deploy
* permission
```
chmod -R 777 bootstrap/cache
chmod -R 777 storage/logs/
chmod -R 777 storage
chmod -R 777 storage/framework
chmod -R 777 public/media
chmod -R 777 public/tmp_uploads
```

* run
```bash
composer install --ignore-platform-req=php
 php artisan config:cache
 php artisan config:clear
 php artisan cache:clear
```

* run deploy
```bash
cp .env.example .env
php artisan key:generate
```
* config your database in .env
  find and replace database config
```bash
vi .env
```
* run database
```bash
php artisan migrate
php artisan db:seed
```

* laravel-mix
```bash
npm install laravel-mix --save-dev
```

* account
```bash
backend: /management/login 
    admin@gmail.com/admin
```


### Using docker ###
```
# 1. Clone source
git clone <repo>
cd giavangbac

# 2. Copy .env (vì .env không commit lên git)
cp .env.example .env
# Sửa thông tin DB cho phù hợp

# 3. Chạy lên
docker compose up -d --build

# 4. Setup Laravel lần đầu
docker exec -it php-laravel php artisan migrate
docker exec -it php-laravel php artisan db:seed  # nếu có seeder

connect navicat: new connection: 127.0.0.1/3307/root/123456
```


