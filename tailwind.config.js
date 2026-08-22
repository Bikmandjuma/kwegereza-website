/** @type {import('tailwindcss').Config} */
export default {
  content: ["./index.html", "./src/**/*.{js,jsx}"],
  darkMode: ["class"],
  theme: {
    extend: {
      colors: {
        green: {
          950: "rgb(var(--c-primary-950) / <alpha-value>)",
          900: "rgb(var(--c-primary-900) / <alpha-value>)",
          800: "rgb(var(--c-primary-800) / <alpha-value>)",
          700: "rgb(var(--c-primary-700) / <alpha-value>)",
        },
        gold: {
          100: "rgb(var(--c-accent-100) / <alpha-value>)",
          400: "rgb(var(--c-accent-400) / <alpha-value>)",
          500: "rgb(var(--c-accent-500) / <alpha-value>)",
          600: "rgb(var(--c-accent-600) / <alpha-value>)",
        },
        cream: {
          DEFAULT: "rgb(var(--c-bg) / <alpha-value>)",
          2: "rgb(var(--c-bg-2) / <alpha-value>)",
        },
        ink: {
          DEFAULT: "rgb(var(--c-ink) / <alpha-value>)",
          soft: "rgb(var(--c-ink-soft) / <alpha-value>)",
        },
        surface: {
          DEFAULT: "rgb(var(--c-surface) / <alpha-value>)",
          2: "rgb(var(--c-surface-2) / <alpha-value>)",
        },
        line: "rgb(var(--c-line) / <alpha-value>)",
      },
      fontFamily: {
        serif: ["'Cormorant Garamond'", "serif"],
        arabic: ["'Amiri'", "serif"],
        sans: ["'Manrope'", "system-ui", "sans-serif"],
      },
      borderRadius: {
        xl2: "22px",
      },
    },
  },
  plugins: [],
};
