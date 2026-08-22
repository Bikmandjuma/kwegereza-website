import { Link } from "react-router-dom";
import { MessageCircle } from "lucide-react";
import { useAuth } from "../context/AuthContext.jsx";

export default function ChatFab() {
  const { user } = useAuth();
  if (!user) return null; // chat requires an ACTIVE account — nothing to link to otherwise

  return (
    <Link
      to="/chat"
      title="Kwegereza Chat"
      className="fixed right-6 bottom-6 w-[58px] h-[58px] rounded-full bg-gradient-to-br from-green-800 to-green-950 text-white flex items-center justify-center shadow-[0_16px_30px_-10px_rgba(8,37,28,0.55)] z-[80]"
    >
      <MessageCircle size={24} />
    </Link>
  );
}
