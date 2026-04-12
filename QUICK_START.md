╔════════════════════════════════════════════════════════════════════╗
║            GYM PLANNER - QUICK START CHECKLIST                   ║
╚════════════════════════════════════════════════════════════════════╝

This file helps you verify everything is set up correctly.

================== PRE-INSTALLATION CHECKLIST ==================

□ XAMPP installed and running (Apache + MySQL)
□ Project files extracted to htdocs folder
□ Browser ready

================== FILE VERIFICATION CHECKLIST ==================

Root Directory:
□ index.html (main app with PHP session check)
□ login.php (login page)
□ logout.php (logout handler)
□ database.sql (SQL schema file)
□ SETUP_GUIDE.md (detailed setup instructions)
□ QUICK_START.md (this file)

config/ Folder:
□ db.php (database configuration)

api/ Folder:
□ get_workouts.php (fetch all workouts)
□ save_workout.php (save custom workouts)
□ save_history.php (save workout completion)
□ get_history.php (fetch workout history)
□ get_stats.php (fetch user statistics)
□ admin_save_workout.php (create pre-made workouts)
□ delete_workout.php (delete workouts)
□ update_workout.php (update workout details)

assets/ Folder:
□ app.js (updated JavaScript with API integration)
□ styles.css (CSS styling - unchanged from original)

================== STEP-BY-STEP INSTALLATION ==================

STEP 1: XAMPP Setup (2 minutes)
───────────────────────────────
1. Start XAMPP Control Panel
2. Click "Start" next to Apache
3. Click "Start" next to MySQL
4. Wait for both to show "Running" in green

STEP 2: Create Database (3 minutes)
──────────────────────────────────
1. Open browser → http://localhost/phpmyadmin
2. On left sidebar, click "New"
3. Database name: gym_planner
4. Collation: utf8mb4_unicode_ci
5. Click "Create"
6. Click "Import" tab at top
7. Click "Choose File" and select database.sql
8. Click "Import"
9. You should see success message and 5 tables created

STEP 3: Extract Project Files (2 minutes)
────────────────────────────────────────
Windows:
  Extract to: C:\xampp\htdocs\gym-planner\

Mac:
  Extract to: /Applications/XAMPP/htdocs/gym-planner/

Linux:
  Extract to: /opt/lampp/htdocs/gym-planner/

STEP 4: Set File Permissions (Linux/Mac only)
────────────────────────────────────────────
Open Terminal and run:
  chmod 755 /path/to/gym-planner
  chmod 755 /path/to/gym-planner/api
  chmod 755 /path/to/gym-planner/config

STEP 5: Test Installation (1 minute)
────────────────────────────────────
1. Open browser
2. Go to: http://localhost/gym-planner/
3. You should be redirected to login page
4. Login with:
   Username: admin
   Password: admin123
5. You should see the dashboard

FAILED? Go to TROUBLESHOOTING section below.

================== FIRST TIME TESTING ==================

After successful login, test these features:

1. Dashboard (default page)
   □ See 4 stat cards (should show 0 values initially)
   □ See "Quick Actions" buttons
   □ See "Recent Workouts" section (empty)

2. Browse Pre-Made Workouts
   □ Click "Browse Workouts" in nav or dashboard
   □ See 6 pre-made workouts displayed
   □ Filter by difficulty level works
   □ Can click "Start Workout"

3. Start & Complete a Workout
   □ Click "Start Workout" on any workout
   □ See exercise details
   □ Can navigate between exercises
   □ Can check off sets completed
   □ Click "Complete Workout" button
   □ Get success message
   □ Dashboard stats update (total workouts increases)

4. Create Custom Workout
   □ Click "Create" in nav
   □ Enter workout name
   □ Click "+ Add Exercise"
   □ Enter exercise details
   □ Can add multiple exercises
   □ Click "Save Workout"
   □ Workout appears in "Your Saved Workouts"

5. Workout History
   □ Click "History" in nav
   □ See completed workouts listed
   □ Can filter by time period
   □ Shows duration and exercise count

6. Admin Panel
   □ Click "Admin" in nav
   □ See "Manage Workouts" tab (shows pre-made workouts)
   □ See "Create Pre-Made Workout" tab
   □ Can create new pre-made workout
   □ Can edit/delete workouts

7. Logout
   □ Click "Logout" in nav
   □ Redirected to login page
   □ Can login again

================== DATABASE VERIFICATION ==================

To verify database setup in phpMyAdmin:

1. Go to http://localhost/phpmyadmin
2. Click "gym_planner" database on left
3. You should see these tables:
   □ users (1 admin user)
   □ workouts (6 pre-made workouts)
   □ exercises (exercises for each workout)
   □ workout_history (initially empty)
   □ user_stats (initial values)

4. Click "users" table
   - Should see 1 row: admin user
   - Password field contains hashed value (very long)

5. Click "workouts" table
   - Should see 6 rows:
     □ Beginner Full Body
     □ Upper Body Strength
     □ Leg Day Power
     □ HIIT Cardio Blast
     □ Core Strength
     □ Powerlifting Basics

