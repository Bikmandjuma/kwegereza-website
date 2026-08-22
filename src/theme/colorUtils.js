// Small, dependency-free color math used to derive a full set of CSS-variable
// shades from just two base colors (primary + accent) plus a light/dark
// mode flag. This is what lets 15+ themes exist without hand-authoring a
// full color ramp for every single one — every theme is just two hex colors
// and a mode, and the rest is computed.

function hexToRgb(hex) {
  const clean = hex.replace("#", "");
  const full = clean.length === 3 ? clean.split("").map((c) => c + c).join("") : clean;
  const num = parseInt(full, 16);
  return { r: (num >> 16) & 255, g: (num >> 8) & 255, b: num & 255 };
}

function clamp(n) {
  return Math.max(0, Math.min(255, Math.round(n)));
}

/** Mix a color toward white (amount 0..1) */
function lighten({ r, g, b }, amount) {
  return { r: clamp(r + (255 - r) * amount), g: clamp(g + (255 - g) * amount), b: clamp(b + (255 - b) * amount) };
}

/** Mix a color toward black (amount 0..1) */
function darken({ r, g, b }, amount) {
  return { r: clamp(r * (1 - amount)), g: clamp(g * (1 - amount)), b: clamp(b * (1 - amount)) };
}

function rgbTriplet({ r, g, b }) {
  return `${r} ${g} ${b}`;
}

/**
 * Computes every CSS variable the app's Tailwind config expects, from just
 * a primary hex, an accent hex, and a light/dark mode flag.
 */
export function computeThemeVars({ primary, accent, mode }) {
  const p = hexToRgb(primary);
  const a = hexToRgb(accent);
  const isDark = mode === "dark";

  const vars = {
    // brand scale (buttons, hero, headers, sidebar-active, footer)
    "--c-primary-950": rgbTriplet(darken(p, isDark ? 0.15 : 0.55)),
    "--c-primary-900": rgbTriplet(darken(p, isDark ? 0.0 : 0.35)),
    "--c-primary-800": rgbTriplet(darken(p, isDark ? -0.12 : 0.15)),
    "--c-primary-700": rgbTriplet(isDark ? lighten(p, 0.12) : p),

    // accent scale (gold/highlight color, CTAs, badges)
    "--c-accent-100": rgbTriplet(lighten(a, isDark ? 0.55 : 0.82)),
    "--c-accent-400": rgbTriplet(lighten(a, 0.15)),
    "--c-accent-500": rgbTriplet(a),
    "--c-accent-600": rgbTriplet(darken(a, 0.15)),

    // page background + secondary background (chips, alt rows)
    "--c-bg": isDark ? rgbTriplet(darken(p, 0.92)) : rgbTriplet(lighten(p, 0.965)),
    "--c-bg-2": isDark ? rgbTriplet(darken(p, 0.84)) : rgbTriplet(lighten(p, 0.93)),

    // card/nav/dropdown surfaces
    "--c-surface": isDark ? rgbTriplet(darken(p, 0.88)) : "255 255 255",
    "--c-surface-2": isDark ? rgbTriplet(darken(p, 0.8)) : rgbTriplet(lighten(p, 0.95)),

    // borders
    "--c-line": isDark ? rgbTriplet(darken(p, 0.7)) : rgbTriplet(lighten(p, 0.83)),

    // text
    "--c-ink": isDark ? "237 241 238" : rgbTriplet(darken(p, 0.62)),
    "--c-ink-soft": isDark ? "168 181 175" : rgbTriplet(darken(lighten(p, 0.1), 0.28)),
  };

  return vars;
}

export function applyThemeVars(vars) {
  const root = document.documentElement;
  Object.entries(vars).forEach(([key, value]) => root.style.setProperty(key, value));
}
