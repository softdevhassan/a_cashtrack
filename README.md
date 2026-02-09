# CashTrack - Lifetime Expense Manager

## 🚀 Project Overview

**CashTrack** is a premium, self-hosted personal finance manager designed for individuals who want complete control over their financial data. Unlike subscription-based SaaS apps, CashTrack offers a **lifetime access model** based on transaction limits, not time.

### Key Features

- **Multi-Wallet Management**: Track cash, bank accounts, credit cards, and digital wallets in one place.
- **Smart Transactions**: Record Income, Expenses, and Transfers with double-entry bookkeeping.
- **Visual Analytics**: Beautiful charts and insights to track your spending habits.
- **Lifetime Plans**: No monthly fees. Use the Free Trial forever (up to 50 transactions) or upgrade to higher limits.
- **Privacy Focused**: Your data stays on your server. No third-party tracking.
- **Modern UI**: Fully responsive light-themed interface built with Tailwind CSS.

---

## 🛠️ Technical Architecture

### Tech Stack

- **Backend**: PHP 8.2+ (Vanilla, optimized for speed)
- **Database**: MySQL 8.0+
- **Frontend**: HTML5, Tailwind CSS (via CDN), Remix Icons
- **Authentication**: Secure session-based auth with CSRF protection

### Database Schema

The system uses a clean relational schema:

- `users`: Accounts and profile data.
- `wallets`: Financial accounts with currency support.
- `transactions`: Core financial records (linked to wallets/categories).
- `categories`: System and user-defined transaction categories.
- `plans` & `subscriptions`: Manages lifetime transaction limits.

---

## 🚀 Quick Start

Get up and running in less than 2 minutes:

1.  **Configure Environment**:
    Copy `.env.example` to `.env` and update your database credentials:

    ```ini
    DB_SERVER=localhost
    DB_NAME=a_cashtrack
    DB_USER=root
    DB_PASS=your_password
    ROOT_PATH=/a_cashtrack # Update if installed in subfolder
    ```

2.  **Run Setup Script**:
    Navigate to the setup page in your browser to initialize the database:
    `http://localhost/a_cashtrack/database/setup.php`

    _This will create all tables and seed default data._

3.  **Login**:
    Use the default admin credentials:
    - **Username**: `admin`
    - **Password**: `admin123`

---

## ⚡ Feature Guide

### 1. Wallets

Create wallets for different asset types (Cash, Bank, Credit Card). CashTrack supports multiple currencies (defaulting to PKR).

### 2. Transactions

- **Income**: Adds to wallet balance.
- **Expense**: Deducts from wallet balance.
- **Transfer**: Moves money between wallets (deducts from source, adds to destination).

### 3. Plans & Limits

CashTrack operates on a "Simulated SaaS" model for demonstration:

- **Free Trial**: 50 Transactions, 2 Wallets.
- **Starter**: 500 Transactions, 3 Wallets.
- **Professional**: 1000 Transactions, 4 Wallets.
- **Enterprise**: 2000 Transactions, 5 Wallets.

_Note: The admin user typically has specific permissions or overrides._

---

## 👨‍💻 Developer Guide

### Folder Structure

```
/
├── api/                 # AJAJ Endpoints (Transactions, Wallets)
├── auth/                # Login, Signup, Password Reset
├── database/            # Migrations and Seeds
│   ├── migrations/      # .sql structure files
│   └── seeds/           # defaut data
├── includes/            # Core functions and config
├── pages/               # Application views
│   ├── dashboard.php    # Main overview
│   ├── wallets.php      # Wallet management
│   ├── transactions.php # Transaction history
│   └── ...
├── index.php            # Router
└── .env                 # Configuration
```

---

## 📄 License

Private proprietary software. All rights reserved.
