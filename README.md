# 💹 Swap Calculator — Laravel 12 Test Project

## 📑 Table of Contents
- [General Information](#-general-information)
- [Core Functionality](#-core-functionality)
- [Admin Panel (Filament)](#️-admin-panel-filament)
- [Installation & Running Guide](#️-installation--running-guide)
- [Advanced Architecture](#️⃣-advanced-architecture)
- [Code Style and Principles](#️-code-style-and-principles)
- [Design Philosophy](#-design-philosophy)
- [License](#-license)

A clean and modular **Forex Swap Calculator** built with **Laravel 12**.  
This project allows users to calculate total swap profit or loss based on trading parameters such as currency pair, lot size, swap rates, position type, and holding days.  
The system validates inputs, performs instant AJAX calculations, stores results in the database, and displays recent calculation history.

Demo Visit: [https://test-swap-calculator.2etitb.easypanel.host/](https://test-swap-calculator.2etitb.easypanel.host/)
---

## 🧩 General Information

- **Framework:** Laravel 12.x  
- **PHP Version:** ≥ 8.2  
- **Database:** MySQL / MariaDB  
- **Frontend:** Blade + TailwindCSS + Fetch API  
- **Architecture:** MVC + Service–Repository–DTO Pattern  
- **License:** MIT  

---

## 🧮 Core Functionality

- Calculate **Total Swap** based on the formula:  
  `Total Swap = Lot Size × Swap Rate × Holding Days`
- Select **Long or Short** position (swap direction changes automatically)
- Instant validation and calculation (via Fetch API)
- Save every calculation to the database (`swap_calculations` table)
- Display **calculation history** with filters, pagination, and delete actions
- Built with clean separation of logic using **DTO, Service, and Repository layers**

---

## 🖥️ Admin Panel (Filament)

The project includes **Filament Admin (v4)** for data management:

- Manage swap calculation history (CRUD)
- Import/Export CSV data
- Auto-calculate `total_swap` when importing

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
Can run seeder
```bash
php artisan db:seed
```

### 5️⃣ Start the development server
```bash
php artisan serve
```

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

## 6️⃣ Advanced Architecture

This project follows a **Service–Repository–DTO architecture** within the Laravel ecosystem.  
Each layer is independent and testable, ensuring clean separation of concerns and maintainability.

### 🧱 `app/DTOs`
- **`SwapInputDTO.php`**  
  Data Transfer Object (DTO) encapsulating user input fields such as `pair`, `lot_size`, `swap_long`, `swap_short`, `position_type`, and `days`.  
  Used to safely pass structured data from the controller to the service layer.

---

### ⚙️ `app/Http`
- **`Controllers/SwapController.php`**  
  Handles both web form submissions and AJAX requests.  
  Uses the `SwapService` to compute total swap, then returns JSON or Blade responses.  
  Designed to support both synchronous and asynchronous calculation workflows.

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
  Responsible for retrieving swap rates (long/short) for each currency pair.  
  Provides a unified interface for the service layer to access rate data from the database or external sources.

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

## ✳️ Code Style and Principles

- PSR-12 compliant, using strict typing (`declare(strict_types=1)`).
- Service and Repository layers are dependency-injected for testability.
- DTOs ensure immutable and type-safe data transfer.
- Comments follow Laravel docblock conventions.

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
