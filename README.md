# Kwegereza Islam Umuryango — Public Site + Auth

React + Vite + Tailwind + React Router, wired to the real `kwegereza-backend`
API (not mocked). Run both projects together — see below.

## Run locally (needs the backend running on :4000 first)

```bash
npm install
cp .env.example .env    # VITE_API_URL, already points at localhost:4000/api
npm run dev              # http://localhost:5173
```

## Build for production
```bash
npm run build    # outputs to dist/
npm run preview  # preview the production build
```

## Routes
- `/`                    Home (hero + previews)
- `/about`               About Us
- `/abarimu`             Teachers
- `/inyandiko`           Articles / Ifaida
- `/amatangazo`          Announcements
- `/ibitabo`             Books library
- `/login`               Login (real backend auth)
- `/register`            Registration (creates a PENDING student)
- `/pending`             Waiting-for-approval screen
- `/chat`                Kwegereza Chat — real-time messaging (protected, any active user)
- `/live-class`          Live audio classroom — lobby + real WebRTC (protected)
- `/leader/abanyeshuri`  Leader/Admin-only approval queue (protected route)

## Ifaida content management implementation notes
- `src/components/RichTextEditor.jsx` — a genuinely functional editor
  (contentEditable + document.execCommand), every toolbar button real:
  bold/italic/underline/strikethrough, H1/H2/H3/paragraph, bullet/numbered
  lists, blockquote, link, alignment, text color, highlight, horizontal
  rule, undo/redo, clear formatting. Honest note: execCommand is a legacy
  API — still functional in every major browser, but a maintained
  framework (TipTap/ProseMirror) would be the right long-term upgrade.
- `src/pages/leader/IfaidaEditorPage.jsx` — debounced autosave (1.5s after
  typing stops, never on every keystroke), showing "Bika..." / "Bikawe ✓"
  exactly as the spec asks; a Preview toggle that renders the post in the
  exact same styling students will see; Publish/Unpublish only shown if the
  user actually has `ifaida.publish`.
- `src/pages/InyandikoPage.jsx` + `src/pages/IfaidaDetailPage.jsx` — the
  public reading experience, now backed by real published posts instead of
  static demo data. An honest empty state shows when nothing's published yet
  — no fake placeholder articles.
- `.prose-editor` styling lives in global `index.css` so the editor, its
  preview, and the public reading page all render identically.
