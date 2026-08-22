import { useState } from "react";
import { Settings, Sun, Moon, Check } from "lucide-react";
import { useTheme } from "../context/ThemeContext.jsx";

export default function ThemeSwitcher({ variant = "onDark" }) {
  const { themeKey, mode, themes, selectTheme, toggleMode } = useTheme();
  const [open, setOpen] = useState(false);

  const triggerClass =
    variant === "onDark"
      ? "text-[#dce9e2] hover:text-gold-400"
      : "text-ink-soft hover:text-green-950";

  return (
    <div className="relative">
      <button
        onClick={() => setOpen((o) => !o)}
        title="Igenamiterere ry'amabara"
        className={`w-7 h-7 rounded-full flex items-center justify-center transition-colors ${triggerClass}`}
      >
        <Settings size={15} />
      </button>

      {open && (
        <>
          <button
            className="fixed inset-0 z-[95] cursor-default"
            onClick={() => setOpen(false)}
            aria-label="Funga"
          />
          <div className="absolute right-0 top-9 w-[300px] bg-surface text-ink rounded-2xl shadow-2xl border border-line z-[100] overflow-hidden">
            <div className="px-4 py-3 border-b border-line flex items-center justify-between">
              <span className="font-bold text-sm">Igenamiterere</span>
              <div className="flex items-center gap-1 bg-cream-2 rounded-full p-1">
                <button
                  onClick={() => mode !== "light" && toggleMode()}
                  className={`w-7 h-7 rounded-full flex items-center justify-center transition-colors ${
                    mode === "light" ? "bg-green-950 text-white" : "text-ink-soft"
                  }`}
                  title="Urumuri (Light)"
                >
                  <Sun size={13} />
                </button>
                <button
                  onClick={() => mode !== "dark" && toggleMode()}
                  className={`w-7 h-7 rounded-full flex items-center justify-center transition-colors ${
                    mode === "dark" ? "bg-green-950 text-white" : "text-ink-soft"
                  }`}
                  title="Umwijima (Dark)"
                >
                  <Moon size={13} />
                </button>
              </div>
            </div>

            <div className="p-3 max-h-[340px] overflow-y-auto">
              <div className="text-[10.5px] font-extrabold text-ink-soft uppercase tracking-wide px-1.5 mb-2">
                Amabara ({themes.length})
              </div>
              <div className="grid grid-cols-4 gap-2.5">
                {themes.map((t) => (
                  <button
                    key={t.key}
                    onClick={() => selectTheme(t.key)}
                    title={t.label}
                    className="flex flex-col items-center gap-1.5 group"
                  >
                    <span
                      className={`relative w-9 h-9 rounded-full overflow-hidden border-2 transition-transform group-hover:scale-110 ${
                        themeKey === t.key ? "border-gold-500" : "border-line"
                      }`}
                    >
                      <span className="absolute inset-0" style={{ background: t.primary }} />
                      <span
                        className="absolute inset-0"
                        style={{
                          background: t.accent,
                          clipPath: "polygon(100% 0, 100% 100%, 0 100%)",
                        }}
                      />
                      {themeKey === t.key && (
                        <span className="absolute inset-0 flex items-center justify-center bg-black/20">
                          <Check size={14} className="text-white drop-shadow" />
                        </span>
                      )}
                    </span>
                  </button>
                ))}
              </div>
            </div>
          </div>
        </>
      )}
    </div>
  );
}
