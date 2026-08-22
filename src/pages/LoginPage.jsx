import { useState } from "react";
import { Link, useLocation, useNavigate } from "react-router-dom";
import { LogIn } from "lucide-react";
import { useAuth } from "../context/AuthContext.jsx";
import { useGoogleButton } from "../hooks/useGoogleButton.js";

export default function LoginPage() {
  const { login, loginWithGoogle } = useAuth();
  const navigate = useNavigate();
  const location = useLocation();
  const [email, setEmail] = useState("");
  const [password, setPassword] = useState("");
  const [error, setError] = useState("");
  const [busy, setBusy] = useState(false);

  function routeAfterLogin(user) {
    const canSeeDashboard = user.role === "ADMIN" || (user.permissions ?? []).includes("analytics.view");
    const dest =
      location.state?.from ??
      (user.role === "STUDENT" ? "/" : canSeeDashboard ? "/leader/analytics" : "/");
    navigate(dest, { replace: true });
  }

  async function handleGoogleCredential(idToken) {
    setError("");
    try {
      const res = await loginWithGoogle(idToken);
      if (res.data.user.status === "ACTIVE") routeAfterLogin(res.data.user);
      else if (res.data.user.status === "PENDING") navigate("/pending");
      else setError(res.message ?? "Konti yawe ntiyemerewe kwinjira ubu.");
    } catch (err) {
      setError(err.message);
    }
  }

  const { buttonRef, configured } = useGoogleButton(handleGoogleCredential, { text: "signin_with" });

  async function handleSubmit(e) {
    e.preventDefault();
    setError("");
    setBusy(true);
    try {
      const user = await login(email, password);
      routeAfterLogin(user);
    } catch (err) {
      setError(err.message);
    } finally {
      setBusy(false);
    }
  }

  return (
    <div className="min-h-[70vh] flex items-center justify-center px-6 py-16">
      <div className="w-full max-w-[420px]">
        <div className="text-center mb-8">
          <div className="w-[54px] h-[54px] rounded-full border-2 border-gold-500 bg-green-950 flex items-center justify-center mx-auto mb-4">
            <svg viewBox="0 0 24 24" fill="none" stroke="#cf9d3f" strokeWidth="1.6" className="w-7 h-7">
              <path d="M4 21V11l8-6 8 6v10" />
              <path d="M9 21v-6h6v6" />
            </svg>
          </div>
          <h1 className="font-display text-[30px] font-bold text-green-950">Injira kuri Kwegereza</h1>
          <p className="text-ink-soft text-sm mt-1">Injiza email n'ijambo ry'ibanga byawe</p>
        </div>

        <form onSubmit={handleSubmit} className="card space-y-4">
          {error && (
            <div className="bg-red-50 border border-red-200 text-red-700 text-sm rounded-xl px-4 py-3">
              {error}
            </div>
          )}
          <div>
            <label className="block text-xs font-bold text-ink-soft mb-1.5 uppercase tracking-wide">
              Email
            </label>
            <input
              type="email"
              required
              value={email}
              onChange={(e) => setEmail(e.target.value)}
              className="w-full rounded-xl border border-line px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-gold-400"
              placeholder="wowe@example.com"
            />
          </div>
          <div>
            <label className="block text-xs font-bold text-ink-soft mb-1.5 uppercase tracking-wide">
              Ijambo ry'ibanga
            </label>
            <input
              type="password"
              required
              value={password}
              onChange={(e) => setPassword(e.target.value)}
              className="w-full rounded-xl border border-line px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-gold-400"
              placeholder="••••••••"
            />
          </div>
          <button type="submit" disabled={busy} className="btn btn-gold w-full justify-center disabled:opacity-60">
            <LogIn size={17} />
            {busy ? "Turimo kwinjira..." : "Injira"}
          </button>
        </form>

        {configured && (
          <>
            <div className="flex items-center gap-3 my-5 text-xs text-ink-soft">
              <div className="flex-1 h-px bg-line" /> cyangwa <div className="flex-1 h-px bg-line" />
            </div>
            <div ref={buttonRef} className="flex justify-center" />
          </>
        )}

        <p className="text-center text-sm text-ink-soft mt-6">
          Nta konti ufite?{" "}
          <Link to="/register" className="text-gold-600 font-bold">
            Iyandikishe
          </Link>
        </p>

        <div className="mt-8 text-xs text-ink-soft bg-cream-2 rounded-xl p-4 leading-relaxed">
          <b className="block mb-1 text-green-950">Konti zo kugerageza:</b>
          Admin: admin@kwegereza.rw / Admin@12345
          <br />
          Umuyobozi: leader@kwegereza.rw / Leader@12345
        </div>
      </div>
    </div>
  );
}
