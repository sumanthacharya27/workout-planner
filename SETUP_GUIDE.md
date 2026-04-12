╔════════════════════════════════════════════════════════════════════╗
║        GYM PLANNER - FULL STACK (PHP + MySQL) SETUP GUIDE        ║
╚════════════════════════════════════════════════════════════════════╝

================== OVERVIEW ==================
This is a complete conversion of your localStorage-based Gym Planner
into a full-stack application with:
✓ PHP Backend
✓ MySQL Database
✓ User Authentication (Admin only)
✓ Admin Panel for managing workouts
✓ Persistent data storage
✓ Preserved UI and functionality

================== REQUIREMENTS ==================
1. XAMPP (Apache + MySQL + PHP)
   - Download from: https://www.apachefriends.org/
   - Version 7.4+ recommended
   - Default setup is fine

2. Modern Web Browser
   - Chrome, Firefox, Safari, or Edge

================== INSTALLATION STEPS ==================

STEP 1: Download & Install XAMPP
────────────────────────────────
1. Go to https://www.apachefriends.org/
2. Download XAMPP for your OS
3. Install with default settings
4. Remember installation location (typically C:\xampp or /Applications/XAMPP)

STEP 2: Start XAMPP Services
────────────────────────────
1. Open XAMPP Control Panel
2. Start these services:
   ✓ Apache
   ✓ MySQL
   
Status should show "Running" in green

STEP 3: Prepare Project Files
────────────────────────────
The project structure should be:

gym-planner/
├── config/
│   └── db.php                 (Database connection)
├── api/
│   ├── get_workouts.php
│   ├── save_workout.php
│   ├── save_history.php
│   ├── get_history.php
│   ├── get_stats.php
│   ├── admin_save_workout.php
│   ├── delete_workout.php
│   └── update_workout.php
├── assets/
│   ├── styles.css
│   └── app.js
├── index.html                 (Main app - requires login)
├── login.php                  (Login page)
├── logout.php                 (Logout)
└── database.sql               (Database schema)

STEP 4: Create Database
────────────────────────
1. Open phpMyAdmin:
   - Open browser
   - Go to: http://localhost/phpmyadmin

2. Create Database:
   - Click "New" on left sidebar
   - Database name: gym_planner
   - Collation: utf8mb4_unicode_ci
   - Click "Create"

3. Import SQL File:
   - Select "gym_planner" database
   - Click "Import" tab
   - Click "Choose File"
   - Select: database.sql
   - Click "Import"
   
   You should see these tables:
   ✓ users (with admin account)
   ✓ workouts (with 6 pre-made workouts)
   ✓ exercises (with pre-made exercises)
   ✓ workout_history
   ✓ user_stats

STEP 5: Set Up Project Files
────────────────────────────
WINDOWS:
1. Extract project to: C:\xampp\htdocs\gym-planner

MAC:
1. Extract project to: /Applications/XAMPP/htdocs/gym-planner

LINUX:
1. Extract project to: /opt/lampp/htdocs/gym-planner

STEP 6: Verify File Permissions (Linux/Mac)
───────────────────────────────────────────
Run in terminal:
chmod 755 gym-planner
chmod 755 gym-planner/api
chmod 755 gym-planner/config

STEP 7: Start Using the App
────────────────────────────
1. Open browser
2. Go to: http://localhost/gym-planner/
3. You will be redirected to login.php
4. Login with:
   Username: admin
   Password: admin123

================== FILE STRUCTURE DETAILS ==================

config/db.php
─────────────
- Database connection configuration
- PDO setup with error handling
- Helper functions for API responses
- Change DB_HOST, DB_USER, DB_PASS if needed

database.sql
────────────
- MySQL schema with all tables
- Pre-made workout data
- Admin user account (hashed password)
- Indexes for performance

login.php
─────────
- Admin authentication
- Session management
- Redirects to index.html on success
- Redirects to login.php if not authenticated

index.html
──────────
- Main application (PHP wrapper)
- Checks session before loading
- Identical UI to original
- Added Admin panel section
- Added logout link

api/ folder
───────────
All endpoints return JSON

get_workouts.php
  - Returns all workouts with exercises
  - No authentication required
  
save_workout.php
  - Creates custom workouts
  - Requires authentication
  - Saves exercises with proper order
  
save_history.php
  - Records completed workouts
  - Updates user stats
  - Calculates streak
  
get_history.php
  - Fetches workout history
  - Supports filters: all, week, month
  
get_stats.php
  - Returns user statistics
  - Includes total workouts, time, streak
  
admin_save_workout.php
  - Creates pre-made workouts (admin only)
  - Requires authentication
  
delete_workout.php
  - Deletes workouts (cascades to exercises)
  - Requires authentication
  
update_workout.php
  - Updates workout details and exercises
  - Requires authentication

assets/app.js
─────────────
- Rewritten to use backend API calls
- Replaces localStorage with fetch() requests
- All functions prefixed with documentation
- Error handling and user feedback
- Admin functions for workout management

assets/styles.css
──────────────────
- Original CSS (unchanged)
- All styles preserved
- Responsive design intact

================== SECURITY NOTES ==================

1. Password Security
   - Passwords stored using bcrypt hashing
   - password_verify() used for authentication
   - Default admin password should be changed in production

2. SQL Injection Prevention
   - All queries use prepared statements (PDO)
   - Parameters bound separately
   - No string concatenation in queries

3. Session Security
   - Sessions managed by PHP
   - Authentication checked on protected pages
   - Logout destroys session

4. Input Validation
   - All user input sanitized with htmlspecialchars
   - Type casting for numeric values
   - Required field validation

================== TROUBLESHOOTING ==================

