import { createContext, useContext, useEffect, useState, useCallback } from "react";
import { THEMES, findTheme } from "../theme/presets.js";
import { applyThemeVars, computeThemeVars } from "../theme/colorUtils.js";

const ThemeContext = createContext(null);
const STORAGE_KEY = "kiu_theme";
const MODE_KEY = "kiu_theme_mode";

export function ThemeProvider({ children }) {
  const [themeKey, setThemeKey] = useState(() => localStorage.getItem(STORAGE_KEY) ?? "default");
  const [mode, setMode] = useState(() => localStorage.getItem(MODE_KEY) ?? findTheme(themeKey).mode);

  useEffect(() => {
    const theme = findTheme(themeKey);
    const vars = computeThemeVars({ primary: theme.primary, accent: theme.accent, mode });
    applyThemeVars(vars);
    document.documentElement.classList.toggle("dark", mode === "dark");
    localStorage.setItem(STORAGE_KEY, themeKey);
    localStorage.setItem(MODE_KEY, mode);
  }, [themeKey, mode]);

  const selectTheme = useCallback((key) => {
    setThemeKey(key);
    setMode(findTheme(key).mode); // switching palettes resets to that palette's intended mode
  }, []);

  const toggleMode = useCallback(() => {
    setMode((m) => (m === "dark" ? "light" : "dark"));
  }, []);

  return (
    <ThemeContext.Provider
      value={{ themeKey, mode, themes: THEMES, selectTheme, toggleMode, current: findTheme(themeKey) }}
    >
      {children}
    </ThemeContext.Provider>
  );
}

export function useTheme() {
  const ctx = useContext(ThemeContext);
  if (!ctx) throw new Error("useTheme must be used inside <ThemeProvider>");
  return ctx;
}
