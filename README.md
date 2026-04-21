# Nocturnal Luxury — Hotel Management System

![Project Theme](https://img.shields.io/badge/Theme-Nocturnal%20Luxury-A0717F?style=for-the-badge)
![Tech Stack](https://img.shields.io/badge/Stack-Laravel%20%7C%20Tailwind%20%7C%20Vite-black?style=for-the-badge)

Welcome to the **Nocturnal Luxury** Hotel Management System (HSM). This platform is a high-end, multi-role ecosystem designed to transform hotel operations into a seamless, curated experience. From the elegant guest portal to the sophisticated staff dashboards, every interaction is crafted with a premium aesthetic and enterprise-grade logic.

---

## 🏛️ Project Essence
Our platform bridge the gap between discerning guests and high-performance hotel staff. It features a bespoke UI/UX inspired by nocturnal luxury—utilizing a palette of warm greys, rose gold accents, and elegant "glassmorphic" components.

---

## 🛠️ Technology Suite
- **Framework**: [Laravel 11](https://laravel.com/) (PHP 8.2+)
- **Styling**: [Tailwind CSS](https://tailwindcss.com/) with a custom **Nocturnal Luxury** design system.
- **Assets**: [Vite](https://vitejs.dev/) for high-performance frontend orchestration.
- **Database**: [MySQL](https://www.mysql.com/) with a robust relational architecture for complex booking and staff workflows.
- **Icons**: Custom SVG component-based library with interactive glows and Rose Gold gradients.
- **Typography**: Sophisticated pairing of **Inter** (Sans) and **Playfair Display** (Serif).

---

## 🎭 User Roles & Capabilities

### 🧳 The Guest (The Curator)
The guest experience is focused on effortless luxury and transparent management.
- **Booking Hub**: Browse curated hotels and rooms with real-time availability.
- **Secure Payments**: Process bookings through a simulated Rose Gold payment portal.
- **Refund Management**: Securely request refunds directly from the dashboard for receptionist review.
- **Legacy Reviews**: Share experiences through a premium, interactive rating system featuring "Perspective Pillars" and animated feedback.

### 🛎️ The Receptionist (The Orchestrator)
The front-line managers overseeing the hotel's heartbeat.
- **Dynamic Dashboard**: Monitor real-time room occupancy and guest statuses.
- **Check-in/Out**: Manage the guest lifecycle from arrival to departure.
- **Walk-in Registration**: Register and book stays for guests directly at the front desk.
- **Financial Control**: Record cash payments, confirm online deposits, and manage pending refund requests.

### 🧹 The Cleaner (The Artisan)
The essential staff maintaining the hotel's impeccability.
- **Status Workflow**: Mobile-optimized interface to track room statuses.
- **Task Management**: Transition rooms from `Dirty` to `Cleaned` immediately upon completion.
- **Stay Visibility**: View room details and occupancy to plan cleaning schedules effectively.

### 🔍 The Inspector (The Quality Guardian)
Ensuring every room meets the highest standards of luxury.
- **Inspection Logic**: Verify rooms marked as `Cleaned`.
- **Status Finalization**: Authorize rooms to return to `Available` status or mark them for rework.
- **QA Reports**: Monitor cleaning performance across the assigned hotel.

### 👔 The Owner (The Strategist)
High-level management of the hotel's growth and staff.
- **Hotel Operations**: Create and manage multiple hotel properties.
- **Staff Onboarding**: Review applications from Cleaners, Inspectors, and Receptionists; manage their status and hotel assignments.
- **Business Intelligence**: View reports on revenue and occupancy.

### 🛡️ The Admin (The Architect)
System-wide governance and configuration.
- **Platform Management**: Oversee all users, hotels, and global system settings.

---

## 🚀 Getting Started

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

---

*Designed with 🌙 Nocturnal Luxury in mind.*