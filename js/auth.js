document.addEventListener('DOMContentLoaded', async function() {
    
    // Check if already logged in
    const isLoggedIn = await checkAuth();
    if (isLoggedIn) {
        window.location.href = 'dashboard.html';
        return;
    }
    
    // Form switching
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
    
    // Login form submission
    const loginFormElement = document.getElementById('loginFormElement');
    loginFormElement.addEventListener('submit', async (e) => {
        e.preventDefault();
        
        const email = document.getElementById('loginEmail').value;
        const password = document.getElementById('loginPassword').value;
        
        setLoading(true, 'login');
        clearError('loginError');
        
        const result = await apiCall(ENDPOINTS.LOGIN, {
            email: email,
            password: password
        });
        
        if (result.success) {
            window.location.href = 'dashboard.html';
        } else {
            showError('loginError', result.message);
            setLoading(false, 'login');
        }
    });
    
    // Signup form submission
    const signupFormElement = document.getElementById('signupFormElement');
    signupFormElement.addEventListener('submit', async (e) => {
        e.preventDefault();
        
        const full_name = document.getElementById('signupName').value;
        const email = document.getElementById('signupEmail').value;
        const password = document.getElementById('signupPassword').value;
        const height = document.getElementById('signupHeight').value;
        const age = document.getElementById('signupAge').value;
        const gender = document.getElementById('signupGender').value;
        
        setLoading(true, 'signup');
        clearError('signupError');
        
        const result = await apiCall(ENDPOINTS.REGISTER, {
            full_name: full_name,
            email: email,
            password: password,
            height: height,
            age: age,
            gender: gender
        });
        
        if (result.success) {
            window.location.href = 'dashboard.html';
        } else {
            showError('signupError', result.message);
            setLoading(false, 'signup');
        }
    });
    
    // Helper functions
    function setLoading(loading, formType) {
        const btn = formType === 'login' 
            ? loginFormElement.querySelector('button')
            : signupFormElement.querySelector('button');
        
        const text = btn.querySelector('.btn-text');
        const loader = btn.querySelector('.btn-loader');
        
        if (loading) {
            text.style.display = 'none';
            loader.style.display = 'inline';
            btn.disabled = true;
        } else {
            text.style.display = 'inline';
            loader.style.display = 'none';
            btn.disabled = false;
        }
    }
    
    function showError(elementId, message) {
        const element = document.getElementById(elementId);
        element.textContent = message;
        element.style.display = 'block';
    }
    
    function clearError(elementId) {
        const element = document.getElementById(elementId);
        element.textContent = '';
        element.style.display = 'none';
    }
    
    function clearErrors() {
        clearError('loginError');
        clearError('signupError');
    }
});