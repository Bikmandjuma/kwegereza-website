import { useState } from "react";
import AdminSidebar from "../components/admin/AdminSidebar.jsx";
import AdminHeader from "../components/admin/AdminHeader.jsx";

export default function AdminLayout({ title, breadcrumb, children }) {
  const [sidebarOpen, setSidebarOpen] = useState(false);

  return (
    <div className="h-screen bg-cream flex overflow-hidden">
      <AdminSidebar open={sidebarOpen} />

      {sidebarOpen && (
        <button
          className="fixed inset-0 bg-black/30 z-[85] lg:hidden"
          onClick={() => setSidebarOpen(false)}
          aria-label="Funga menu"
        />
      )}

      {/* Only this column scrolls — the sidebar keeps its own independent
          scroll region (see AdminSidebar's <nav>) so neither one drags the
          whole page/viewport with it. */}
      <div className="flex-1 min-w-0 h-screen overflow-y-auto">
        <AdminHeader
          breadcrumb={breadcrumb}
          title={title}
          onToggleSidebar={() => setSidebarOpen((o) => !o)}
        />
        <main className="p-5 lg:p-8">{children}</main>
      </div>
    </div>
  );
}
