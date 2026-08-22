import { useState } from "react";
import { useNavigate } from "react-router-dom";
import { Menu, Search, ChevronDown, LogOut, User as UserIcon } from "lucide-react";
import { useAuth } from "../../context/AuthContext.jsx";
import NotificationBell from "../NotificationBell.jsx";
import ThemeSwitcher from "../ThemeSwitcher.jsx";

export default function AdminHeader({ breadcrumb, title, onToggleSidebar }) {
  const { user, logout } = useAuth();
  const navigate = useNavigate();
  const [open, setOpen] = useState(false);

  async function handleLogout() {
    await logout();
    setOpen(false);
    navigate("/");
  }

  return (
    <header className="sticky top-0 z-[80] bg-surface border-b border-line">
      <div className="h-[72px] px-5 lg:px-8 flex items-center justify-between gap-4">
        <div className="flex items-center gap-3">
          <button
            onClick={onToggleSidebar}
            className="lg:hidden w-9 h-9 rounded-lg bg-cream-2 flex items-center justify-center flex-none"
          >
            <Menu size={18} />
          </button>
          <div>
            <div className="text-[11.5px] text-ink-soft">{breadcrumb}</div>
            <h1 className="font-display text-xl font-bold text-green-950 leading-tight">{title}</h1>
          </div>
        </div>

        <div className="flex items-center gap-3">
          <div className="hidden md:flex items-center gap-2 bg-cream-2 rounded-full px-4 py-2 text-sm text-ink-soft w-[220px]">
            <Search size={14} />
            <input placeholder="Shakisha..." className="bg-transparent outline-none w-full text-sm" />
          </div>

          <ThemeSwitcher variant="onLight" />
          <NotificationBell />

          <div className="relative">
            <button
              onClick={() => setOpen((o) => !o)}
              className="flex items-center gap-2 pl-1 pr-2.5 py-1 rounded-full hover:bg-cream-2"
            >
              <div className="w-8 h-8 rounded-full bg-gradient-to-br from-green-700 to-green-950 text-white flex items-center justify-center font-display font-bold text-xs flex-none">
                {user?.fullName?.[0]}
              </div>
              <span className="hidden sm:block text-sm font-semibold text-green-950">
                {user?.fullName?.split(" ")[0]}
              </span>
              <ChevronDown size={14} className="text-ink-soft" />
            </button>

            {open && (
              <div className="absolute right-0 top-11 w-52 bg-surface rounded-2xl shadow-xl border border-line overflow-hidden z-[100]">
                <div className="px-4 py-3 border-b border-line">
                  <div className="text-sm font-semibold text-green-950">{user?.fullName}</div>
                  <div className="text-xs text-ink-soft truncate">{user?.email}</div>
                </div>
                <div className="px-4 py-2 text-[11px] font-bold text-ink-soft uppercase flex items-center gap-1.5">
                  <UserIcon size={12} /> {user?.role}
                </div>
                <button
                  onClick={handleLogout}
                  className="w-full text-left px-4 py-2.5 text-sm font-semibold flex items-center gap-2 hover:bg-cream-2 border-t border-line"
                >
                  <LogOut size={14} /> Sohoka
                </button>
              </div>
            )}
          </div>
        </div>
      </div>
    </header>
  );
}
