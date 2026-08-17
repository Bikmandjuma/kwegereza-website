import Echo from 'laravel-echo'
import Pusher from 'pusher-js'

let echoInstance = null

/**
 * Lazily creates the Echo connection. Realtime is treated as an
 * enhancement, not a hard dependency — NotificationBell keeps its
 * 30-second poll running regardless, so if the WebSocket server is down
 * (or misconfigured in a given environment) the user still gets updates,
 * just not instantly. This mirrors the server-side fix from this same
 * phase, where a broadcast failure was made non-fatal to ticket creation.
 *
 * Switched from Pusher (a third-party hosted service) to Laravel Reverb —
 * the spec calls for Reverb specifically ("Do not introduce a separate
 * Node backend"), and Reverb speaks the same wire protocol Pusher does,
 * so laravel-echo/pusher-js work completely unchanged here; only the
 * target host/port and the `broadcaster` name change.
 */
export function getEcho() {
  if (echoInstance) return echoInstance

  const key = import.meta.env.VITE_REVERB_APP_KEY
  if (!key) return null // broadcasting not configured for this environment

  window.Pusher = Pusher
  echoInstance = new Echo({
    broadcaster: 'reverb',
    key,
    wsHost: import.meta.env.VITE_REVERB_HOST || window.location.hostname,
    wsPort: Number(import.meta.env.VITE_REVERB_PORT || 8080),
    wssPort: Number(import.meta.env.VITE_REVERB_PORT || 8080),
    forceTLS: (import.meta.env.VITE_REVERB_SCHEME || 'http') === 'https',
    enabledTransports: ['ws', 'wss'],
    // Same-origin now — no need to derive this by string-surgery off
    // the API base URL; the owner broadcasting auth route is a fixed,
    // known path (see routes/api.php).
    authEndpoint: '/api/broadcasting/auth',
    auth: {
      headers: {
        Authorization: `Bearer ${localStorage.getItem('kwegereza_token') || ''}`,
        Accept: 'application/json',
      },
    },
  })

  return echoInstance
}

export function disconnectEcho() {
  if (echoInstance) {
    echoInstance.disconnect()
    echoInstance = null
  }
}
