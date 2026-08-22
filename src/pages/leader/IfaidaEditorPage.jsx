import { useEffect, useRef, useState, useCallback } from "react";
import { useNavigate, useParams } from "react-router-dom";
import { ArrowLeft, Eye, EyeOff, X } from "lucide-react";
import AdminLayout from "../../layouts/AdminLayout.jsx";
import RichTextEditor from "../../components/RichTextEditor.jsx";
import { useAuth } from "../../context/AuthContext.jsx";
import {
  getMyIfaida,
  publishIfaida,
  unpublishIfaida,
  updateIfaida,
} from "../../api/ifaida.js";

const AUTOSAVE_DELAY_MS = 1500;

export default function IfaidaEditorPage() {
  const { id } = useParams();
  const navigate = useNavigate();
  const { user } = useAuth();
  const isAdmin = user?.role === "ADMIN";
  const canPublish = isAdmin || (user?.permissions ?? []).includes("ifaida.publish");

  const [post, setPost] = useState(null);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState("");
  const [saveState, setSaveState] = useState("idle"); // idle | saving | saved
  const [showPreview, setShowPreview] = useState(false);
  const saveTimeout = useRef(null);
  const latestFields = useRef(null);

  useEffect(() => {
    getMyIfaida(id)
      .then((res) => setPost(res.data))
      .catch((err) => setError(err.message))
      .finally(() => setLoading(false));
  }, [id]);

  const flushSave = useCallback(async () => {
    if (!latestFields.current) return;
    setSaveState("saving");
    try {
      const res = await updateIfaida(id, latestFields.current);
      setSaveState("saved");
      setTimeout(() => setSaveState("idle"), 2000);
      return res.data;
    } catch (err) {
      setError(err.message);
      setSaveState("idle");
    }
  }, [id]);

  // Debounced autosave: every field edit resets the timer. A database write
  // only happens after the person stops typing for AUTOSAVE_DELAY_MS —
  // never on every keystroke.
  function scheduleAutosave(fields) {
    latestFields.current = { ...latestFields.current, ...fields };
    clearTimeout(saveTimeout.current);
    saveTimeout.current = setTimeout(flushSave, AUTOSAVE_DELAY_MS);
  }

  function updateField(key, value) {
    setPost((prev) => ({ ...prev, [key]: value }));
    scheduleAutosave({ [key]: value });
  }

  async function handlePublishToggle() {
    clearTimeout(saveTimeout.current);
    await flushSave();
    try {
      const res = post.status === "PUBLISHED" ? await unpublishIfaida(id) : await publishIfaida(id);
      setPost(res.data);
    } catch (err) {
      setError(err.message);
    }
  }

  if (loading) {
    return (
      <AdminLayout breadcrumb="Ahabanza / Ifaida" title="Ifaida">
        <div className="text-center text-ink-soft text-sm py-16">Turimo gupakira...</div>
      </AdminLayout>
    );
  }

  if (error && !post) {
    return (
      <AdminLayout breadcrumb="Ahabanza / Ifaida" title="Ifaida">
        <div className="bg-red-50 border border-red-200 text-red-700 text-sm rounded-xl px-4 py-3">{error}</div>
      </AdminLayout>
    );
  }

  return (
    <AdminLayout breadcrumb="Ahabanza / Ifaida" title="Hindura Ifaida">
      <div className="flex items-center justify-between mb-6">
        <button
          onClick={() => navigate("/leader/ifaida")}
          className="flex items-center gap-1.5 text-sm font-semibold text-ink-soft"
        >
          <ArrowLeft size={16} /> Subira ku rutonde
        </button>
        <div className="flex items-center gap-3">
          <span className="text-xs font-semibold text-ink-soft">
            {saveState === "saving" && "Bika..."}
            {saveState === "saved" && "Bikawe ✓"}
          </span>
          <button
            onClick={() => setShowPreview((p) => !p)}
            className="btn btn-outline !border-green-900 !text-green-950 !py-2"
          >
            {showPreview ? <X size={15} /> : <Eye size={15} />}
            {showPreview ? "Funga Preview" : "Preview"}
          </button>
          {canPublish && (
            <button onClick={handlePublishToggle} className="btn btn-gold !py-2">
              {post.status === "PUBLISHED" ? <EyeOff size={15} /> : <Eye size={15} />}
              {post.status === "PUBLISHED" ? "Hagarika" : "Tangaza"}
            </button>
          )}
        </div>
      </div>

      {error && (
        <div className="bg-red-50 border border-red-200 text-red-700 text-sm rounded-xl px-4 py-3 mb-5">{error}</div>
      )}

      {showPreview ? (
        <div className="card max-w-[720px] mx-auto">
          <div className="eyebrow">{post.category || "Ifaida"}</div>
          <h1 className="font-display text-[32px] font-bold text-green-950 mb-2">{post.title}</h1>
          <div className="text-xs text-ink-soft mb-6 flex items-center gap-3">
            <span>{user.fullName}</span>
            <span>•</span>
            <span>{post.readingMinutes} min</span>
          </div>
          {post.coverImage && (
            <img src={post.coverImage} alt="" className="w-full h-[260px] object-cover rounded-2xl mb-6" />
          )}
          <div className="prose-editor text-[15px] leading-relaxed" dangerouslySetInnerHTML={{ __html: post.content }} />
        </div>
      ) : (
        <div className="grid grid-cols-1 lg:grid-cols-[1fr_280px] gap-6">
          <div className="space-y-4">
            <input
              value={post.title}
              onChange={(e) => updateField("title", e.target.value)}
              placeholder="Umutwe w'Ifaida"
              className="w-full text-2xl font-display font-bold text-green-950 border-b border-line pb-3 outline-none bg-transparent"
            />
            <textarea
              value={post.description}
              onChange={(e) => updateField("description", e.target.value)}
              placeholder="Isobanuro rigufi (rigaragara ku rutonde)"
              rows={2}
              className="w-full text-sm border border-line rounded-xl p-3 outline-none focus:ring-2 focus:ring-gold-400 bg-surface"
            />
            <RichTextEditor value={post.content} onChange={(html) => updateField("content", html)} />
          </div>

          <div className="space-y-4">
            <div className="card !p-4">
              <label className="block text-xs font-bold text-ink-soft uppercase mb-1.5">Umwanditsi</label>
              <div className="text-sm font-semibold text-green-950">{user.fullName}</div>
            </div>
            <div className="card !p-4">
              <label className="block text-xs font-bold text-ink-soft uppercase mb-1.5">Icyiciro</label>
              <input
                value={post.category}
                onChange={(e) => updateField("category", e.target.value)}
                placeholder="Urugero: Aqida, Fiqh, Sira..."
                className="w-full text-sm border border-line rounded-lg px-3 py-2 outline-none bg-surface"
              />
            </div>
            <div className="card !p-4">
              <label className="block text-xs font-bold text-ink-soft uppercase mb-1.5">Ifoto (URL)</label>
              <input
                value={post.coverImage ?? ""}
                onChange={(e) => updateField("coverImage", e.target.value)}
                placeholder="https://..."
                className="w-full text-sm border border-line rounded-lg px-3 py-2 outline-none bg-surface"
              />
            </div>
            <div className="card !p-4">
              <label className="block text-xs font-bold text-ink-soft uppercase mb-1.5">Uko bimeze</label>
              <span
                className={`text-xs font-bold px-2.5 py-1 rounded-full ${
                  post.status === "PUBLISHED" ? "bg-emerald-50 text-emerald-700" : "bg-amber-50 text-amber-700"
                }`}
              >
                {post.status === "PUBLISHED" ? "Byatangajwe" : "Umushinga"}
              </span>
              {post.publishedAt && (
                <div className="text-xs text-ink-soft mt-2">
                  {new Date(post.publishedAt).toLocaleString("rw-RW")}
                </div>
              )}
            </div>
          </div>
        </div>
      )}
    </AdminLayout>
  );
}
