# 💹 Swap Calculator — Laravel 12 Test Project

A clean and simple **Forex Swap Calculator** built with **Laravel 12**.  
This project allows users to calculate total swap profit or loss based on trading parameters such as currency pair, lot size, swap rates, position type, and holding days.  
The system validates input, performs instant AJAX calculations, stores results in the database, and displays recent calculation history.

---

## 🧩 General Information

- **Framework:** Laravel 12.x  
- **PHP Version:** ≥ 8.2  
- **Database:** MySQL / MariaDB  
- **Frontend:** Blade + TailwindCSS + Fetch API  
- **Architecture:** MVC + Service Layer Pattern  
- **License:** MIT  

---

## ⚙️ Installation & Running Guide

### 1️⃣ Clone the repository

git clone https://github.com/phamle21/swap-calculator.git
cd swap-calculator

### 2️⃣ Install dependencies
composer install
npm install && npm run build

### 3️⃣ Set up environment
cp .env.example .env
php artisan key:generate


Update database connection in .env:

DB_CONNECTION=mysql
DB_DATABASE=swap_calculator
DB_USERNAME=root
DB_PASSWORD=

### 4️⃣ Run migrations
php artisan migrate

### 5️⃣ Start the development server
php artisan serve


Visit: https://test-swap-calculator.2etitb.easypanel.host/

---

### ⚡ Optional: Optimize for better performance
# Generate cache files for routes, config, and views
php artisan optimize

# (Use this when deploying or running in production)
# To clear caches after updating code/config:
php artisan optimize:clear
