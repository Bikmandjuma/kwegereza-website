// Every theme is just { key, label, mode, primary, accent } — the rest of
// the color ramp (borders, surfaces, text) is computed by colorUtils.js.
// This is what makes it easy to add more themes later: two hex values and
// a mode, nothing else to hand-tune.

export const THEMES = [
  { key: "default", label: "Kwegereza (Icyatsi & Zahabu)", mode: "light", primary: "#0b3d2e", accent: "#cf9d3f" },
  { key: "green-white", label: "Icyatsi & Umweru", mode: "light", primary: "#0b3d2e", accent: "#146349" },
  { key: "black-white", label: "Umukara & Umweru", mode: "light", primary: "#111111", accent: "#3a3a3a" },
  { key: "white-yellow", label: "Umweru & Umuhondo", mode: "light", primary: "#7a5c00", accent: "#e0b400" },
  { key: "white-red", label: "Umweru & Umutuku", mode: "light", primary: "#7a1220", accent: "#dc3545" },
  { key: "white-violet", label: "Umweru & Violet", mode: "light", primary: "#3d2465", accent: "#8b5cf6" },
  { key: "white-purple", label: "Umweru & Purple", mode: "light", primary: "#4a1d6e", accent: "#a855f7" },
  { key: "purple-white", label: "Purple & Umweru", mode: "dark", primary: "#3b0764", accent: "#c084fc" },
  { key: "blue-yellow", label: "Ubururu & Umuhondo", mode: "light", primary: "#1e3a8a", accent: "#facc15" },
  { key: "navy-gold", label: "Navy & Zahabu", mode: "light", primary: "#0f1f3d", accent: "#d4a017" },
  { key: "teal-white", label: "Teal & Umweru", mode: "light", primary: "#0f4c4c", accent: "#14b8a6" },
  { key: "maroon-cream", label: "Maroon & Cream", mode: "light", primary: "#5c1a1a", accent: "#e0b657" },
  { key: "forest-orange", label: "Ishyamba & Orange", mode: "light", primary: "#14331f", accent: "#f97316" },
  { key: "slate-blue", label: "Slate & Ubururu", mode: "light", primary: "#1e293b", accent: "#3b82f6" },
  { key: "rose-charcoal", label: "Rose & Charcoal", mode: "light", primary: "#3a1220", accent: "#f43f5e" },
  { key: "black-blue", label: "Umukara wuzuye + Buto z'Ubururu", mode: "dark", primary: "#0a0a0a", accent: "#3b82f6" },
  { key: "emerald-dark", label: "Emerald (Dark Mode)", mode: "dark", primary: "#064e3b", accent: "#34d399" },
  { key: "midnight-gold", label: "Midnight & Zahabu", mode: "dark", primary: "#0b1220", accent: "#e0b657" },
];

export function findTheme(key) {
  return THEMES.find((t) => t.key === key) ?? THEMES[0];
}
