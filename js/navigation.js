// Navigation module - handles page switching
const Navigation = {
    
    init() {
        this.setupNavLinks();
        this.setupActionButtons();
    },
    
    setupNavLinks() {
        document.querySelectorAll('.nav-link').forEach(link => {
            link.addEventListener('click', (e) => {
                e.preventDefault();
                const page = link.getAttribute('data-page');
                this.navigateTo(page);
            });
        });
    },
    
    setupActionButtons() {
        document.querySelectorAll('.action-btn').forEach(button => {
            button.addEventListener('click', () => {
                const page = button.getAttribute('data-page');
                this.navigateTo(page);
            });
        });
    },
    
    navigateTo(page) {
        // Hide all sections
        document.querySelectorAll('.page-section').forEach(section => {
            section.classList.remove('active');
        });
        
        // Remove active from all nav links
        document.querySelectorAll('.nav-link').forEach(link => {
            link.classList.remove('active');
        });
        
        // Show target section
        const targetSection = document.getElementById(page);
        if (targetSection) {
            targetSection.classList.add('active');
        }
        
        // Set active nav link
        const activeLink = document.querySelector(`.nav-link[data-page="${page}"]`);
        if (activeLink) {
            activeLink.classList.add('active');
        }
        
        // Load page content
        this.loadPageContent(page);
    },
    
    loadPageContent(page) {
        switch(page) {
            case 'dashboard':
                Dashboard.load();
                break;
            case 'workouts':
                Workouts.loadPreMade();
                break;
            case 'custom':
                CustomWorkouts.load();
                break;
            case 'history':
                History.load();
                break;
            case 'progress':
                Progress.load();
                break;
        }
    }
};