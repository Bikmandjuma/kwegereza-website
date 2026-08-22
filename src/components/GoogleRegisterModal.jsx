import { useEffect, useState } from "react";
import { useNavigate } from "react-router-dom";
import { X } from "lucide-react";
import { useAuth } from "../context/AuthContext.jsx";
import { isGoogleConfigured } from "../config/google.js";
import { useGoogleButton } from "../hooks/useGoogleButton.js";

// Configurable per spec ("The delay must be configurable") — override via
// .env without touching code: VITE_GOOGLE_MODAL_DELAY_MS=8000
const DELAY_MS = Number(import.meta.env.VITE_GOOGLE_MODAL_DELAY_MS) || 5000;

const NEXT_AT_KEY = "kiu_google_modal_next_at";
const DISMISS_COUNT_KEY = "kiu_google_modal_dismiss_count";
// Escalating snooze so a guest who keeps closing it sees it less and less
// often, rather than every single visit ("Do not annoy the user repeatedly").
const SNOOZE_DAYS_BY_COUNT = [3, 7, 14, 30, 60];

function msUntilAllowed() {
  const nextAt = Number(localStorage.getItem(NEXT_AT_KEY) || 0);
  return nextAt - Date.now();
}

function snooze() {
  const count = Number(localStorage.getItem(DISMISS_COUNT_KEY) || 0);
  const days = SNOOZE_DAYS_BY_COUNT[Math.min(count, SNOOZE_DAYS_BY_COUNT.length - 1)];
  localStorage.setItem(NEXT_AT_KEY, String(Date.now() + days * 86400000));
  localStorage.setItem(DISMISS_COUNT_KEY, String(count + 1));
}

export default function GoogleRegisterModal() {
  const { user, loginWithGoogle } = useAuth();
  const navigate = useNavigate();
  const [visible, setVisible] = useState(false);
  const [error, setError] = useState("");

  async function handleCredential(idToken) {
    setError("");
    try {
      const res = await loginWithGoogle(idToken);
      if (res.data.user.status === "ACTIVE") {
        setVisible(false);
        navigate("/");
      } else if (res.data.user.status === "PENDING") {
        setVisible(false);
        navigate("/pending");
      } else {
        setError(res.message ?? "Konti yawe ntiyemerewe kwinjira ubu.");
      }
    } catch (err) {
      setError(err.message);
    }
  }

  const { buttonRef, configured } = useGoogleButton(handleCredential, { text: "signup_with" });

  // The 5s (configurable) arrival timer — only for guests, only if not
  // currently snoozed from a previous dismissal.
  useEffect(() => {
    if (user) return undefined;
    if (msUntilAllowed() > 0) return undefined;
    const t = setTimeout(() => setVisible(true), DELAY_MS);
    return () => clearTimeout(t);
  }, [user]);

  function dismiss() {
    snooze();
    setVisible(false);
  }

  function goRegister() {
    setVisible(false);
    navigate("/register");
  }

  function goLogin() {
    setVisible(false);
    navigate("/login");
  }

  if (!visible || user) return null;

  return (
    <div className="fixed inset-0 bg-black/50 z-[300] flex items-center justify-center p-4" onClick={dismiss}>
      <div
        className="bg-surface rounded-3xl w-full max-w-sm p-7 text-center relative shadow-2xl"
        onClick={(e) => e.stopPropagation()}
      >
        <button
          onClick={dismiss}
          className="absolute top-4 right-4 w-8 h-8 rounded-full hover:bg-cream-2 flex items-center justify-center text-ink-soft"
          aria-label="Funga"
        >
          <X size={16} />
        </button>

        <div className="w-14 h-14 rounded-full border-2 border-gold-500 bg-green-950 flex items-center justify-center mx-auto mb-4">
          <svg viewBox="0 0 24 24" fill="none" stroke="#cf9d3f" strokeWidth="1.6" className="w-7 h-7">
            <path d="M4 21V11l8-6 8 6v10" />
            <path d="M9 21v-6h6v6" />
          </svg>
        </div>

        <h3 className="font-display text-xl font-bold text-green-950 mb-1.5">Murakaza neza kuri Kwegereza.</h3>
        <p className="text-sm text-ink-soft mb-6 leading-relaxed">
          Kwiyandikisha bizagufasha gukomeza kwiga no kubona serivisi zose.
        </p>

        {error && (
          <div className="bg-red-50 border border-red-200 text-red-700 text-xs rounded-xl px-3 py-2 mb-4">{error}</div>
        )}

        <div className="flex flex-col items-center gap-3">
          {configured ? (
            <div ref={buttonRef} className="flex justify-center" />
          ) : (
            <div className="text-[11px] text-ink-soft/70 italic">Google Sign-In izaza vuba.</div>
          )}

          <button onClick={goRegister} className="btn btn-gold w-full justify-center">
            Iyandikishe
          </button>
          <button onClick={goLogin} className="btn btn-outline !border-line !text-green-950 w-full justify-center">
            Mfite konti — Injira
          </button>
          <button onClick={dismiss} className="text-xs font-semibold text-ink-soft mt-1 hover:text-green-950">
            Ntabwo niteguye ubu
          </button>
        </div>
      </div>
    </div>
  );
}
