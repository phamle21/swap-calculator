# 💹 Swap Calculator — Laravel 12 Test Project

A clean and modular **Forex Swap Calculator** built with **Laravel 12**.  
This project allows users to calculate total swap profit or loss based on trading parameters such as currency pair, lot size, swap rates, position type, and holding days.  
The system validates inputs, performs instant AJAX calculations, stores results in the database, and displays recent calculation history.

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
```bash
git clone https://github.com/phamle21/swap-calculator.git
cd swap-calculator
```

### 2️⃣ Install dependencies
```bash
composer install
npm install && npm run build
```

### 3️⃣ Set up environment
```bash
cp .env.example .env
php artisan key:generate
```

Then update your database connection in `.env`:
```
DB_CONNECTION=mysql
DB_DATABASE=swap_calculator
DB_USERNAME=root
DB_PASSWORD=
```

### 4️⃣ Run migrations
```bash
php artisan migrate
```

### 5️⃣ Start the development server
```bash
php artisan serve
```

Visit: [https://test-swap-calculator.2etitb.easypanel.host/](https://test-swap-calculator.2etitb.easypanel.host/)

---

### ⚡ Optional: Optimize for better performance
Generate cache files for routes, config, and views:
```bash
php artisan optimize
```

To clear caches after updating code or config:
```bash
php artisan optimize:clear
```

---

## 🧮 Core Functionality

- Calculate **Total Swap** based on the formula:  
  `Total Swap = Lot Size × Swap Rate × Holding Days`
- Select **Long or Short** position (swap direction changes automatically)
- Instant validation and calculation (via AJAX)
- Save every calculation to the database (`swap_calculations` table)
- Display **calculation history** with filters, pagination, and delete actions
- Built with clean separation of logic using **DTO, Service, and Repository layers**

---

## 🖥️ Admin Panel (Filament)

The project includes **Filament Admin (v4)** for data management:

- Manage swap calculation history (CRUD)
- Import/Export CSV data
- Auto-calculate `total_swap` when importing
- Optional: create `CurrencyPairResource` to manage default swap rates

---

## 🗄️ Database Schema Overview

**Table:** `swap_calculations`
| Column | Type | Description |
|---------|------|-------------|
| id | bigint | Primary key |
| currency_pair_id | bigint | Foreign key to `currency_pairs` |
| lot_size | decimal(10,2) | Trade volume |
| position_type | enum(Long, Short) | Trade direction |
| swap_rate | decimal(10,2) | Swap rate (USD/lot/day) |
| days | int | Holding days |
| total_swap | decimal(15,4) | Total swap result |
| created_at / updated_at | timestamps | Metadata |

---

## 6️⃣ Advanced Architecture

This project follows a **modular and extensible structure** with clear separation of layers:
**Controller → DTO → Service → Repository → Model**.  
It demonstrates architectural thinking suitable for scalable Laravel applications.

### 🧱 `app/DTOs`
- **`SwapInputDTO.php`**  
  Data Transfer Object (DTO) encapsulating user input fields such as `pair`, `lot_size`, `swap_long`, `swap_short`, `position_type`, and `days`.  
  Used to safely pass structured data from the controller to the service layer.

---

### ⚙️ `app/Http`
- **`Controllers/SwapController.php`**  
  Handles user requests (via form or AJAX), delegates business logic to `SwapService`, and returns the calculated result or view.  
  Contains no business logic — acts purely as an orchestrator.

- **`Requests/SwapCalculateRequest.php`**  
  Defines Laravel validation rules ensuring data integrity (e.g. positive lot size, valid position type, integer days).

- **`Resources/SwapCalculationResource.php`**  
  Used for **Filament Admin Panel**, providing CRUD management for swap calculations.

---

### 🧮 `app/Services`
- **`SwapService.php`**  
  Contains all **business logic**:  
  - Determines the active swap rate (`swap_long` or `swap_short`) based on position type  
  - Calculates `total_swap = lot_size × swap_rate × days`  
  - Interacts with `SwapCalculationRepository` to persist data  
  Enables clean testable logic independent from controllers.

---

### 🗄️ `app/Repositories`
- **`SwapCalculationRepository.php`**  
  Handles all database interactions for `swap_calculations` (CRUD, pagination, and history queries).  
  Simplifies data access and allows easy mocking in tests.

- **`SwapRateRepository.php`**  
  Provides access to swap rate sources or `currency_pairs` data.  
  Encapsulates rate-fetching logic to decouple from the service layer.

---

### 🧩 `app/Filament`
- Houses all **Filament Admin configurations**, including:  
  - `SwapCalculationResource.php` (admin CRUD for history)  
  - `Importer` classes for batch uploads with automatic `total_swap` computation.  

---

### ⚙️ `app/Providers`
- Service providers for dependency binding or registering additional layers when scaling.  
  Currently optional but ready for future DI or event bootstrapping.

---

## 🧭 Design Philosophy

- Clear separation of **responsibility per layer**
- DRY, testable, and maintainable codebase
- Easy to extend for:
  - REST API endpoints (`/api/swap/calculate`, `/api/swap/history`)
  - Dashboard widgets (e.g., total swap over last 7 days)
  - CSV import/export
  - Multi-user tracking

---

## 📜 License
This project is open-sourced under the [MIT License](https://opensource.org/licenses/MIT).