Problem: "Database connection failed"
Solution:
  1. Check MySQL is running in XAMPP
  2. Verify database name is "gym_planner"
  3. Check credentials in config/db.php
  4. Try resetting MySQL in XAMPP Control Panel

Problem: "API endpoints returning 404"
Solution:
  1. Check file paths match directory structure
  2. Verify .htaccess is in project root (if needed)
  3. Check Apache is running
  4. Ensure api/ folder exists and has all files

Problem: "Login not working"
Solution:
  1. Import database.sql file again
  2. Clear browser cookies
  3. Check phpmyadmin that users table exists
  4. Verify password hash in database

Problem: "White screen after login"
Solution:
  1. Check browser console for JS errors (F12)
  2. Check network tab to see API call failures
  3. Verify all api/*.php files exist
  4. Check PHP error log in XAMPP

Problem: "Workouts not loading"
Solution:
  1. Open browser console (F12)
  2. Check Network tab for api/get_workouts.php response
  3. Verify database has workout data
  4. Check for JSON parse errors

================== CHANGING DEFAULT PASSWORD ==================

1. Generate new hashed password:
   - Use an online PHP hashing tool or
   - Run this PHP code in your browser:
     <?php echo password_hash('your_new_password', PASSWORD_DEFAULT); ?>

2. Update database:
   - Go to phpMyAdmin
   - Select gym_planner database
   - Click users table
   - Edit admin user
   - Replace password hash in password field
   - Click Save

================== DATABASE BACKUP ==================

Export Database:
1. Go to phpMyAdmin
2. Select gym_planner
3. Click "Export"
4. Format: SQL
5. Click "Go"
6. Save as gym_planner_backup.sql

Restore Database:
1. Go to phpMyAdmin
2. Select gym_planner
3. Click "Import"
4. Choose backup file
5. Click "Import"

================== FEATURES ==================

Dashboard:
✓ View workout statistics
✓ See current streak
✓ Quick action buttons
✓ Recent workouts list

Browse Workouts:
✓ Filter by difficulty (All, Beginner, Intermediate, Advanced)
✓ View pre-made workout details
✓ Start any workout
✓ See exercise count

Create Custom Workout:
✓ Add custom workouts
✓ Save exercises with reps/sets/weight
✓ View saved custom workouts
✓ Edit/delete custom workouts

Workout Execution:
✓ Exercise-by-exercise display
✓ Track set completion
✓ Rest timer between sets
✓ Progress indicator
✓ Complete or quit workout

History:
✓ View all completed workouts
✓ Filter by time period
✓ See duration and exercise count
✓ Track fitness journey

Progress:
✓ Charts showing workout frequency
✓ Exercise distribution stats
✓ Personal records tracking
✓ Achievement system

Admin Panel:
✓ Manage pre-made workouts
✓ Edit workout details
✓ Add/remove exercises
✓ Delete workouts
✓ Create new pre-made workouts

================== API ENDPOINTS REFERENCE ==================

GET /api/get_workouts.php
  Returns: { success: true, data: [workouts...] }

GET /api/get_history.php?filter=all|week|month
  Returns: { success: true, data: [history...] }

GET /api/get_stats.php
  Returns: { success: true, data: { total_workouts, total_time, ... } }

POST /api/save_workout.php
  Body: { name, description, exercises: [...] }
  Returns: { success: true, workoutId: number }

POST /api/save_history.php
  Body: { workoutId, duration, notes }
  Returns: { success: true }

POST /api/admin_save_workout.php
  Body: { name, description, difficulty, exercises: [...] }
  Returns: { success: true, workoutId: number }

POST /api/update_workout.php
  Body: { workoutId, name, description, difficulty, exercises: [...] }
  Returns: { success: true }

POST /api/delete_workout.php
  Body: { workoutId: number }
  Returns: { success: true }

================== PERFORMANCE NOTES ==================

Database Indexes:
- workout_id on exercises table
- workout_id on workout_history table
- completed_at on workout_history (for date filtering)
- difficulty on workouts (for filtering)

These ensure fast queries even with large datasets.

JSON Format:
- All APIs return JSON for easy frontend parsing
- Consistent response structure with success flag
- Error messages included in responses

Prepared Statements:
- All queries use PDO prepared statements
- Prevents SQL injection
- Better performance than string concatenation

================== NEXT STEPS ==================

1. Test Login:
   - Username: admin
   - Password: admin123

2. View Pre-Made Workouts:
   - Click "Browse Workouts"
   - Should see 6 pre-made workouts

3. Start a Workout:
   - Click on any workout
   - Click "Start Workout"
   - Complete the workout
   - History should record it

4. Create Custom Workout:
   - Click "Create"
   - Add exercises
   - Save and start

5. Admin Panel:
   - Click "Admin" in nav
   - Manage or create workouts
   - Edit pre-made workouts

6. Change Default Password:
   - Use phpMyAdmin to update hash
   - Or see "Changing Default Password" section

================== SUPPORT ==================

Common Issues:
1. Port 3306 blocked?
   - Change MySQL port in XAMPP
   - Update config/db.php port

2. Large workouts slow?
   - Add more database indexes
   - Implement pagination

3. Need custom fields?
   - Modify database.sql
   - Update API endpoints
   - Update frontend form

================== VERSION INFO ==================

Version: 1.0.0 Full-Stack
Created: 2024
Database: MySQL 5.7+
PHP: 7.4+
Frontend: Vanilla JS (No frameworks)
Browser Support: All modern browsers

================== LICENSE ==================

This project is provided as-is for personal use.
Modify freely for your needs.

═══════════════════════════════════════════════════════════════════════════════

Ready to go! Follow the installation steps above and you'll be up and running
in minutes. Questions? Check the TROUBLESHOOTING section.

Good luck with your gym tracking! 💪
