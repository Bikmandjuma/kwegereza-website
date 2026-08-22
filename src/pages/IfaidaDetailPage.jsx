import { useEffect, useState } from "react";
import { useParams, Link } from "react-router-dom";
import { ArrowLeft, Clock, User } from "lucide-react";
import { getPublishedIfaida } from "../api/ifaida.js";

export default function IfaidaDetailPage() {
  const { id } = useParams();
  const [post, setPost] = useState(null);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState("");

  useEffect(() => {
    getPublishedIfaida(id)
      .then((res) => setPost(res.data))
      .catch((err) => setError(err.message))
      .finally(() => setLoading(false));
  }, [id]);

  if (loading) {
    return <div className="py-24 text-center text-ink-soft text-sm">Turimo gupakira...</div>;
  }

  if (error || !post) {
    return (
      <div className="py-24 max-w-[600px] mx-auto px-6 text-center">
        <p className="text-ink-soft text-sm mb-4">{error || "Iyi nyandiko ntiboneka."}</p>
        <Link to="/inyandiko" className="btn btn-gold inline-flex">
          <ArrowLeft size={15} /> Subira ku Nyandiko
        </Link>
      </div>
    );
  }

  return (
    <div className="py-16 max-w-[720px] mx-auto px-6">
      <Link to="/inyandiko" className="flex items-center gap-1.5 text-sm font-semibold text-ink-soft mb-8">
        <ArrowLeft size={16} /> Subira ku Nyandiko
      </Link>

      {post.category && <div className="eyebrow">{post.category}</div>}
      <h1 className="font-display text-[36px] font-bold text-green-950 mb-4 leading-tight">{post.title}</h1>

      <div className="flex items-center gap-4 text-sm text-ink-soft mb-8 pb-8 border-b border-line">
        <span className="flex items-center gap-1.5">
          <User size={14} /> {post.authorName}
        </span>
        <span className="flex items-center gap-1.5">
          <Clock size={14} /> {post.readingMinutes} min
        </span>
        {post.publishedAt && <span>{new Date(post.publishedAt).toLocaleDateString("rw-RW")}</span>}
      </div>

      {post.coverImage && (
        <img src={post.coverImage} alt="" className="w-full h-[320px] object-cover rounded-2xl mb-8" />
      )}

      <div
        className="prose-editor text-[16px] leading-loose text-ink"
        dangerouslySetInnerHTML={{ __html: post.content }}
      />
    </div>
  );
}
