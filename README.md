# 🏋️ MuscleMap - Advanced Gym Workout Planner ✅

## 📋 PROJECT STATUS: FULLY COMPLETED & ENHANCED

Your gym workout planner has been successfully evolved into **MuscleMap**, a secure, multi-user, full-stack PHP/MySQL application with professional-grade features and modular architecture.

✅ **Robust RBAC** - Role-Based Access Control (Admin vs. User)  
✅ **Multi-User Isolation** - Personal data tracking for every user  
✅ **Body Calculator** - Integrated BMI, BMR, and TDEE calculation suite  
✅ **Modular Architecture** - Clean separation of concerns with `admin/` and `user/` modules  
✅ **Refined UI/UX** - Personalized greetings and smooth animations  
✅ **Security Hardened** - Bcrypt hashing, session-based role enforcement, and prepared SQL statements  

---

## 📁 UPDATED PROJECT STRUCTURE
```
gym_workout-planner/
├── index.php                   # Secure App Shell (Role-enforced)
├── login.php                   # Secure Authentication System
├── register.php                # Multi-user Stats Initialization
├── config/
│   └── db.php                  # Central DB connection & RBAC helpers
├── api/                        # REST API Layer
│   ├── get_workouts.php        # Shared workout fetching
│   ├── save_workout.php        # User custom workout logic
│   ├── admin_save_workout.php  # Admin-only template creation
│   └── ... (other endpoints)
├── admin/                      # Administrative Module
│   └── admin_panel.php         # Template & User management UI
├── user/                       # User Feature Module
│   ├── dashboard.php           # Personalized user overview
│   ├── calculator.php          # BMI & BMR logic suite
│   ├── workouts.php            # Workout selection & browsing
│   ├── history.php             # Personal workout logs
│   ├── progress_section.php    # Advanced analytics & charts
│   └── modals.php              # Shared UI components
└── assets/                     # Core Assets
    ├── app.js                  # Frontend Controller (SPA Logic)
    └── styles.css              # Modern UI System
```

---

## ✨ KEY FEATURES

### 👤 User Personalization
- **Personalized Dashboard**: Greets you by name and provides real-time statistics on your fitness journey.
- **Data Isolation**: Workouts, history, and stats are securely isolated to your account.

### 📐 Body Calculator Suite
- **BMI Calculator**: Real-time BMI calculation with visual scale positioning and health category advice.
- **BMR & TDEE**: High-precision metabolism tracking using the **Mifflin-St Jeor Equation**.
- **Calorie Goals**: Automatic breakdown of daily calorie targets for Weight Loss, Maintenance, and Muscle Gain.

### 📋 Workout Management
- **Pre-Made Workouts**: Access curated plans designed by administrators.
- **Custom Builder**: Create, edit, and delete your own personalized routines.
- **Interactive Tracking**: Real-time workout execution with set-tracking and progress indicators.

### 🛡️ Administrative Controls
- **Template Management**: Create and manage global workout templates available to all users.
- **Secure Access**: Admin features are strictly protected via server-side session role verification (`isAdmin()`).

---

## 🚀 SETUP & INSTALLATION

### **1. Environment Requirements**
- **Server**: Apache (via XAMPP/WAMP)
- **PHP**: 7.4 or higher
- **Database**: MySQL 5.7+

### **2. Database Configuration**
1. Create a database named `gym_planner`.
2. Import the `database.sql` file provided in the repository.
3. Configure `config/db.php` with your local credentials (default: `root` with no password).

### **3. Initial Credentials**
- **Standard User**: [Register your own account]
- **Admin User**:
    - **Username:** `admin`
    - **Password:** `admin123`

---

## 🔒 SECURITY & ARCHITECTURE

✅ **Bcrypt Encryption**: Hashed passwords using `PASSWORD_DEFAULT`.  
✅ **SQL Injection Protection**: 100% adherence to PDO Prepared Statements.  
✅ **Namespace Isolation**: UI fragments consolidated into module directories for easier maintenance.  
✅ **Server-Side RBAC**: Security checks are performed on every API request and page include.  

---

## 🛠️ TROUBLESHOOTING

- **"Access Denied"**: Ensure you have an active session. If trying to reach Admin features, ensure your user role in the `users` table is set to `admin`.
- **Chart Issues**: Ensure you have an active internet connection to load the `Chart.js` via CDN.
- **Calculator Unresponsive**: Check the browser console (F12) for potential JavaScript errors during calculation.

---

**Built with passion for fitness & clean code. 💪 Happy Tracking!**
