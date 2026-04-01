# 🎟️ Passage

A comprehensive, robust event management system built with **Laravel 10+ / 13**, **PHP 8.4**, **MySQL**, and **Tailwind CSS**. **Passage** allows users to discover, create, update, and securely book tickets for events. It features a complete administrative backend, role-based access control, file uploads, and transactional integrity for ticket bookings.

---

## ✨ Key Features

- **🎫 Event Management**: Full CRUD capabilities for events. Users can create, edit, view, and (soft) delete their own events.
- **🔒 Secure Ticket Booking**: Implements database row-level locking (`lockForUpdate()`) within transactions to precisely manage `available_tickets` and prevent race conditions (overselling).
- **🛡️ Role-Based Access Control (RBAC)**: Secure middleware (`EnsureUserOwnsEvent`, `EnsureUserIsAdmin`) restricts editing capabilities to event owners and administrative actions to admins.
- **👨‍💼 Admin Dashboard**: A comprehensive admin panel to view platform statistics, enforce moderation by force-purging or restoring soft-deleted events, manage categories/tags, and manage user roles.
- **🖼️ Media Uploads**: Integrated local storage for user profile pictures and event poster images.
- **🏷️ Categorization & Tags**: Organize events dynamically using database-driven categories and many-to-many tag relationships.
- **🔌 API Ready**: Equipped with `EventResource` JSON transformers, making it fully ready to serve data to mobile clients.

---

## 🛠️ Tech Stack

- **Backend**: Laravel 13, PHP 8.4
- **Frontend**: Laravel Blade, Tailwind CSS
- **Mobile**: Flutter, Dart
- **Database**: MySQL (Eloquent ORM)
- **Authentication**: Laravel Breeze / Application Auth / Sanctum (for API)

---

## 🚀 Getting Started

Follow these instructions to get a copy of the project up and running on your local machine for development and testing purposes.

### Prerequisites

Ensure you have the following installed on your local machine:
- **PHP** >= 8.3
- **Composer**
- **Node.js** & **npm**
- **MySQL** Database

### Installation

1. **Clone the repository** (if you haven't already):
   ```bash
   git clone <repository-url>
   cd Passage
   ```

2. **Run the Setup Script**:
   We have configured a handy composer script that installs all dependencies, generates the `.env` file, generates the application key, runs migrations, and builds the frontend assets.
   ```bash
   composer run setup
   ```
   *Note: Make sure your database credentials in the newly created `.env` file are correct before running migrations. You may need to create the database manually first.*

3. **Link Storage**:
   Make sure the public storage directory is linked so uploaded images display correctly.
   ```bash
   php artisan storage:link
   ```

### Running the Application

To start the development environment (which concurrently runs the Laravel server, Vite for frontend asset compilation, queue worker, and logs):

```bash
composer run dev
```

Visit the application at `http://localhost:8000` or your configured local URL.

---

## 📱 Mobile Application (EventPass)

The mobile application is built with **Flutter** and serves as a scanner and event discovery tool.

### Features
- **🔍 Event Discovery**: Browse all active events on the platform.
- **🚀 Ticket Scanning**: Admins and event owners can scan ticket QR codes (built-in scanner).
- **💳 Secure Booking**: Seamless integration with the web backend for ticket reservations.
- **🌙 Dark/Light Mode**: Full theme support for a premium user experience.

### Flutter Setup

1. **Navigate to the app directory**:
   ```bash
   cd event_pass_app
   ```

2. **Install Flutter dependencies**:
   ```bash
   flutter pub get
   ```

3. **Configure API Endpoint**:
   Update the `lib/services/api_service.dart` file (or your configuration file) to point to your local or hosted Laravel API URL.

4. **Run the application**:
   ```bash
   flutter run
   ```

---

## 🏗️ System Architecture Highlights

### Database Relationships
- **Users**: Can create **Events** (1-to-many) and place **Bookings** (1-to-many).
- **Events**: Belong to a **Category** (many-to-1) and have many **Tags** (many-to-many via `event_tag` pivot).
- **Bookings**: Pivot representing a User reserving a spot at an Event.

### Soft Deletes & Moderation
When a user deletes their event, it is **Soft Deleted** (`deleted_at` timestamp is set). It is hidden from standard public queries but remains in the database. Site Admins can view these events in the Admin Dashboard and choose to either **Restore** them or **Force Delete** them permanently.

### Transactional Booking Integrity
When a user books a ticket, Passage uses `DB::transaction()` paired with `Event::lockForUpdate()` to read and decrement the available ticket count. This ensures that even under heavy concurrent load, the system will never oversell event tickets.

---

## 📚 Documentation
For an exhaustive, deep-dive into the architectural decisions, database schemas, full request lifecycles, and route maps, please refer to the internal documentation:
- [`crud_documentation.md`](./crud_documentation.md)

---

## 🛡️ Security Vulnerabilities
If you discover a security vulnerability within Passage, please report it internally. All security vulnerabilities will be promptly addressed.

## 📄 License
This project is proprietary. All rights reserved.
