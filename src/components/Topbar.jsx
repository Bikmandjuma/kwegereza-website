import { useState } from "react";
import { Link, useNavigate } from "react-router-dom";
import { Phone, Mail, ChevronDown, LogOut, ShieldCheck, BarChart3 } from "lucide-react";
import { useAuth } from "../context/AuthContext.jsx";
import NotificationBell from "./NotificationBell.jsx";
import ThemeSwitcher from "./ThemeSwitcher.jsx";

export default function Topbar() {
  const { user, logout } = useAuth();
  const navigate = useNavigate();
  const [open, setOpen] = useState(false);

  async function handleLogout() {
    await logout();
    setOpen(false);
    navigate("/");
  }

  return (
    <div className="bg-green-950 text-[#dce9e2] text-[13px]">
      <div className="max-w-[1240px] mx-auto px-7 h-10 flex items-center justify-between">
        <div className="hidden sm:flex gap-5 items-center">
          <span className="flex items-center gap-1.5 opacity-90">
            <Phone size={14} /> (+250) 723 061 482
          </span>
          <span className="flex items-center gap-1.5 opacity-90">
            <Mail size={14} /> umuryangok@gmail.com
          </span>
          <ThemeSwitcher />
        </div>

        <div className="sm:hidden">
          <ThemeSwitcher />
        </div>

        <div className="flex gap-2.5 items-center ml-auto sm:ml-0 relative">
          {user ? (
            <>
              <NotificationBell />
              {(user.role === "ADMIN" || user.role === "LEADER") && (
                <Link to="/leader/abanyeshuri" className="pill">
                  <ShieldCheck size={13} /> Approval Center
                </Link>
              )}
              {(user.role === "ADMIN" || user.role === "LEADER") && (
                <Link to="/leader/analytics" className="pill">
                  <BarChart3 size={13} /> Analytics
                </Link>
              )}
              <button className="pill" onClick={() => setOpen((o) => !o)}>
                {user.fullName.split(" ")[0]} <ChevronDown size={12} />
              </button>
              {open && (
                <div className="absolute right-0 top-9 bg-surface text-ink rounded-xl shadow-lg border border-line py-2 w-44 z-[200]">
                  <div className="px-4 py-2 text-xs text-ink-soft border-b border-line">
                    {user.email}
                  </div>
                  <button
                    onClick={handleLogout}
                    className="w-full text-left px-4 py-2.5 text-sm font-semibold flex items-center gap-2 hover:bg-cream-2"
                  >
                    <LogOut size={14} /> Sohoka
                  </button>
                </div>
              )}
            </>
          ) : (
            <>
              <Link to="/login" className="pill">
                Account <ChevronDown size={12} />
              </Link>
              <Link to="/register" className="pill pill-gold">
                Twandikire
              </Link>
            </>
          )}
        </div>
      </div>
    </div>
  );
}
