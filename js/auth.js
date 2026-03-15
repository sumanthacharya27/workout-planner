// ========================================
// AUTHENTICATION LOGIC
// ========================================

document.addEventListener('DOMContentLoaded', function() {
    
    // ========================================
    // FORM SWITCHING
    // ========================================
    
    const loginForm = document.getElementById('loginForm');
    const signupForm = document.getElementById('signupForm');
    const showSignupBtn = document.getElementById('showSignup');
    const showLoginBtn = document.getElementById('showLogin');
    
    showSignupBtn.addEventListener('click', (e) => {
        e.preventDefault();
        loginForm.classList.remove('active');
        signupForm.classList.add('active');
        clearErrors();
    });
    
    showLoginBtn.addEventListener('click', (e) => {
        e.preventDefault();
        signupForm.classList.remove('active');
        loginForm.classList.add('active');
        clearErrors();
    });
    
    // ========================================
    // LOGIN FUNCTIONALITY
    // ========================================
    
    const loginFormElement = document.getElementById('loginFormElement');
    const loginBtn = document.getElementById('loginBtn');
    const loginError = document.getElementById('loginError');
    
    loginFormElement.addEventListener('submit', async (e) => {
        e.preventDefault();
        
        const email = document.getElementById('loginEmail').value.trim();
        const password = document.getElementById('loginPassword').value;
        
        // Show loading state
        setButtonLoading(loginBtn, true);
        clearError(loginError);
        
        try {
            // Sign in with Firebase
            const userCredential = await auth.signInWithEmailAndPassword(email, password);
            console.log('✅ Login successful!', userCredential.user);
            
            // Redirect to main app
            window.location.href = 'index.html';
            
        } catch (error) {
            console.error('❌ Login error:', error);
            showError(loginError, getErrorMessage(error.code));
            setButtonLoading(loginBtn, false);
        }
    });
    
    // ========================================
    // SIGNUP FUNCTIONALITY
    // ========================================
    
    const signupFormElement = document.getElementById('signupFormElement');
    const signupBtn = document.getElementById('signupBtn');
    const signupError = document.getElementById('signupError');
    
    signupFormElement.addEventListener('submit', async (e) => {
        e.preventDefault();
        
        const name = document.getElementById('signupName').value.trim();
        const email = document.getElementById('signupEmail').value.trim();
        const password = document.getElementById('signupPassword').value;
        const passwordConfirm = document.getElementById('signupPasswordConfirm').value;
        const height = parseInt(document.getElementById('signupHeight').value);
        const age = parseInt(document.getElementById('signupAge').value);
        const gender = document.getElementById('signupGender').value;
        
        // Validation
        if (password !== passwordConfirm) {
            showError(signupError, 'Passwords do not match!');
            return;
        }
        
        if (password.length < 6) {
            showError(signupError, 'Password must be at least 6 characters!');
            return;
        }
        
        if (!gender) {
            showError(signupError, 'Please select a gender!');
            return;
        }
        
        // Show loading state
        setButtonLoading(signupBtn, true);
        clearError(signupError);
        
        try {
            // Create user account
            const userCredential = await auth.createUserWithEmailAndPassword(email, password);
            const user = userCredential.user;
            
            console.log('✅ User created!', user);
            
            // Create user profile in Firestore
            await db.collection('users').doc(user.uid).set({
                profile: {
                    email: email,
                    fullName: name,
                    height: height,
                    age: age,
                    gender: gender,
                    dateJoined: firebase.firestore.FieldValue.serverTimestamp(),
                    isActive: true
                },
                stats: {
                    totalWorkouts: 0,
                    totalExercises: 0,
                    totalTime: 0,
                    currentStreak: 0
                }
            });
            
            console.log('✅ User profile created!');
            
            // Redirect to main app
            window.location.href = 'index.html';
            
        } catch (error) {
            console.error('❌ Signup error:', error);
            showError(signupError, getErrorMessage(error.code));
            setButtonLoading(signupBtn, false);
        }
    });
    
    // ========================================
    // HELPER FUNCTIONS
    // ========================================
    
    function setButtonLoading(button, loading) {
        const text = button.querySelector('.btn-text');
        const loader = button.querySelector('.btn-loader');
        
        if (loading) {
            text.style.display = 'none';
            loader.style.display = 'inline';
            button.disabled = true;
        } else {
            text.style.display = 'inline';
            loader.style.display = 'none';
            button.disabled = false;
        }
    }
    
    function showError(element, message) {
        element.textContent = message;
        element.classList.add('show');
    }
    
    function clearError(element) {
        element.textContent = '';
        element.classList.remove('show');
    }
    
    function clearErrors() {
        clearError(loginError);
        clearError(signupError);
    }
    
    function getErrorMessage(errorCode) {
        const errorMessages = {
            'auth/email-already-in-use': 'This email is already registered. Please login instead.',
            'auth/invalid-email': 'Invalid email address.',
            'auth/operation-not-allowed': 'Email/password sign-in is not enabled.',
            'auth/weak-password': 'Password is too weak. Please use a stronger password.',
            'auth/user-disabled': 'This account has been disabled.',
            'auth/user-not-found': 'No account found with this email.',
            'auth/wrong-password': 'Incorrect password. Please try again.',
            'auth/too-many-requests': 'Too many failed attempts. Please try again later.',
            'auth/network-request-failed': 'Network error. Please check your connection.'
        };
        
        return errorMessages[errorCode] || 'An error occurred. Please try again.';
    }
    
    // ========================================
    // CHECK IF ALREADY LOGGED IN
    // ========================================
    
    auth.onAuthStateChanged((user) => {
        if (user) {
            // User is already logged in, redirect to main app
            console.log('✅ User already logged in:', user.email);
            window.location.href = 'index.html';
        }
    });
});