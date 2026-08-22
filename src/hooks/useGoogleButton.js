import { useEffect, useRef, useState } from "react";
import { GOOGLE_CLIENT_ID, isGoogleConfigured } from "../config/google.js";

function loadGoogleScript() {
  return new Promise((resolve, reject) => {
    if (window.google?.accounts?.id) {
      resolve();
      return;
    }
    const existing = document.getElementById("google-identity-script");
    if (existing) {
      existing.addEventListener("load", () => resolve());
      existing.addEventListener("error", () => reject(new Error("Google script ntiyapakiwe.")));
      return;
    }
    const script = document.createElement("script");
    script.id = "google-identity-script";
    script.src = "https://accounts.google.com/gsi/client";
    script.async = true;
    script.defer = true;
    script.onload = () => resolve();
    script.onerror = () => reject(new Error("Google script ntiyapakiwe."));
    document.head.appendChild(script);
  });
}

/**
 * Renders Google's real "Sign in with Google" widget into the returned ref.
 * Shared by GoogleRegisterModal, LoginPage, and RegisterPage so the
 * script-loading + initialize/renderButton dance only needs to be correct
 * once. `configured` is false (no widget) until a real Client ID is set.
 */
export function useGoogleButton(onCredential, options = {}) {
  const buttonRef = useRef(null);
  const [ready, setReady] = useState(false);

  useEffect(() => {
    if (!isGoogleConfigured()) return undefined;
    let cancelled = false;
    loadGoogleScript()
      .then(() => {
        if (!cancelled) setReady(true);
      })
      .catch(() => setReady(false));
    return () => {
      cancelled = true;
    };
  }, []);

  useEffect(() => {
    if (!ready || !buttonRef.current || !window.google?.accounts?.id) return;
    window.google.accounts.id.initialize({
      client_id: GOOGLE_CLIENT_ID,
      callback: (response) => onCredential(response.credential),
    });
    window.google.accounts.id.renderButton(buttonRef.current, {
      theme: "filled_black",
      size: "large",
      shape: "pill",
      width: 280,
      text: "continue_with",
      ...options,
    });
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [ready]);

  return { buttonRef, configured: isGoogleConfigured() };
}
