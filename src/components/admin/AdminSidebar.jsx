import { NavLink } from "react-router-dom";
import {
  LayoutDashboard,
  Users,
  UserCog,
  Radio,
  MessageCircle,
  Home,
  BookOpen,
  PenLine,
  Megaphone,
  Mic,
  GraduationCap,
} from "lucide-react";
import { useAuth } from "../../context/AuthContext.jsx";

// Every real nav item declares what it needs to be visible at all:
//   - nothing              -> baseline feature, every logged-in admin/leader sees it
//   - requires: "x.y"      -> only visible if the user has that permission
//   - requiresAny: [...]   -> visible if the user has any one of these
//   - adminOnly: true      -> only visible to role === "ADMIN", no permission can grant it
// This list is filtered below — an item a user can't access is not just
// disabled, it is never rendered at all, per the spec's dynamic-sidebar rule.
const NAV_SECTIONS = [
  {
    label: "Ahabanza",
    items: [
      { to: "/leader/analytics", label: "Dashboard", icon: LayoutDashboard, end: true, requires: "analytics.view" },
      { to: "/leader/abakoresha", label: "Abakoresha", icon: UserCog, adminOnly: true },
      { to: "/leader/abanyeshuri", label: "Abanyeshuri", icon: Users, requires: "student.approve" },
    ],
  },
  {
    label: "Ibikorwa",
    items: [
      { to: "/chat", label: "Kwegereza Chat", icon: MessageCircle }, // baseline — every active account can chat
      { to: "/live-class", label: "Amasomo ya Live", icon: Radio, requires: "classroom.host" },
      { to: "/leader/ifaida", label: "Ifaida", icon: PenLine, requiresAny: ["ifaida.create", "ifaida.update"] },
      { to: "/leader/dars", label: "Amasomo (Dars)", icon: Mic, requiresAny: ["dars.create", "dars.update"] },
      { to: "/leader/ibitabo", label: "Ibitabo", icon: BookOpen, requiresAny: ["book.create", "book.update", "book.view"] },
      { to: "/leader/amatangazo", label: "Amatangazo", icon: Megaphone, requiresAny: ["announcement.create", "announcement.update"] },
      { to: "/leader/abarimu", label: "Abarimu", icon: GraduationCap, requires: "teacher.manage" },
    ],
  },
];

export default function AdminSidebar({ open }) {
  const { user } = useAuth();
  const isAdmin = user?.role === "ADMIN";
  const permissions = user?.permissions ?? [];

  function canSee(item) {
    if (item.adminOnly) return isAdmin;
    if (item.requires) return isAdmin || permissions.includes(item.requires);
    if (item.requiresAny) return isAdmin || item.requiresAny.some((p) => permissions.includes(p));
    return true; // baseline item, no gate
  }

  const linkClass = ({ isActive }) =>
    `flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-[13.5px] font-semibold transition-colors ${
      isActive ? "bg-green-950 text-white" : "text-[#4d6157] hover:bg-cream-2"
    }`;

  return (
    <aside
      className={`fixed lg:static top-0 left-0 h-full w-[260px] bg-surface border-r border-line flex flex-col z-[90] transition-transform duration-200 ${
        open ? "translate-x-0" : "-translate-x-full lg:translate-x-0"
      }`}
    >
      <div className="h-[72px] flex items-center gap-3 px-5 border-b border-line flex-none">
        <div className="w-10 h-10 rounded-full border-2 border-gold-500 bg-green-950 flex items-center justify-center flex-none">
          <svg viewBox="0 0 24 24" fill="none" stroke="#cf9d3f" strokeWidth="1.6" className="w-5 h-5">
            <path d="M4 21V11l8-6 8 6v10" />
            <path d="M9 21v-6h6v6" />
          </svg>
        </div>
        <div className="leading-tight">
          <b className="text-green-950 text-[15px] block">K.I.U</b>
          <small className="text-ink-soft text-[10.5px]">
            {isAdmin ? "Admin Dashboard" : "Leader Dashboard"}
          </small>
        </div>
      </div>

      <nav className="flex-1 overflow-y-auto px-3 py-5">
        {NAV_SECTIONS.map((section) => {
          const visibleItems = section.items.filter(canSee);
          if (visibleItems.length === 0) return null; // never show an empty section header either
          return (
            <div key={section.label} className="mb-6">
              <div className="text-[10.5px] font-extrabold text-ink-soft uppercase tracking-wide px-3 mb-2">
                {section.label}
              </div>
              <div className="space-y-1">
                {visibleItems.map(({ to, label, icon: Icon, end }) => (
                  <NavLink key={to} to={to} end={end} className={linkClass}>
                    <Icon size={17} />
                    {label}
                  </NavLink>
                ))}
              </div>
            </div>
          );
        })}
      </nav>

      <div className="p-3 border-t border-line">
        <NavLink to="/" className="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-[13.5px] font-semibold text-ink-soft hover:bg-cream-2">
          <Home size={17} />
          Subira ku rubuga
        </NavLink>
      </div>
    </aside>
  );
}
