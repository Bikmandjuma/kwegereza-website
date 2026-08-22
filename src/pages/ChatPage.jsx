import { useEffect, useRef, useState, useCallback } from "react";
import { useSearchParams } from "react-router-dom";
import { Search, Send, Circle } from "lucide-react";
import { getToken } from "../api/client.js";
import { trackEvent } from "../api/activity.js";
import { useAuth } from "../context/AuthContext.jsx";
import { useChatSocket } from "../hooks/useChatSocket.js";
import { listConversations, listMessages, markConversationRead, startConversation } from "../api/chat.js";
import { searchUsers } from "../api/users.js";

function formatTime(iso) {
  return new Date(iso).toLocaleTimeString("rw-RW", { hour: "2-digit", minute: "2-digit" });
}

export default function ChatPage() {
  const { user } = useAuth();
  const token = getToken();
  const { connected, joinConversation, sendMessage, onNewMessage, onTyping, onPresence, emitTyping } =
    useChatSocket(token);

  const [conversations, setConversations] = useState([]);
  const [activeId, setActiveId] = useState(null);
  const [activeOther, setActiveOther] = useState(null);
  const [messages, setMessages] = useState([]);
  const [draft, setDraft] = useState("");
  const [typingUser, setTypingUser] = useState(null);
  const [onlineIds, setOnlineIds] = useState(new Set());
  const [search, setSearch] = useState("");
  const [results, setResults] = useState([]);
  const [error, setError] = useState("");
  const typingTimeout = useRef(null);
  const [, setSearchParams] = useSearchParams();
  const bottomRef = useRef(null);

  const loadConversations = useCallback(async () => {
    try {
      const res = await listConversations();
      setConversations(res.data);
    } catch (err) {
      setError(err.message);
    }
  }, []);

  useEffect(() => {
    loadConversations();
  }, [loadConversations]);

  useEffect(() => {
    trackEvent("CHAT_OPEN").catch(() => {});
  }, []);

  // Real-time presence: keep a live set of who's currently online.
  useEffect(() => onPresence(({ userId, online }) => {
    setOnlineIds((prev) => {
      const next = new Set(prev);
      if (online) next.add(userId);
      else next.delete(userId);
      return next;
    });
  }), [onPresence]);

  async function openConversation(conv) {
    setActiveId(conv.id);
    setActiveOther(conv.otherUser);
    setSearchParams({});
    setResults([]);
    setSearch("");
    try {
      await joinConversation(conv.id);
      const res = await listMessages(conv.id);
      setMessages(res.data);
      await markConversationRead(conv.id);
    } catch (err) {
      setError(err.message);
    }
  }

  // Subscribe to new messages ONLY while a conversation is open, and clean up
  // the listener whenever activeId changes or the component unmounts — this
  // is exactly the pattern the spec calls out: never let a stale listener
  // from a previous conversation keep firing.
  useEffect(() => {
    if (!activeId) return undefined;
    const unsubscribe = onNewMessage((msg) => {
      if (msg.conversationId !== activeId) return;
      setMessages((prev) => {
        // Defense in depth: even though the server guarantees one broadcast
        // per message, never render a second bubble for the same id.
        if (prev.some((m) => m.id === msg.id)) return prev;
        return [...prev, msg];
      });
    });
    return unsubscribe;
  }, [activeId, onNewMessage]);

  useEffect(() => {
    if (!activeId) return undefined;
    const unsubscribe = onTyping(({ conversationId, fullName, typing, userId }) => {
      if (conversationId !== activeId || userId === user.id) return;
      setTypingUser(typing ? fullName : null);
    });
    return unsubscribe;
  }, [activeId, onTyping, user.id]);

  useEffect(() => {
    bottomRef.current?.scrollIntoView({ behavior: "smooth" });
  }, [messages]);

  async function handleSend(e) {
    e.preventDefault();
    const body = draft.trim();
    if (!body || !activeId) return;
    setDraft("");
    emitTyping(activeId, false);
    try {
      await sendMessage(activeId, body);
      // No optimistic local insert needed — the server always echoes
      // message:new back to us too (we're a room member), so there is
      // exactly one code path that adds a bubble to the screen.
    } catch (err) {
      setError(err.message);
    }
  }

  function handleDraftChange(e) {
    setDraft(e.target.value);
    if (!activeId) return;
    emitTyping(activeId, true);
    clearTimeout(typingTimeout.current);
    typingTimeout.current = setTimeout(() => emitTyping(activeId, false), 1500);
  }

  async function handleSearch(q) {
    setSearch(q);
    if (q.trim().length < 2) {
      setResults([]);
      return;
    }
    try {
      const res = await searchUsers(q);
      setResults(res.data);
    } catch (err) {
      setError(err.message);
    }
  }

  async function handleStartChat(otherUser) {
    try {
      const res = await startConversation(otherUser.id);
      await loadConversations();
      await openConversation({ id: res.data.conversation.id, otherUser });
    } catch (err) {
      setError(err.message);
    }
  }

  return (
    <div className="py-10 max-w-[1100px] mx-auto px-6">
      <div className="flex items-center justify-between mb-6">
        <div>
          <div className="eyebrow">KWEGEREZA CHAT</div>
          <h1 className="font-display text-[30px] font-bold text-green-950">Ubutumwa</h1>
        </div>
        <div className="flex items-center gap-2 text-xs font-bold">
          <Circle size={9} className={connected ? "text-emerald-500 fill-emerald-500" : "text-gray-300 fill-gray-300"} />
          {connected ? "Uhuze" : "Ntabwo uhuye"}
        </div>
      </div>

      {error && (
        <div className="bg-red-50 border border-red-200 text-red-700 text-sm rounded-xl px-4 py-3 mb-4">
          {error}
        </div>
      )}

      <div className="grid grid-cols-1 md:grid-cols-[300px_1fr] gap-5 h-[560px]">
        {/* Conversation list + search */}
        <div className="card !p-0 flex flex-col overflow-hidden">
          <div className="p-3 border-b border-line">
            <div className="flex items-center gap-2 bg-cream-2 rounded-full px-3.5 py-2">
              <Search size={14} className="text-ink-soft flex-none" />
              <input
                value={search}
                onChange={(e) => handleSearch(e.target.value)}
                placeholder="Shakisha umuntu..."
                className="w-full text-sm bg-transparent outline-none"
              />
            </div>
            {results.length > 0 && (
              <div className="mt-2 border border-line rounded-xl overflow-hidden">
                {results.map((r) => (
                  <button
                    key={r.id}
                    onClick={() => handleStartChat(r)}
                    className="w-full text-left px-3 py-2.5 text-sm hover:bg-cream-2 flex flex-col"
                  >
                    <span className="font-semibold text-green-950">{r.fullName}</span>
                    <span className="text-xs text-ink-soft">{r.role}</span>
                  </button>
                ))}
              </div>
            )}
          </div>
          <div className="flex-1 overflow-y-auto">
            {conversations.length === 0 && (
              <div className="p-6 text-center text-ink-soft text-sm">
                Nta kiganiro ufite. Shakisha umuntu hejuru utangire.
              </div>
            )}
            {conversations.map((c) => (
              <button
                key={c.id}
                onClick={() => openConversation(c)}
                className={`w-full text-left px-4 py-3 border-b border-line flex items-center gap-3 hover:bg-cream-2 ${
                  activeId === c.id ? "bg-cream-2" : ""
                }`}
              >
                <div className="relative flex-none">
                  <div className="w-10 h-10 rounded-full bg-gradient-to-br from-green-700 to-green-950 text-white flex items-center justify-center font-display font-bold text-sm">
                    {c.otherUser?.fullName?.[0] ?? "?"}
                  </div>
                  {onlineIds.has(c.otherUser?.id) && (
                    <span className="absolute -bottom-0.5 -right-0.5 w-3 h-3 rounded-full bg-emerald-500 border-2 border-white" />
                  )}
                </div>
                <div className="min-w-0">
                  <div className="font-semibold text-sm text-green-950 truncate">{c.otherUser?.fullName}</div>
                  <div className="text-xs text-ink-soft truncate">
                    {c.lastMessage?.body ?? "Ntanubutumwa"}
                  </div>
                </div>
              </button>
            ))}
          </div>
        </div>

        {/* Thread */}
        <div className="card !p-0 flex flex-col overflow-hidden">
          {!activeId ? (
            <div className="flex-1 flex items-center justify-center text-ink-soft text-sm">
              Hitamo umuntu wo kuganira na we.
            </div>
          ) : (
            <>
              <div className="px-5 py-4 border-b border-line flex items-center gap-3">
                <div className="w-9 h-9 rounded-full bg-gradient-to-br from-green-700 to-green-950 text-white flex items-center justify-center font-display font-bold text-sm">
                  {activeOther?.fullName?.[0] ?? "?"}
                </div>
                <div>
                  <div className="font-semibold text-sm text-green-950">{activeOther?.fullName}</div>
                  <div className="text-xs text-ink-soft">
                    {onlineIds.has(activeOther?.id) ? "Ari kuri interineti" : "Ntari kuri interineti"}
                  </div>
                </div>
              </div>

              <div className="flex-1 overflow-y-auto px-5 py-4 space-y-3">
                {messages.map((m) => {
                  const mine = m.senderId === user.id;
                  return (
                    <div key={m.id} className={`flex ${mine ? "justify-end" : "justify-start"}`}>
                      <div
                        className={`max-w-[70%] rounded-2xl px-4 py-2.5 text-sm ${
                          mine ? "bg-green-950 text-white rounded-br-sm" : "bg-cream-2 text-ink rounded-bl-sm"
                        }`}
                      >
                        <div>{m.deleted ? <em className="opacity-60">Ubutumwa bwasibwe</em> : m.body}</div>
                        <div className={`text-[10px] mt-1 ${mine ? "text-white/60" : "text-ink-soft"}`}>
                          {formatTime(m.createdAt)}
                        </div>
                      </div>
                    </div>
                  );
                })}
                {typingUser && (
                  <div className="text-xs text-ink-soft italic">{typingUser} arimo kwandika...</div>
                )}
                <div ref={bottomRef} />
              </div>

              <form onSubmit={handleSend} className="p-3 border-t border-line flex gap-2">
                <input
                  value={draft}
                  onChange={handleDraftChange}
                  placeholder="Andika ubutumwa..."
                  className="flex-1 rounded-full border border-line px-4 py-2.5 text-sm outline-none focus:ring-2 focus:ring-gold-400"
                />
                <button type="submit" className="btn btn-gold !px-4 !py-2.5">
                  <Send size={16} />
                </button>
              </form>
            </>
          )}
        </div>
      </div>
    </div>
  );
}
