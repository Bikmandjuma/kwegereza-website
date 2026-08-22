import { useState } from "react";
import { Link, useNavigate } from "react-router-dom";
import { UserPlus } from "lucide-react";
import { useAuth } from "../context/AuthContext.jsx";
import { useGoogleButton } from "../hooks/useGoogleButton.js";

export default function RegisterPage() {
  const { register, loginWithGoogle } = useAuth();
  const navigate = useNavigate();
  const [form, setForm] = useState({ fullName: "", email: "", phone: "", password: "", gender: "", kunia: "" });
  const [error, setError] = useState("");
  const [busy, setBusy] = useState(false);

  function update(field) {
    return (e) => setForm((f) => ({ ...f, [field]: e.target.value }));
  }

  async function handleGoogleCredential(idToken) {
    setError("");
    try {
      const res = await loginWithGoogle(idToken);
      if (res.data.user.status === "ACTIVE") navigate("/", { replace: true });
      else if (res.data.user.status === "PENDING") navigate("/pending", { replace: true, state: { email: res.data.user.email } });
      else setError(res.message ?? "Konti yawe ntiyemerewe kwinjira ubu.");
    } catch (err) {
      setError(err.message);
    }
  }

  const { buttonRef, configured } = useGoogleButton(handleGoogleCredential, { text: "signup_with" });

  async function handleSubmit(e) {
    e.preventDefault();
    setError("");
    setBusy(true);
    try {
      await register(form);
      navigate("/pending", { replace: true, state: { email: form.email } });
    } catch (err) {
      setError(err.message);
    } finally {
      setBusy(false);
    }
  }

  return (
    <div className="min-h-[70vh] flex items-center justify-center px-6 py-16">
      <div className="w-full max-w-[440px]">
        <div className="text-center mb-8">
          <div className="eyebrow justify-center">TWIYUNGEHO</div>
          <h1 className="font-display text-[30px] font-bold text-green-950">Iyandikishe kuri Kwegereza</h1>
          <p className="text-ink-soft text-sm mt-1">
            Konti yawe izategereza kwemezwa n'ubuyobozi mbere yo gukoreshwa.
          </p>
        </div>

        <form onSubmit={handleSubmit} className="card space-y-4">
          {error && (
            <div className="bg-red-50 border border-red-200 text-red-700 text-sm rounded-xl px-4 py-3">
              {error}
            </div>
          )}
          <div>
            <label className="block text-xs font-bold text-ink-soft mb-1.5 uppercase tracking-wide">
              Amazina yombi
            </label>
            <input
              required
              value={form.fullName}
              onChange={update("fullName")}
              className="w-full rounded-xl border border-line px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-gold-400"
              placeholder="Aisha Uwimana"
            />
          </div>
          <div>
            <label className="block text-xs font-bold text-ink-soft mb-1.5 uppercase tracking-wide">
              Email
            </label>
            <input
              type="email"
              required
              value={form.email}
              onChange={update("email")}
              className="w-full rounded-xl border border-line px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-gold-400"
              placeholder="wowe@example.com"
            />
          </div>
          <div>
            <label className="block text-xs font-bold text-ink-soft mb-1.5 uppercase tracking-wide">
              Telefoni (si ngombwa)
            </label>
            <input
              value={form.phone}
              onChange={update("phone")}
              className="w-full rounded-xl border border-line px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-gold-400"
              placeholder="+250 7XX XXX XXX"
            />
          </div>
          <div className="grid grid-cols-2 gap-3">
            <div>
              <label className="block text-xs font-bold text-ink-soft mb-1.5 uppercase tracking-wide">
                Igitsina (si ngombwa)
              </label>
              <select
                value={form.gender}
                onChange={update("gender")}
                className="w-full rounded-xl border border-line px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-gold-400 bg-surface"
              >
                <option value="">Hitamo...</option>
                <option value="MALE">Gabo</option>
                <option value="FEMALE">Gore</option>
              </select>
            </div>
            <div>
              <label className="block text-xs font-bold text-ink-soft mb-1.5 uppercase tracking-wide">
                Kunia (si ngombwa)
              </label>
              <input
                value={form.kunia}
                onChange={update("kunia")}
                className="w-full rounded-xl border border-line px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-gold-400"
                placeholder="Umm-Ruslaan"
              />
            </div>
          </div>
          <div>
            <label className="block text-xs font-bold text-ink-soft mb-1.5 uppercase tracking-wide">
              Ijambo ry'ibanga
            </label>
            <input
              type="password"
              required
              minLength={6}
              value={form.password}
              onChange={update("password")}
              className="w-full rounded-xl border border-line px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-gold-400"
              placeholder="Byibura inyuguti 6"
            />
          </div>
          <button type="submit" disabled={busy} className="btn btn-gold w-full justify-center disabled:opacity-60">
            <UserPlus size={17} />
            {busy ? "Turimo kwiyandikisha..." : "Iyandikishe"}
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
          Usanzwe ufite konti?{" "}
          <Link to="/login" className="text-gold-600 font-bold">
            Injira
          </Link>
        </p>
      </div>
    </div>
  );
}
