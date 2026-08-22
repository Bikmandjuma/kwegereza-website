import { useLocation, useNavigate } from "react-router-dom";
import { Clock, LogOut, Mail } from "lucide-react";

export default function PendingApprovalPage() {
  const location = useLocation();
  const navigate = useNavigate();
  const email = location.state?.email;

  return (
    <div className="min-h-[70vh] flex items-center justify-center px-6 py-16">
      <div className="w-full max-w-[480px] text-center">
        <div
          className="w-20 h-20 rounded-full mx-auto mb-6 flex items-center justify-center text-white"
          style={{ background: "linear-gradient(135deg,#0b3d2e,#cf9d3f)" }}
        >
          <Clock size={34} />
        </div>
        <h1 className="font-display text-[30px] font-bold text-green-950 mb-3">
          Konti yawe iri gutegereza kwemezwa.
        </h1>
        <p className="text-ink-soft text-[15px] leading-relaxed mb-8">
          Murakoze kwiyandikisha kuri Kwegereza. Konti yawe igomba kubanza kwemezwa n'ubuyobozi
          mbere yo kubona amasomo, ibitabo, na chat.
        </p>

        <div className="card text-left space-y-3 mb-8">
          {email && (
            <div className="flex items-center gap-3 text-sm">
              <Mail size={16} className="text-gold-600 flex-none" />
              <span className="text-ink-soft">
                Email: <b className="text-green-950">{email}</b>
              </span>
            </div>
          )}
          <div className="flex items-center gap-3 text-sm">
            <Clock size={16} className="text-gold-600 flex-none" />
            <span className="text-ink-soft">
              Uko byifashe ubu: <b className="text-amber-600">Bitegereje / PENDING</b>
            </span>
          </div>
        </div>

        <button
          onClick={() => navigate("/")}
          className="btn btn-outline !border-green-900 !text-green-950 mx-auto"
        >
          <LogOut size={16} />
          Subira ku rubuga
        </button>
      </div>
    </div>
  );
}
