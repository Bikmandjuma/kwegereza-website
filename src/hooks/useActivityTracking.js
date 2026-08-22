import { useEffect, useRef } from "react";
import { useLocation } from "react-router-dom";
import { useAuth } from "../context/AuthContext.jsx";
import { sendHeartbeat, trackEvent } from "../api/activity.js";

const HEARTBEAT_INTERVAL_MS = 30000;

/**
 * Fires a PAGE_VIEW on real navigation (not on every render — only when the
 * pathname actually changes) and a heartbeat every 30s while the app is open
 * and the user is logged in. This is intentionally the ONLY place these two
 * things happen, so there's exactly one source of PAGE_VIEW/heartbeat calls
 * to reason about.
 */
export function useActivityTracking() {
  const { user } = useAuth();
  const location = useLocation();
  const lastPathRef = useRef(null);

  useEffect(() => {
    if (!user) return;
    if (lastPathRef.current === location.pathname) return;
    lastPathRef.current = location.pathname;
    trackEvent("PAGE_VIEW", { path: location.pathname }).catch(() => {});
  }, [user, location.pathname]);

  useEffect(() => {
    if (!user) return undefined;
    const interval = setInterval(() => {
      sendHeartbeat().catch(() => {});
    }, HEARTBEAT_INTERVAL_MS);
    return () => clearInterval(interval);
  }, [user]);
}
