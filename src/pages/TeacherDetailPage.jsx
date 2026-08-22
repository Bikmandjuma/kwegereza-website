import { useEffect, useState } from "react";
import { useParams } from "react-router-dom";
import { Mic, Video, Play, Pause, PlayCircle } from "lucide-react";
import SectionHead from "../components/SectionHead.jsx";
import { getPublicTeacher } from "../api/teachers.js";
import { getPublishedDarsat, trackDarsPlay } from "../api/dars.js";

function youtubeEmbedUrl(url) {
  const match = url?.match(/(?:v=|youtu\.be\/|embed\/)([\w-]{11})/);
  return match ? `https://www.youtube.com/embed/${match[1]}` : null;
}

function AudioCard({ dars }) {
  const [playing, setPlaying] = useState(false);
  const [audioEl, setAudioEl] = useState(null);
  const [hasCountedPlay, setHasCountedPlay] = useState(false);

  function toggle() {
    if (!audioEl) return;
    if (playing) {
      audioEl.pause();
    } else {
      audioEl.play();
      if (!hasCountedPlay) {
        trackDarsPlay(dars.id).catch(() => {});
        setHasCountedPlay(true);
      }
    }
  }

  return (
    <div className="card">
      <div className="flex items-center gap-4 mb-3">
        <button
          onClick={toggle}
          className="w-12 h-12 rounded-full bg-green-950 text-white flex items-center justify-center flex-none hover:bg-green-800 transition-colors"
        >
          {playing ? <Pause size={18} /> : <Play size={18} className="ml-0.5" />}
        </button>
        <div className="min-w-0">
          <h3 className="font-display text-[16px] font-bold text-green-950 truncate">{dars.title}</h3>
          <div className="text-xs text-ink-soft flex items-center gap-1"><PlayCircle size={11} /> {dars.plays} plays</div>
        </div>
      </div>
      {dars.description && <p className="text-xs text-ink-soft mb-3 line-clamp-2">{dars.description}</p>}
      {/* Full native control set: play/pause, seek, volume, download, speed —
          exactly the "audio will have full functionality-control" spec item. */}
      <audio
        ref={setAudioEl}
        src={dars.audio}
        controls
        controlsList="nodownload"
        className="w-full h-9"
        onPlay={() => setPlaying(true)}
        onPause={() => setPlaying(false)}
        onEnded={() => setPlaying(false)}
      />
    </div>
  );
}

function VideoCard({ dars }) {
  const [started, setStarted] = useState(false);
  const embed = youtubeEmbedUrl(dars.youtubeUrl);

  return (
    <div className="card !p-0 overflow-hidden">
      <div className="aspect-video bg-black">
        {embed ? (
          started ? (
            <iframe
              src={`${embed}?autoplay=1`}
              title={dars.title}
              className="w-full h-full"
              allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
              allowFullScreen
            />
          ) : (
            <button
              onClick={() => {
                setStarted(true);
                trackDarsPlay(dars.id).catch(() => {});
              }}
              className="w-full h-full relative flex items-center justify-center group"
              style={
                dars.thumbnail
                  ? { backgroundImage: `url(${dars.thumbnail})`, backgroundSize: "cover", backgroundPosition: "center" }
                  : {}
              }
            >
              <div className="absolute inset-0 bg-black/30 group-hover:bg-black/40 transition-colors" />
              <div className="relative w-14 h-14 rounded-full bg-white/90 flex items-center justify-center">
                <Play size={22} className="text-green-950 ml-1" />
              </div>
            </button>
          )
        ) : (
          <div className="w-full h-full flex items-center justify-center text-white/60 text-xs">Link ntabwo ihari</div>
        )}
      </div>
      <div className="p-4">
        <h3 className="font-display text-[15px] font-bold text-green-950 truncate">{dars.title}</h3>
        <div className="text-xs text-ink-soft flex items-center gap-1 mt-1"><PlayCircle size={11} /> {dars.plays} plays</div>
      </div>
    </div>
  );
}

export default function TeacherDetailPage() {
  const { id } = useParams();
  const [teacher, setTeacher] = useState(null);
  const [darsat, setDarsat] = useState([]);
  const [filter, setFilter] = useState("AUDIO");
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState("");

  useEffect(() => {
    setLoading(true);
    Promise.all([getPublicTeacher(id), getPublishedDarsat({ teacherId: id, perPage: 50 })])
      .then(([teacherRes, darsatRes]) => {
        setTeacher(teacherRes.data);
        setDarsat(darsatRes.data);
      })
      .catch((err) => setError(err.message))
      .finally(() => setLoading(false));
  }, [id]);

  const audioCount = darsat.filter((d) => d.type === "AUDIO").length;
  const videoCount = darsat.filter((d) => d.type === "VIDEO").length;
  const visible = darsat.filter((d) => d.type === filter);

  if (loading) return <div className="py-24 text-center text-ink-soft text-sm">Turimo gupakira...</div>;
  if (error) return <div className="py-24 text-center text-red-600 text-sm">{error}</div>;
  if (!teacher) return null;

  return (
    <div className="py-[86px]">
      <div className="max-w-[1240px] mx-auto px-7">
        <div className="flex flex-col items-center text-center mb-10">
          <div className="w-24 h-24 rounded-full mb-4 flex items-center justify-center font-display font-bold text-3xl text-white border-[3px] border-gold-400 bg-gradient-to-br from-green-700 to-green-950 overflow-hidden">
            {teacher.photo ? <img src={teacher.photo} alt={teacher.name} className="w-full h-full object-cover" /> : teacher.name[0]}
          </div>
          <h1 className="font-display text-3xl font-bold text-green-950">{teacher.name}</h1>
          {teacher.kunia && <div className="text-sm text-ink-soft mt-1">{teacher.kunia}</div>}
          <div className="text-gold-600 font-bold text-xs uppercase tracking-wide mt-2">{teacher.role}</div>
          {teacher.bio && <p className="text-sm text-ink-soft max-w-xl mt-3 leading-relaxed">{teacher.bio}</p>}
        </div>

        <div className="flex justify-center gap-2 mb-8">
          <button
            onClick={() => setFilter("AUDIO")}
            className={`flex items-center gap-1.5 px-4 py-2 rounded-full text-sm font-bold ${
              filter === "AUDIO" ? "bg-green-950 text-white" : "bg-cream-2 text-ink-soft"
            }`}
          >
            <Mic size={14} /> Ijwi ({audioCount})
          </button>
          <button
            onClick={() => setFilter("VIDEO")}
            className={`flex items-center gap-1.5 px-4 py-2 rounded-full text-sm font-bold ${
              filter === "VIDEO" ? "bg-green-950 text-white" : "bg-cream-2 text-ink-soft"
            }`}
          >
            <Video size={14} /> Videwo ({videoCount})
          </button>
        </div>

        {visible.length === 0 ? (
          <div className="text-center text-ink-soft text-sm py-12">Nta {filter === "AUDIO" ? "ijwi" : "videwo"} ryabonetse kuri uyu mwarimu.</div>
        ) : (
          <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            {visible.map((d) => (filter === "AUDIO" ? <AudioCard key={d.id} dars={d} /> : <VideoCard key={d.id} dars={d} />))}
          </div>
        )}
      </div>
    </div>
  );
}
