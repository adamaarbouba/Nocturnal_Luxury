# Nocturnal Luxury — Hotel Management System

![Project Theme](https://img.shields.io/badge/Theme-Nocturnal%20Luxury-A0717F?style=for-the-badge)
![Tech Stack](https://img.shields.io/badge/Stack-Laravel%20%7C%20Tailwind%20%7C%20Vite-black?style=for-the-badge)

## Project Bio

#### Welcome to the Nocturnal Luxury Hotel Management System (HSM). This system connects your guest bookings directly to your facility management. Guests use it to reserve rooms, and your team uses it to run the hotel.

## Technology

- **Framework**: Laravel 11
- **Styling**: Tailwind CSS with a custom **Nocturnal Luxury** design system.
- **Database**: MySQL with a robust relational architecture for complex booking and staff workflows.

---

## Starter Commandes

### Prerequisites

- **PHP**: 8.2 or higher
- **Composer**
- **Node.js**: 20.x or higher
- **MySQL**

### Installation

1. **Initialize Dependencies**:
    ```bash
    composer install
    npm install
    ```
2. **Environment Configuration**:
    ```bash
    cp .env.example .env
    php artisan key:generate
    ```
3. **Database Setup**:
    ```bash
    php artisan migrate --seed
    ```
4. **Build & Serve**:
    ```bash
    npm run build
    php artisan serve
    ```
