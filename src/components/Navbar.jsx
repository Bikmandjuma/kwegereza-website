import { useEffect, useState } from "react";
import { NavLink } from "react-router-dom";
import {
  Home,
  Info,
  Users,
  PenLine,
  Megaphone,
  BookOpen,
  Mic,
  Search,
  Menu,
  Radio,
} from "lucide-react";
import { useAuth } from "../context/AuthContext.jsx";
import { useNotifications } from "../context/NotificationContext.jsx";
import SearchModal from "./SearchModal.jsx";

const links = [
  { to: "/", label: "Home", icon: Home, end: true },
  { to: "/about", label: "About Us", icon: Info },
  { to: "/abarimu", label: "Teachers", icon: Users },
  { to: "/inyandiko", label: "Inyandiko", icon: PenLine },
  { to: "/amatangazo", label: "Amatangazo", icon: Megaphone },
  { to: "/ibitabo", label: "Ibitabo", icon: BookOpen },
];

export default function Navbar() {
  const [open, setOpen] = useState(false);
  const [searchOpen, setSearchOpen] = useState(false);
  const [micBlink, setMicBlink] = useState(false);
  const { user } = useAuth();
  const { banner } = useNotifications();

  const navLinks = user ? [...links, { to: "/live-class", label: "Live Class", icon: Radio }] : links;

  // Blinks the mic for exactly 1 second when a live class starts — per spec.
  // Note: this only fires for signed-in users, since realtime notifications
  // require an authenticated socket connection (see NotificationContext).
  // A guest browsing logged-out won't see this without a separate public,
  // unauthenticated realtime channel, which doesn't exist yet.
  useEffect(() => {
    if (banner?.type !== "liveclass.started") return undefined;
    setMicBlink(true);
    const t = setTimeout(() => setMicBlink(false), 1000);
    return () => clearTimeout(t);
  }, [banner]);

  const linkClass = ({ isActive }) =>
    `flex items-center gap-1.5 px-3.5 py-2 rounded-[10px] font-semibold text-[14.5px] border-b-2 transition-colors ${
      isActive
        ? "text-green-950 border-gold-500"
        : "text-[#33463c] border-transparent hover:text-green-900 hover:bg-cream-2"
    }`;

  return (
    <div className="bg-surface border-b border-line sticky top-0 z-50">
      <div className="max-w-[1240px] mx-auto px-7 h-[84px] flex items-center justify-between gap-6">
        <NavLink to="/" className="flex items-center gap-3">
          <div className="w-[46px] h-[46px] rounded-full border-2 border-gold-500 bg-green-950 flex items-center justify-center flex-none">
            <svg viewBox="0 0 24 24" fill="none" stroke="#cf9d3f" strokeWidth="1.6" className="w-6 h-6">
              <path d="M4 21V11l8-6 8 6v10" />
              <path d="M9 21v-6h6v6" />
              <circle cx="12" cy="6" r="1.4" fill="#cf9d3f" stroke="none" />
            </svg>
          </div>
          <div className="leading-tight">
            <b className="text-[19px] tracking-wide text-green-950 block">K.I.U</b>
            <small className="block text-[11.5px] text-ink-soft tracking-wide">
              Kwegereza Islam Umuryango
            </small>
          </div>
        </NavLink>

        <nav className="hidden lg:flex gap-1.5 items-center">
          {navLinks.map(({ to, label, icon: Icon, end }) => (
            <NavLink key={to} to={to} end={end} className={linkClass}>
              <Icon size={16} className="opacity-80" />
              {label}
            </NavLink>
          ))}
        </nav>

        <div className="hidden lg:flex items-center gap-3">
          <div
            title="Alerts y'amasomo ya live"
            className={`w-[34px] h-[34px] rounded-full flex items-center justify-center transition-colors ${
              micBlink ? "bg-gold-500 text-[#1a1206] animate-pulse" : "bg-[#e9f3ee] text-green-800"
            }`}
          >
            <Mic size={16} />
          </div>
          <button
            onClick={() => setSearchOpen(true)}
            title="Shakisha"
            className="w-[34px] h-[34px] rounded-full bg-cream-2 border border-line text-ink-soft flex items-center justify-center hover:bg-green-950 hover:text-white transition-colors"
          >
            <Search size={15} />
          </button>
        </div>

        <button
          className="lg:hidden w-10 h-10 rounded-[10px] bg-cream-2 flex items-center justify-center"
          onClick={() => setOpen((o) => !o)}
        >
          <Menu size={20} className="text-green-950" />
        </button>
      </div>

      {open && (
        <div className="lg:hidden flex flex-col px-7 pb-4 gap-1 border-t border-line">
          {navLinks.map(({ to, label, icon: Icon, end }) => (
            <NavLink
              key={to}
              to={to}
              end={end}
              onClick={() => setOpen(false)}
              className={linkClass}
            >
              <Icon size={16} className="opacity-80" />
              {label}
            </NavLink>
          ))}
          <button
            onClick={() => {
              setOpen(false);
              setSearchOpen(true);
            }}
            className="flex items-center gap-1.5 px-3.5 py-2 rounded-[10px] font-semibold text-[14.5px] text-[#33463c]"
          >
            <Search size={16} className="opacity-80" /> Shakisha
          </button>
        </div>
      )}

      {searchOpen && <SearchModal onClose={() => setSearchOpen(false)} />}
    </div>
  );
}
