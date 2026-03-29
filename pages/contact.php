<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact | Gym Workout Planner</title>
    <link rel="stylesheet" href="../styles/main.css">
</head>
<body>
    <nav class="navbar">
        <div class="nav-container">
            <div class="logo">💪 GymPlanner</div>
            <ul class="nav-menu">
                <li><a href="../index.php" class="nav-link">Home</a></li>
                <li><a href="about.php" class="nav-link">About</a></li>
                <li><a href="contact.php" class="nav-link active">Contact</a></li>
            </ul>
        </div>
    </nav>
    <div class="container">
        <section class="page-section active">
            <h1>Contact Us</h1>
            <p class="subtitle">We'd love to hear from you!</p>
            <form class="contact-form">
                <div class="form-group">
                    <label for="name">Name</label>
                    <input type="text" id="name" name="name" required>
                </div>
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" required>
                </div>
                <div class="form-group">
                    <label for="message">Message</label>
                    <textarea id="message" name="message" rows="5" required></textarea>
                </div>
                <button type="submit" class="btn-primary">Send Message</button>
            </form>
        </section>
    </div>
</body>
</html>
