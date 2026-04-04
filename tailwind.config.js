/** @type {import('tailwindcss').Config} */
export default {
  content: [
    "./index.php",
    "./modals/**/*.php",
    "./pages/**/*.php",
    "./public/**/*.js",
  ],
  darkMode: 'class',
  theme: {
    extend: {
      colors: {
        brand:   { DEFAULT: '#ff5d2e', dim: '#cc3d12' },
        amber:   { DEFAULT: '#fd8b00' },
        surface: { DEFAULT: '#0a0a0a', card: '#0e0e0e', border: '#282828' },
        ink:     { DEFAULT: '#ffffff', muted: '#888888', faint: '#444444' },
      },
      fontFamily: {
        headline: ['Epilogue', 'sans-serif'],
        body:     ['DM Sans', 'sans-serif'],
      },
      keyframes: {
        fadeUp:     { '0%': { opacity: 0, transform: 'translateY(18px)' }, '100%': { opacity: 1, transform: 'translateY(0)' } },
        slideLeft:  { '0%': { opacity: 0, transform: 'translateX(24px)' },  '100%': { opacity: 1, transform: 'translateX(0)' } },
        slideRight: { '0%': { opacity: 0, transform: 'translateX(-24px)' }, '100%': { opacity: 1, transform: 'translateX(0)' } },
        rippleAnim: { 'to': { transform: 'scale(4)', opacity: '0' } },
        spin:       { 'to': { transform: 'rotate(360deg)' } },
      },
      animation: {
        'fade-up':    'fadeUp 0.5s ease both',
        'slide-left': 'slideLeft 0.35s cubic-bezier(0.4,0,0.2,1) both',
        'slide-right':'slideRight 0.35s cubic-bezier(0.4,0,0.2,1) both',
        'ripple':     'rippleAnim 0.5s linear forwards',
        'spin-fast':  'spin 0.7s linear infinite',
      },
    },
  },
  plugins: [
    require('@tailwindcss/forms'),
  ],
}
