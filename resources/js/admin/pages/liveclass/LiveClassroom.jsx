import { useEffect, useRef, useState, useCallback } from 'react'
import { useParams, useNavigate } from 'react-router-dom'
import * as liveClassApi from '../../features/liveclass/liveClassApi'
import { useLiveClassWebRTC } from '../../hooks/useLiveClassWebRTC'
import { getEcho } from '../../services/echo'
import { useAuth } from '../../contexts/AuthContext'
import { Button, Card, Badge } from '../../components/ui'
import { useToast } from '../../contexts/ToastContext'

export default function LiveClassroom() {
  const { id } = useParams()
  const navigate = useNavigate()
  const { user, hasPermission } = useAuth()
  const { push } = useToast()
  const myKey = `owner:${user?.id}`

  const [participants, setParticipants] = useState([])
  const [liveClass, setLiveClass] = useState(null)
  const localVideoRef = useRef(null)

  const {
    localStream, remoteStreams, micEnabled, enableMic, disableMic,
    camEnabled, enableCamera, disableCamera,
    screenSharing, startScreenShare, stopScreenShare,
    initiateCallTo,
  } = useLiveClassWebRTC({
    classId: id, myKey, isOwnerSide: true,
  })

  // Was completely missing — this component never fetched the class
  // itself, only its participant list, so it had no way to know whether
  // the logged-in leader was actually the host before rendering Mute
  // All / End Class / per-participant mute+remove. Any leader with the
  // broad live_class.manage permission who reached this page (whether
  // via the list — now fixed — or a direct URL) saw fully clickable
  // host controls for a class that wasn't theirs, and got a raw 403
  // from the backend's own (correct) host check the moment they clicked.
  useEffect(() => {
    liveClassApi.getClass(id).then(setLiveClass).catch(() => {})
  }, [id])

  const isHost = liveClass?.host?.id === user?.id
  const canModerate = isHost || hasPermission('live_class.moderate')

  const loadParticipants = useCallback(() => {
    liveClassApi.getParticipants(id).then((data) => {
      setParticipants(data)
      data.filter((p) => p.key !== myKey).forEach((p) => initiateCallTo(p.key))
    }).catch(() => {})
  }, [id, myKey, initiateCallTo])

  useEffect(() => { loadParticipants() }, [loadParticipants])

  useEffect(() => {
    const echo = getEcho()
    if (!echo) return
    const channel = echo.private(`live-class.${id}`)
    channel.listen('.state-changed', (payload) => {
      if (['participant-joined', 'participant-left', 'participant-removed', 'hand-raised', 'hand-approved', 'hand-rejected', 'participant-muted', 'muted-everyone'].includes(payload.type)) {
        loadParticipants()
      }
      if (payload.type === 'class-ended') {
        push('Isomo ryarangiye.')
        navigate('/live-classes')
      }
    })
    return () => echo.leave(`live-class.${id}`)
  }, [id, loadParticipants, navigate, push])

  useEffect(() => {
    if (localVideoRef.current && localStream) localVideoRef.current.srcObject = localStream
  }, [localStream])

  async function handleApprove(participantId) {
    await liveClassApi.approveHand(id, participantId)
  }
  async function handleReject(participantId) {
    await liveClassApi.rejectHand(id, participantId)
  }
  async function handleMute(participantId) {
    await liveClassApi.muteParticipant(id, participantId)
  }
  async function handleMuteAll() {
    await liveClassApi.muteEveryone(id)
    push('Bose bahagaritswe ijwi.')
  }
  async function handleRemove(participantId) {
    if (!confirm('Wemeza gukura uyu muntu?')) return
    await liveClassApi.removeParticipant(id, participantId)
  }
  async function handleEnd() {
    if (!confirm('Wemeza gusoza iri somo?')) return
    await liveClassApi.endClass(id)
    navigate('/live-classes')
  }

  const raisedHands = participants.filter((p) => p.hand_raised)

  return (
    <div className="flex h-[calc(100vh-8rem)] gap-4">
      <div className="flex flex-1 flex-col gap-4">
        <div className="grid flex-1 grid-cols-2 gap-3 overflow-y-auto sm:grid-cols-3">
          <div className="relative rounded-xl2 bg-ink">
            <video ref={localVideoRef} autoPlay muted playsInline className="h-full w-full rounded-xl2 object-cover" />
            <span className="absolute bottom-2 left-2 rounded bg-black/50 px-2 py-0.5 text-xs text-white">Njye (Umuyobozi)</span>
          </div>
          {Object.entries(remoteStreams).map(([key, stream]) => (
            <RemoteVideo key={key} stream={stream} label={participants.find((p) => p.key === key)?.name || key} />
          ))}
        </div>

        <div className="flex flex-wrap items-center gap-3">
          <Button onClick={micEnabled ? disableMic : enableMic}>{micEnabled ? 'Hagarika ijwi' : 'Fungura ijwi'}</Button>
          <Button variant="secondary" onClick={camEnabled ? disableCamera : enableCamera}>
            {camEnabled ? 'Hagarika kamera' : 'Fungura kamera'}
          </Button>
          <Button variant="secondary" onClick={screenSharing ? stopScreenShare : startScreenShare}>
            {screenSharing ? 'Hagarika gusangira ecran' : 'Sangira ecran (Screen Share)'}
          </Button>
          {canModerate && <Button variant="secondary" onClick={handleMuteAll}>Hagarika bose</Button>}
          {canModerate && <Button variant="danger" onClick={handleEnd}>Soza isomo</Button>}
        </div>
      </div>

      <Card className="w-72 flex-shrink-0 overflow-y-auto p-4">
        <h2 className="mb-3 font-display text-lg text-ink dark:text-sand-50">Abari mu isomo ({participants.length})</h2>

        {canModerate && raisedHands.length > 0 && (
          <div className="mb-4 rounded-lg bg-gold-300/20 p-3">
            <p className="mb-2 text-xs font-medium uppercase text-gold-600">Bazamuye ukuboko</p>
            {raisedHands.map((p) => (
              <div key={p.key} className="mb-2 flex items-center justify-between text-sm">
                <span>{p.name}</span>
                <div className="flex gap-2">
                  <button onClick={() => handleApprove(p.key.split(':')[1])} className="text-teal-700 hover:underline dark:text-teal-300">Emeza</button>
                  <button onClick={() => handleReject(p.key.split(':')[1])} className="text-rose-600 hover:underline dark:text-rose-400">Anga</button>
                </div>
              </div>
            ))}
          </div>
        )}

        <ul className="space-y-2 text-sm">
          {participants.map((p) => (
            <li key={p.key} className="flex items-center justify-between">
              <span className="flex items-center gap-1.5">
                {p.name}
                {p.role === 'host' && <Badge tone="success">Umuyobozi</Badge>}
                {p.role === 'speaker' && <Badge tone="neutral">Avuga</Badge>}
              </span>
              {canModerate && p.role !== 'host' && (
                <div className="flex gap-2 text-xs">
                  {!p.is_muted && <button onClick={() => handleMute(p.key.split(':')[1])} className="text-ink/50 hover:text-ink">Hagarika</button>}
                  <button onClick={() => handleRemove(p.key.split(':')[1])} className="text-rose-600 hover:underline dark:text-rose-400">Kura</button>
                </div>
              )}
            </li>
          ))}
        </ul>
      </Card>
    </div>
  )
}

function RemoteVideo({ stream, label }) {
  const ref = useRef(null)
  useEffect(() => {
    if (ref.current) ref.current.srcObject = stream
  }, [stream])
  return (
    <div className="relative rounded-xl2 bg-ink">
      <video ref={ref} autoPlay playsInline className="h-full w-full rounded-xl2 object-cover" />
      <span className="absolute bottom-2 left-2 rounded bg-black/50 px-2 py-0.5 text-xs text-white">{label}</span>
    </div>
  )
}
