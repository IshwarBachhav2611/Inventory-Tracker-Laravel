# Inventory Tracker

A simple web application built with Laravel to manage inventory, suppliers, stock movements, and sales. It provides a dashboard, low-stock alerts, and date-wise reports to help track inventory efficiently.

## Tech Stack

- **Backend:** Laravel 12, PHP 8.2+
- **Frontend:** Blade, HTML, CSS, JavaScript, Bootstrap 5
- **Database:** MySQL
- **Charts:** Chart.js
- **Icons:** Bootstrap Icons

## Features

- **Authentication:** Register, login, and logout.
- **Dashboard:** View inventory summaries, low-stock alerts, and recent transactions.
- **Inventory Management:** Add, edit, search, and manage items using popup forms.
- **Stock Management:** Record stock-in and stock-out transactions.
- **Sales Management:** Record sales with multiple items, quantities, and selling prices.
- **Supplier Management:** Add, edit, view, and manage supplier details.
- **Reports:** View sales and stock reports by month or date range.
- **Low-Stock Alerts:** Automatically identify items that need restocking.

## Project Structure

```text
inventory-tracker/
├── app/
│   ├── Http/Controllers/
│   └── Models/
├── database/
│   └── migrations/
├── resources/
│   └── views/
│       ├── auth/
│       ├── layouts/
│       ├── items/
│       ├── suppliers/
│       ├── reports/
│       ├── dashboard.blade.php
│       └── profile.blade.php
├── routes/
│   └── web.php
├── .env.example
└── README.md
```

## Database Tables

- `users` — User accounts and authentication.
- `items` — Item details, quantities, and minimum stock levels.
- `suppliers` — Supplier contact information.
- `transactions` — Stock-in and stock-out history.
- `sales` — Sales records and total amounts.
- `sale_items` — Items, quantities, and prices for each sale.

## Installation

**Requirements:** PHP 8.2+, Composer, and MySQL.

1. Clone the repository and enter the project directory.

   ```bash
   git clone https://github.com/your-username/inventory-tracker.git
   cd inventory-tracker
   ```

2. Install dependencies and configure the application.

   ```bash
   composer install
   cp .env.example .env
   php artisan key:generate
   ```

3. Create a MySQL database named `inventory_tracker` and update the database credentials in `.env`.

   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=inventory_tracker
   DB_USERNAME=root
   DB_PASSWORD=your_password
   ```

4. Run migrations and start the application.

   ```bash
   php artisan migrate
   php artisan serve
   ```

5. Open **http://127.0.0.1:8000** in your browser and register an account.

## Important Rules

- Stock quantities update automatically through stock movements and sales.
- Sales and stock-out transactions cannot exceed available stock.
- Sales and their corresponding stock updates are saved together.
- Historical sales retain their original selling prices.
- Items with transaction history are deactivated instead of permanently deleted.

## Future Improvements

Barcode scanning, multiple warehouses, and advanced user permissions can be added in future versions.

## Author

**Ishwar Bachhav**  
GitHub: [@IshwarBachhav2611](https://github.com/IshwarBachhav2611/Inventory-Tracker-Laravel)