================== API ENDPOINTS TESTING ==================

You can test API endpoints directly in browser:

1. Get Workouts:
   http://localhost/gym-planner/api/get_workouts.php
   (Should return JSON with all workouts)

2. Get Stats:
   http://localhost/gym-planner/api/get_stats.php
   (Should return stats JSON)

3. Get History:
   http://localhost/gym-planner/api/get_history.php?filter=all
   (Should return history JSON, initially empty)

Each should display valid JSON without errors.

================== TROUBLESHOOTING ==================

Problem: "Database connection failed"
──────────────────────────────────────
Symptoms: White page or error on login page

Solutions:
□ Check MySQL is running in XAMPP Control Panel
□ Go to phpMyAdmin (http://localhost/phpmyadmin)
  - If it works, MySQL is running
□ Verify database "gym_planner" exists in phpMyAdmin
□ Check database.sql was imported successfully
□ In config/db.php, verify:
  - DB_HOST = 'localhost'
  - DB_NAME = 'gym_planner'
  - DB_USER = 'root'
  - DB_PASS = '' (empty for default XAMPP)

Problem: "Cannot find module 'config/db.php'"
─────────────────────────────────────────────
Symptoms: API calls fail, 500 errors

Solutions:
□ Verify config/ folder exists
□ Verify config/db.php file exists
□ Check file permissions (chmod 755 on Linux/Mac)
□ Restart Apache in XAMPP

Problem: "Login page shows but won't login"
────────────────────────────────────────────
Symptoms: Correct credentials but stays on login page

Solutions:
□ Check browser console (F12) for JavaScript errors
□ Clear browser cookies
□ Try different browser (Chrome, Firefox)
□ Check phpmyadmin that users table has data:
  - Go to http://localhost/phpmyadmin
  - Click gym_planner → users
  - Should see admin user
□ Try resetting MySQL:
  - Stop MySQL in XAMPP
  - Start MySQL again
  - Refresh browser

Problem: "API endpoints return 404"
────────────────────────────────────
Symptoms: Network errors in browser console when loading workouts

Solutions:
□ Check all files in api/ folder exist
□ Check file paths are correct
□ Verify api/ folder is in project root
□ Try direct URL in browser:
  http://localhost/gym-planner/api/get_workouts.php
  (Should show JSON output)
□ Check Apache error log in XAMPP
□ Restart Apache

Problem: "Workouts not loading after login"
────────────────────────────────────────────
Symptoms: Dashboard shows but no workouts appear

Solutions:
□ Open browser console (F12)
□ Check Network tab:
  - api/get_workouts.php should have 200 response
  - Click it to see JSON response
  - Should show 6 workouts
□ If response shows error:
  - Check database connection
  - Verify workouts table has data
□ Check for JavaScript errors in Console tab
□ Try hard refresh: Ctrl+F5 (Cmd+Shift+R on Mac)

Problem: "Saving workout doesn't work"
───────────────────────────────────────
Symptoms: Click save but nothing happens

Solutions:
□ Check browser console for errors (F12)
□ Check Network tab to see api/save_workout.php response
□ Verify you're logged in (check session)
□ Check form has all required fields filled
□ Check for JavaScript errors
□ Reload page and try again

Problem: "Can't access /admin panel"
─────────────────────────────────────
Symptoms: Admin link doesn't show or clicking does nothing

Solutions:
□ Admin link should be in navigation
□ Click it to show admin section
□ If not visible, check JavaScript loaded (F12 console)
□ Try hard refresh: Ctrl+F5

Problem: "Changes aren't saving to database"
──────────────────────────────────────────────
Symptoms: Data appears to save but doesn't persist after refresh

Solutions:
□ Check api/save_*.php files exist
□ Verify database connection in config/db.php
□ Check MySQL is running
□ Look at database with phpMyAdmin:
  - Refresh table
  - Should see new data
□ If not appearing in database:
  - Check for SQL errors
  - Verify prepared statements work
  - Check user has INSERT permissions

Problem: "Logout doesn't work"
───────────────────────────────
Symptoms: Click logout but stays logged in

Solutions:
□ Check logout.php file exists
□ Clear browser cookies
□ Close all browser windows/tabs for site
□ Reopen and login again
□ Check browser allows sessions/cookies

Problem: "Getting 403 Forbidden error"
───────────────────────────────────────
Symptoms: File access denied errors

Solutions (Linux/Mac):
□ Run: chmod 755 /path/to/gym-planner
□ Run: chmod 755 /path/to/gym-planner/*
□ Run: chmod 755 /path/to/gym-planner/api
□ Restart Apache

Problem: "Getting 500 Internal Server Error"
──────────────────────────────────────────────
Symptoms: Generic 500 error on any page

Solutions:
□ Check Apache error log in XAMPP
□ Check for PHP syntax errors:
  - In admin console run: php -l config/db.php
  - In admin console run: php -l api/get_workouts.php
□ Increase PHP error reporting:
  - Edit php.ini in XAMPP/php folder
  - Add: error_reporting = E_ALL
□ Check database connection:
  - Try direct connection in phpMyAdmin
□ Restart Apache and MySQL

================== COMMON CONFIGURATION CHANGES ==================

Changing MySQL Port:
If port 3306 is in use:
1. In XAMPP Control Panel → MySQL → Config → my.ini
2. Find: port=3306
3. Change to: port=3307
4. In config/db.php, change:
   define('DB_HOST', 'localhost:3307');

Changing Apache Port:
If port 80 is in use:
1. In XAMPP Control Panel → Apache → Config → httpd.conf
2. Find: Listen 80
3. Change to: Listen 8080
4. Access app at: http://localhost:8080/gym-planner/

Changing Default Password:
1. Generate hash at: https://www.php.net/manual/en/function.password-hash.php
2. Or run this in PHP:
   <?php echo password_hash('newpassword', PASSWORD_DEFAULT); ?>
3. Go to phpMyAdmin
4. Click gym_planner → users table
5. Edit admin row
6. Replace password field value
7. Click Save

================== DEVELOPMENT TIPS ==================

Enable PHP Error Display:
1. Find php.ini in XAMPP/php folder
2. Add/modify: display_errors = On
3. Restart Apache

Check Database Queries:
1. Add debug logging to config/db.php
2. Log all query execution
3. Check logs for problems

Test API Endpoints:
1. Use curl in command line:
   curl http://localhost/gym-planner/api/get_workouts.php
2. Or use browser to visit endpoint
3. Should return valid JSON

Monitor Apache Logs:
1. XAMPP/apache/logs/error.log
2. XAMPP/apache/logs/access.log
3. Shows all requests and errors

Monitor MySQL Logs:
1. XAMPP/mysql/data/error.log
2. Shows database errors
3. Useful for debugging SQL issues

================== PERFORMANCE OPTIMIZATION ==================

If application becomes slow:

1. Add database indexes:
   - Already included in database.sql
   - Check they were created in phpMyAdmin

2. Implement API caching:
   - Cache workout list in localStorage
   - Reduce API calls

3. Optimize images:
   - Workouts use emojis (already lightweight)
   - No images to optimize

4. Compress responses:
   - Enable gzip compression in Apache
   - Add to .htaccess

5. Minify CSS/JS:
   - Not required but can be done
   - Would need build process

================== BACKUP & RESTORE ==================

Backup Database:
1. Go to phpMyAdmin
2. Click gym_planner database
3. Click Export tab
4. Format: SQL
5. Click Go
6. Save file to safe location

Backup Files:
1. Copy entire gym-planner folder
2. Save to external drive or cloud storage

Restore Database:
1. Go to phpMyAdmin
2. Click Import tab
3. Choose backup SQL file
4. Click Import

Restore Files:
1. Copy saved gym-planner folder
2. Paste into htdocs
3. Make sure file permissions are correct

================== NEXT STEPS AFTER INSTALLATION ==================

1. ✓ Test basic functionality (see FIRST TIME TESTING)
2. ✓ Change default admin password
3. ✓ Create some custom workouts
4. ✓ Complete a workout and verify history saves
5. ✓ Test admin panel features
6. ✓ Backup database
7. ✓ Explore all features and get familiar
8. ✓ Customize colors/styling if desired
9. ✓ Plan to add more features (optional)

================== SUPPORT RESOURCES ==================

For Help:
□ Check SETUP_GUIDE.md for detailed information
□ Review code comments in PHP files
□ Check browser console (F12) for JavaScript errors
□ Check phpMyAdmin for data verification
□ Check XAMPP logs for server errors

Database Issues:
□ phpMyAdmin: http://localhost/phpmyadmin
□ MySQL Documentation: https://dev.mysql.com/

PHP Issues:
□ PHP Documentation: https://www.php.net/
□ XAMPP Documentation: https://www.apachefriends.org/

JavaScript Issues:
□ Browser DevTools: F12
□ JavaScript Console: Check for errors
□ Network Tab: Check API responses

================== VERSION INFORMATION ==================

Project: Gym Planner Full-Stack
Version: 1.0.0
Created: 2024
Database: MySQL 5.7+
PHP: 7.4+
Frontend: Vanilla JavaScript
Browser Support: All modern browsers

================== SUCCESS CHECKLIST ==================

After installation, verify:

□ XAMPP Apache running
□ XAMPP MySQL running
□ Project files extracted to htdocs
□ Database "gym_planner" created
□ database.sql imported successfully
□ Can access http://localhost/gym-planner/
□ Can login with admin/admin123
□ Dashboard loads with 0 stats
□ Can see 6 pre-made workouts
□ Can start a workout
□ Can complete a workout
□ Stats update after completing workout
□ Can view history
□ Can create custom workout
□ Can access admin panel
□ Can logout

If all items are checked, your installation is complete and working! ✓

═══════════════════════════════════════════════════════════════════════════════

Questions? See SETUP_GUIDE.md for more detailed information.

Ready to track your workouts! 💪