- **Deferred, disclosed scope**: cover images are a plain URL field (no file
  upload / object storage yet — that's a production-hardening item); bookmark
  and reading-progress tracking from the spec's "Student Ifaida" section
  aren't built — they're genuinely separate systems needing their own
  data model, not wired up to avoid quietly overpromising this pass.

## RBAC & role/permission assignment implementation notes
- `src/components/admin/AdminSidebar.jsx` — every nav item declares
  `requires: "permission.key"` or `adminOnly: true` or nothing; an item the
  user can't access is filtered out entirely before render, never just
  disabled. Empty sections don't even show their header.
- `src/components/ProtectedRoute.jsx` — now accepts a `permission` prop in
  addition to `roles`, so navigating directly to a URL is blocked the same
  way the sidebar link is hidden — not just a UX nicety, an actual route gate
  (though the backend remains the real authority regardless).
- `src/pages/leader/UserManagementPage.jsx` + `PermissionEditorModal.jsx` —
  the admin-only "Abakoresha" page: promote/demote STUDENT ⇄ LEADER, toggle
  any of the 34 permissions grouped by category, block/unblock any non-admin
  account.
- `src/context/NotificationContext.jsx` — now also listens for
  `account:updated` and silently calls `refreshUser()`, so a permission
  change made by an admin shows up in the affected user's sidebar within
  seconds, without them needing to log out and back in.

## Analytics implementation notes
- `src/hooks/useActivityTracking.js` — fires PAGE_VIEW on real navigation
  changes and a heartbeat every 30s, only while logged in. This is the only
  place either of those calls happens.
- `src/pages/leader/AnalyticsDashboardPage.jsx` — range selector (daily/
  weekly/monthly/yearly/lifetime, default weekly) + real stat cards + an
  event-type breakdown. No charting library added — simple CSS bars, kept
  deliberately lean.
- **Honest note**: only login sessions, chat opens, book downloads, and
  live-class attendance are tracked, because those are the only things with
  real backing data right now. Video/audio/exam analytics from the spec need
  Phase 6 (real Dars/Ifaida/Books/Exams content models) to exist first —
  see the backend README.

## Notification system implementation notes
- `src/context/NotificationContext.jsx` — single source of truth for the bell
  badge and the top banner; opens its own realtime connection (cleaned up on
  unmount) and listens for `notification:new`.
- `src/components/NotificationBell.jsx` — unread badge + dropdown + the
  "Emeza ubutumwa bwa push" prompt (only shown if the browser hasn't granted
  permission yet — never auto-requested without a real button click).
- `src/components/TopBanner.jsx` — the ambient sliding banner from the spec;
  auto-dismisses after 7s, click navigates to the notification's URL.
- `src/utils/push.js` + `public/sw.js` — real Service Worker registration and
  Push API subscription, not mocked.
- **Honest note**: exactly like the live-class audio, actual push delivery to
  a real device needs a human with a real browser to confirm. I've verified
  the subscription is stored/removed correctly and that the server actually
  attempts a signed push send — see the backend README for what's tested.

## Live classroom implementation notes
- `src/hooks/useLiveClass.js` — same cleanup discipline as the chat hook.
- `src/webrtc/peer.js` — thin RTCPeerConnection wrapper (STUN server, track
  handling, teardown). Real browser WebRTC APIs, not mocked.
- Star topology: the host's audio is offered to every participant
  ("broadcast" connection); a participant only gets a second, separate
  ("uplink") connection once the host explicitly approves them to speak, and
  only after they themselves click "Fungura Mikoro" — no code path ever
  turns on a participant's microphone without that explicit click.
- **Honest note**: the signaling (who-can-do-what, join/mute/approve/end) is
  live-tested against the real server — see the backend README. Actual audio
  transmission needs a real browser + microphone to confirm end-to-end,
  which I have not been able to do myself in this environment. Please try it
  with two browser tabs and let me know how it goes.

## Chat implementation notes
- `src/hooks/useChatSocket.js` — the single realtime hook every chat UI goes
  through. Every `socket.on()` here has a matching `socket.off()` in cleanup,
  which is what stops duplicate listeners (and therefore duplicate rendered
  messages) after remounts, navigation, or React StrictMode's dev double-invoke.
- Messages are never optimistically inserted client-side — the server always
  echoes `message:new` back to the sender too (senders join their own
  conversation room), so there is exactly one code path that ever adds a
  message bubble to the screen.

## Structure
- `src/api/` — `client.js` (fetch wrapper, unwraps `{success,data,message,meta}`),
  `auth.js`, `students.js`
- `src/context/AuthContext.jsx` — session state; re-validates against the
  backend on every app load rather than trusting localStorage
- `src/components/ProtectedRoute.jsx` — frontend RBAC convenience only; the
  backend is always the real gate
- `src/components/` — Topbar (session-aware), Navbar, Footer, Hero, ChatFab
- `src/layouts/PublicLayout.jsx` — shared shell
- `src/pages/` — one file per route, including `leader/ApprovalCenterPage.jsx`
- `src/data/` — teachers.js, articles.js, announcements.js, books.js (still
  static — becomes real API calls in a later content-management phase)

## Verified together

Backend + frontend were booted side-by-side and tested end-to-end: frontend
served correctly, CORS preflight passed for the frontend's exact origin, and
a real registration request from that origin landed in the database as
`PENDING`. Full auth lifecycle (register → pending → approve → login →
block → session killed) is tested on the backend — see its README.

## Next phases (not yet built — see chat for the full roadmap)
Realtime chat, live audio classrooms (WebRTC), push notifications, RBAC-driven
content management (Ifaida/Dars/Books as real CRUD, not static data), analytics.

