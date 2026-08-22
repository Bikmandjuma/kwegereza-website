import { useEffect } from "react";
import { Outlet, useLocation } from "react-router-dom";
import Topbar from "../components/Topbar.jsx";
import Navbar from "../components/Navbar.jsx";
import Footer from "../components/Footer.jsx";
import ChatFab from "../components/ChatFab.jsx";
import GoogleRegisterModal from "../components/GoogleRegisterModal.jsx";
import { recordVisit } from "../api/visits.js";

export default function PublicLayout() {
  const location = useLocation();

  useEffect(() => {
    recordVisit(location.pathname).catch(() => {
      // Non-critical — the visit counter is a nice-to-have, never blocks navigation.
    });
  }, [location.pathname]);

  return (
    <div className="min-h-screen flex flex-col">
      <Topbar />
      <Navbar />
      <main className="flex-1">
        <Outlet />
      </main>
      <Footer />
      <ChatFab />
      <GoogleRegisterModal />
    </div>
  );
}
