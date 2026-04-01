<!-- Auth Modal -->
<div id="authModal" class="modal hidden">
    <div class="modal-content auth-form">
        <div class="form-toggle">
            <button class="toggle-btn active" data-form="login">Login</button>
            <button class="toggle-btn" data-form="register">Register</button>
        </div>
        
        <!-- Login Form -->
        <form id="loginForm" class="form-section active">
            <h2>Welcome Back</h2>
            <input type="email" placeholder="Email" required>
            <input type="password" placeholder="Password" required>
            <button type="submit" class="btn btn-primary">Login</button>
            <p class="form-message"></p>
        </form>
        
        <!-- Register Form -->
        <form id="registerForm" class="form-section">
            <h2>Create Account</h2>
            <input type="text" placeholder="Full Name" required>
            <input type="email" placeholder="Email" required>
            <input type="password" placeholder="Password" required>
            <button type="submit" class="btn btn-primary">Register</button>
            <p class="form-message"></p>
        </form>
    </div>
</div>