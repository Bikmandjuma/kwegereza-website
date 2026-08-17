import { useCallback, useEffect, useRef, useState } from 'react'
import { getEcho } from '../services/echo'
import * as liveClassApi from '../features/liveclass/liveClassApi'

/**
 * A genuine WebRTC mesh: each participant opens one RTCPeerConnection per
 * other participant, and audio/video flows directly peer-to-peer once
 * negotiated — this server (and this app) never touches the media itself,
 * only the signaling handshake (SDP offers/answers, ICE candidates),
 * relayed over the private live-class.{id} broadcast channel via
 * LiveClassSignal. A mesh is the right tradeoff for a small class (a dozen
 * or so participants); it wouldn't scale to hundreds without an SFU, which
 * is real, separate infrastructure beyond this phase's scope.
 *
 * Honest limitation: this code is written to the real WebRTC spec and
 * exercised against real signaling events, but actual two-browser
 * audio/video was not verifiable in the sandbox this was built in (no
 * real browser/camera available) — see the phase report.
 */
export function useLiveClassWebRTC({ classId, myKey, isOwnerSide }) {
  const [localStream, setLocalStream] = useState(null)
  const [remoteStreams, setRemoteStreams] = useState({}) // key -> MediaStream
  const [micEnabled, setMicEnabled] = useState(false)
  const [camEnabled, setCamEnabled] = useState(false)
  const [screenSharing, setScreenSharing] = useState(false)
  const peersRef = useRef({}) // key -> RTCPeerConnection
  const localStreamRef = useRef(null)
  const screenStreamRef = useRef(null)
  const cameraTrackRef = useRef(null) // kept aside while screen-sharing, restored on stop

  const iceServers = [{ urls: 'stun:stun.l.google.com:19302' }]

  const sendSignal = useCallback(
    (to, signal) => liveClassApi.sendSignal(classId, to, signal),
    [classId]
  )

  const createPeerConnection = useCallback((peerKey) => {
    const pc = new RTCPeerConnection({ iceServers })

    pc.onicecandidate = (e) => {
      if (e.candidate) sendSignal(peerKey, { type: 'ice-candidate', candidate: e.candidate })
    }

    pc.ontrack = (e) => {
      setRemoteStreams((prev) => ({ ...prev, [peerKey]: e.streams[0] }))
    }

    if (localStreamRef.current) {
      localStreamRef.current.getTracks().forEach((track) => pc.addTrack(track, localStreamRef.current))
    }

    peersRef.current[peerKey] = pc
    return pc
  }, [sendSignal]) // eslint-disable-line react-hooks/exhaustive-deps

  const initiateCallTo = useCallback(async (peerKey) => {
    const pc = createPeerConnection(peerKey)
    const offer = await pc.createOffer()
    await pc.setLocalDescription(offer)
    sendSignal(peerKey, { type: 'offer', sdp: offer })
  }, [createPeerConnection, sendSignal])

  const handleSignal = useCallback(async (payload) => {
    if (payload.to !== myKey) return // not for us
    const peerKey = payload.from
    const { signal } = payload

    let pc = peersRef.current[peerKey]

    if (signal.type === 'offer') {
      pc = pc || createPeerConnection(peerKey)
      await pc.setRemoteDescription(new RTCSessionDescription(signal.sdp))
      const answer = await pc.createAnswer()
      await pc.setLocalDescription(answer)
      sendSignal(peerKey, { type: 'answer', sdp: answer })
    } else if (signal.type === 'answer' && pc) {
      await pc.setRemoteDescription(new RTCSessionDescription(signal.sdp))
    } else if (signal.type === 'ice-candidate' && pc) {
      try {
        await pc.addIceCandidate(new RTCIceCandidate(signal.candidate))
      } catch { /* ignore late candidates */ }
    }
  }, [myKey, createPeerConnection, sendSignal])

  useEffect(() => {
    const echo = getEcho()
    if (!echo) return
    const channel = isOwnerSide ? echo.private(`live-class.${classId}`) : echo.private(`live-class.${classId}`)
    channel.listen('.signal', handleSignal)
    return () => echo.leave(`live-class.${classId}`)
  }, [classId, isOwnerSide, handleSignal])

  const enableMic = useCallback(async () => {
    try {
      const stream = await navigator.mediaDevices.getUserMedia({ audio: true, video: false })
      if (localStreamRef.current) {
        stream.getAudioTracks().forEach((t) => localStreamRef.current.addTrack(t))
      } else {
        localStreamRef.current = stream
        setLocalStream(stream)
      }
      setMicEnabled(true)
      stream.getAudioTracks().forEach((track) => {
        Object.values(peersRef.current).forEach((pc) => pc.addTrack(track, localStreamRef.current))
      })
    } catch (e) {
      console.error('getUserMedia (audio) failed', e)
    }
  }, [])

  const disableMic = useCallback(() => {
    localStreamRef.current?.getAudioTracks().forEach((t) => {
      t.stop()
      localStreamRef.current.removeTrack(t)
    })
    setMicEnabled(false)
    if (!localStreamRef.current?.getTracks().length) {
      localStreamRef.current = null
      setLocalStream(null)
    }
  }, [])

  /** Camera — same on/off pattern as the mic, kept as a separate track
   * so either can be toggled independently without tearing down the
   * other, matching how every real video-call app behaves. */
  const enableCamera = useCallback(async () => {
    try {
      const stream = await navigator.mediaDevices.getUserMedia({ audio: false, video: true })
      const [videoTrack] = stream.getVideoTracks()
      cameraTrackRef.current = videoTrack
      if (localStreamRef.current) {
        localStreamRef.current.addTrack(videoTrack)
      } else {
        localStreamRef.current = stream
        setLocalStream(stream)
      }
      setCamEnabled(true)
      Object.values(peersRef.current).forEach((pc) => pc.addTrack(videoTrack, localStreamRef.current))
      setLocalStream(localStreamRef.current) // trigger re-render so <video> picks up the new track
    } catch (e) {
      console.error('getUserMedia (video) failed', e)
    }
  }, [])

  const disableCamera = useCallback(() => {
    if (cameraTrackRef.current) {
      cameraTrackRef.current.stop()
      localStreamRef.current?.removeTrack(cameraTrackRef.current)
      cameraTrackRef.current = null
    }
    setCamEnabled(false)
    if (!localStreamRef.current?.getTracks().length) {
      localStreamRef.current = null
      setLocalStream(null)
    } else {
      setLocalStream(localStreamRef.current)
    }
  }, [])

  /** Screen share — replaces the outgoing video track on every existing
   * peer connection (RTCRtpSender.replaceTrack) rather than renegotiating
   * each connection from scratch, and automatically restores the camera
   * track (if one was active) when the browser's own "Stop sharing" UI
   * ends the screen stream. */
  const startScreenShare = useCallback(async () => {
    try {
      const stream = await navigator.mediaDevices.getDisplayMedia({ video: true, audio: false })
      const [screenTrack] = stream.getVideoTracks()
      screenStreamRef.current = stream

      Object.values(peersRef.current).forEach((pc) => {
        const sender = pc.getSenders().find((s) => s.track && s.track.kind === 'video')
        if (sender) sender.replaceTrack(screenTrack)
        else pc.addTrack(screenTrack, stream)
      })

      screenTrack.onended = () => stopScreenShare()
      setScreenSharing(true)
    } catch (e) {
      console.error('getDisplayMedia failed', e)
    }
  }, []) // eslint-disable-line react-hooks/exhaustive-deps

  const stopScreenShare = useCallback(() => {
    screenStreamRef.current?.getTracks().forEach((t) => t.stop())
    screenStreamRef.current = null

    const restoreTrack = cameraTrackRef.current
    Object.values(peersRef.current).forEach((pc) => {
      const sender = pc.getSenders().find((s) => s.track && s.track.kind === 'video')
      if (sender) sender.replaceTrack(restoreTrack || null)
    })
    setScreenSharing(false)
  }, [])

  const cleanup = useCallback(() => {
    Object.values(peersRef.current).forEach((pc) => pc.close())
    peersRef.current = {}
    disableMic()
    disableCamera()
    stopScreenShare()
  }, [disableMic, disableCamera, stopScreenShare])

  useEffect(() => () => cleanup(), [cleanup])

  return {
    localStream, remoteStreams, micEnabled, enableMic, disableMic,
    camEnabled, enableCamera, disableCamera,
    screenSharing, startScreenShare, stopScreenShare,
    initiateCallTo, cleanup,
  }
}
