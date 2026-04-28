# 🏋️ MuscleMap - Project Flow & Execution Guide

This document provides a comprehensive overview of how the **MuscleMap** application operates and instructions on how to set it up locally.

---

## 🚀 Setup & Installation

Since this is a full-stack PHP/MySQL application, it requires a local server environment (XAMPP or WAMP).

### 1. Environment Requirements
*   **Server**: Apache (via XAMPP/WAMP)
*   **PHP**: 7.4 or higher
*   **Database**: MySQL 5.7+

### 2. Installation Steps
1.  **Move Files**: Place the `gym_workout-planner` folder inside your server's root directory (e.g., `C:\xampp\htdocs\`).
2.  **Start Services**: Open your XAMPP Control Panel and start **Apache** and **MySQL**.
3.  **Create Database**:
    *   Open [http://localhost/phpmyadmin/](http://localhost/phpmyadmin/).
    *   Create a new database named `workout_planner`.
4.  **Import Schema**:
    *   Select the `workout_planner` database.
    *   Go to the **Import** tab.
    *   Choose the `database.sql` file from the project root and click **Go**.
5.  **Configure Connection**:
    *   Open `config/db.php`.
    *   Ensure the `DB_USER` (default: `root`) and `DB_PASS` (default: `""`) match your local MySQL settings.

### 3. Accessing the App
Open your browser and navigate to:
`http://localhost/gym_workout-planner`

### 4. Default Credentials
*   **Admin Username**: `admin`
*   **Admin Password**: `admin123`
*   **Standard User**: Register a new account via the UI.

---

## 🔄 Flow of Execution

The application is designed as a **Modular SPA (Single Page Application)** where PHP handles the session and data persistence, while JavaScript manages the UI state.

### 1. Authentication Layer
*   **`index.php`**: The gateway. It starts a session and checks for a `user_id`. If missing, it forces a redirect to the login page.
*   **`login.php` / `register.php`**: Securely authenticates users. On success, it populates the `$_SESSION` superglobal with user metadata (ID, Name, Role).

### 2. Main Application Shell (`index.php`)
Once authenticated, `index.php` serves as the primary container:
*   **Data Injection**: It prints a small JavaScript block (`window.APP_CONFIG`) containing the user's session data, allowing the frontend to remain in sync with the backend.
*   **Section Inclusion**: It uses PHP `include` statements to load all functional modules (Dashboard, Workouts, History, Progress, etc.) into the DOM at once, initially hidden via CSS.
*   **Role Enforcement**: It checks `isAdmin()` to conditionally render administrative links and the management panel.

### 3. Frontend Logic (`assets/app.js`)
The "brain" of the client-side experience:
*   **Navigation**: Listens for clicks on the `.nav-link` elements. When clicked, it updates the "Active" class and toggles the visibility of the corresponding sections in the `container`.
*   **Interactivity**: Manages complex UI states like the "Workout Execution" timer and set-tracking without requiring page reloads.

### 4. RESTful API Layer (`api/`)
The frontend communicates with the database exclusively through these endpoints:
*   **Workflow**: `app.js` sends an `async` fetch request -> `api/*.php` verifies the session -> Performs SQL query via PDO -> Returns a JSON response.
*   **Key Endpoints**:
    *   `get_workouts.php`: Fetches both admin templates and user-created plans.
    *   `save_history.php`: Records a completed session and updates the user's streak/stats.

### 5. Data Architecture (`database.sql`)
*   **`users`**: Manages credentials (hashed with Bcrypt) and RBAC roles.
*   **`workouts` & `exercises`**: A one-to-many relationship defining the routines.
*   **`user_stats`**: Stores cached performance metrics (streaks, totals) to ensure the Dashboard loads instantly.

---

## 📁 Directory Structure Summary

| Directory | Purpose |
| :--- | :--- |
| `admin/` | UI components restricted to administrative users. |
| `user/` | Core feature modules for standard workout tracking. |
| `api/` | PHP endpoints for AJAX data transactions. |
| `assets/` | Global CSS styles and the main JavaScript controller. |
| `config/` | Database connection logic and global helper functions. |
