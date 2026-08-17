/** @type {import('tailwindcss').Config} */
export default {
  darkMode: 'class',
  content: [
    "./resources/**/*.blade.php",
    "./resources/**/*.js",
    "./resources/**/*.vue",
    // The React admin app lives here now — without this, Tailwind never
    // scans .jsx files at all, and none of its classes (including every
    // custom color/font token merged in below) would generate any CSS.
    // That's the kind of failure that looks like "nothing is styled" or
    // "the app is broken" with no error message anywhere to explain why.
    "./resources/js/admin/**/*.{js,jsx}",
  ],
  theme: {
    extend: {
      // Merged in from the React app's own tailwind.config.js (it was a
      // fully separate Vite project before, with its own config file
      // that Laravel's config never saw or shared).
      colors: {
        ink: '#1C2320',
        sand: { 50: '#FBF8F1', 100: '#F7F1E4', 200: '#EDE3CC', 300: '#E3D6B8' },
        teal: {
          950: '#081F1C', 900: '#0E3B36', 800: '#114540', 700: '#155248',
          600: '#1F6F63', 500: '#2C8C7C', 100: '#DCEAE6',
        },
        gold: { 700: '#7C5F30', 600: '#9C7A3F', 500: '#B08D57', 400: '#C7A876', 300: '#DCC79A' },
        rose: { 700: '#8A2F2F', 600: '#A23B3B', 100: '#F6E3E1' },
      },
      fontFamily: {
        display: ['"Fraunces"', 'serif'],
        body: ['"Inter"', 'sans-serif'],
      },
      borderRadius: { xl2: '1.25rem', xl3: '1.75rem' },
      boxShadow: {
        soft: '0 1px 2px rgba(28,35,32,0.04), 0 8px 24px -8px rgba(14,59,54,0.10)',
        lift: '0 12px 32px -8px rgba(14,59,54,0.20), 0 2px 8px -2px rgba(14,59,54,0.10)',
        glow: '0 0 0 1px rgba(176,141,87,0.25), 0 8px 30px -6px rgba(176,141,87,0.35)',
      },
      backgroundImage: {
        'khatam': "url(\"data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='90' height='90' viewBox='0 0 90 90'%3E%3Cg fill='none' stroke='%23FFFFFF' stroke-opacity='0.06' stroke-width='1'%3E%3Crect x='15' y='15' width='60' height='60'/%3E%3Crect x='15' y='15' width='60' height='60' transform='rotate(45 45 45)'/%3E%3C/g%3E%3C/svg%3E\")",
        'grad-teal': 'linear-gradient(135deg, #0E3B36 0%, #155248 55%, #1F6F63 100%)',
        'grad-gold': 'linear-gradient(135deg, #B08D57 0%, #C7A876 100%)',
      },
      keyframes: {
        'toast-in': {
          '0%': { opacity: '0', transform: 'translateY(0.5rem) scale(0.98)' },
          '100%': { opacity: '1', transform: 'translateY(0) scale(1)' },
        },
        'fade-in': {
          '0%': { opacity: '0' },
          '100%': { opacity: '1' },
        },
        'fade-up': {
          '0%': { opacity: '0', transform: 'translateY(8px)' },
          '100%': { opacity: '1', transform: 'translateY(0)' },
        },
        shimmer: {
          '0%': { backgroundPosition: '-400px 0' },
          '100%': { backgroundPosition: '400px 0' },
        },
        'spin-slow': {
          '0%': { transform: 'rotate(0deg)' },
          '100%': { transform: 'rotate(360deg)' },
        },
        float: {
          '0%, 100%': { transform: 'translateY(0)' },
          '50%': { transform: 'translateY(-6px)' },
        },
      },
      animation: {
        'toast-in': 'toast-in 0.2s ease-out',
        'fade-in': 'fade-in 0.15s ease-out',
        'fade-up': 'fade-up 0.35s cubic-bezier(0.16,1,0.3,1) both',
        shimmer: 'shimmer 1.6s infinite linear',
        'spin-slow': 'spin-slow 16s linear infinite',
        float: 'float 5s ease-in-out infinite',
      },
      transitionTimingFunction: {
        spring: 'cubic-bezier(0.16, 1, 0.3, 1)',
      },
    },
  },
  plugins: [],
}

