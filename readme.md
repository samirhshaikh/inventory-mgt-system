# Inventory Management System

---

###Tech Stack Used

Purpose of development of this application is to showcase my knowledge of following technologies:

1. Laravel
2. Vue.js, Vuex
3. SPA using Inertia.js
4. Tailwind CSS
5. Webpack

### Assumptions
* MySQL server is running with following config:
    1) host: 127.0.0.1
    2) port: 8889
    3) username: root
    4) password: root
    5) Please create a database schema named 'IMS2' on the MySQL server

> `.env` file is set with above parameters. In case you have different, then you need to modify the same in the `.env` file.

* Install `wkhtmltopdf` from https://wkhtmltopdf.org/downloads.html

### Initiating Application
Please run the following commands in api directory

> composer install
>
> php artisan key:generate
> 
> npm install

### Running Application

> npm run hot
> 
> php artisan route:clear
>
> php artisan ziggy:generate
> 
> php artisan route:cache
> 
> php artisan serve

