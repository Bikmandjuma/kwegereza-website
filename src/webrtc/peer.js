// Thin wrapper around RTCPeerConnection so the component code doesn't have to
// repeat ICE-server config, track handling, and cleanup every time it opens
// a connection. Uses a public STUN server only — for production, a TURN
// server should be added here for users behind restrictive NATs, and this is
// the single place that would need updating.
const ICE_SERVERS = [{ urls: "stun:stun.l.google.com:19302" }];

export function createPeerConnection({ onIceCandidate, onTrack }) {
  const pc = new RTCPeerConnection({ iceServers: ICE_SERVERS });

  pc.onicecandidate = (event) => {
    if (event.candidate) onIceCandidate(event.candidate);
  };
  pc.ontrack = (event) => {
    onTrack(event.streams[0]);
  };

  return pc;
}

export async function getMicrophoneStream() {
  return navigator.mediaDevices.getUserMedia({ audio: true, video: false });
}

// Real camera capture — browser device-permission prompt included. Never
// called automatically; only in direct response to the person clicking
// "enable camera" themselves, same discipline as the microphone helper.
export async function getCameraStream() {
  return navigator.mediaDevices.getUserMedia({ audio: false, video: { width: 640, height: 480 } });
}

// Real screen capture via the browser's own share-picker UI — the person
// explicitly chooses what to share; this code never has access to anything
// beyond what they picked.
export async function getScreenShareStream() {
  return navigator.mediaDevices.getDisplayMedia({ video: true, audio: false });
}

export function closePeerConnection(pc) {
  if (!pc) return;
  pc.onicecandidate = null;
  pc.ontrack = null;
  pc.getSenders().forEach((sender) => sender.track?.stop());
  pc.close();
}

/**
 * Lightweight WebRTC connection-quality watcher. Polls getStats() every
 * couple seconds and reports "good" | "poor" | "disconnected" based on
 * packet loss and round-trip time — drives the "Poor connection" /
 * "Disconnected" states from the spec instead of only tracking whether the
 * socket itself is up.
 */
export function watchConnectionQuality(pc, onChange) {
  let cancelled = false;
  let lastState = "good";

  async function tick() {
    if (cancelled || !pc) return;
    if (pc.connectionState === "disconnected" || pc.connectionState === "failed" || pc.connectionState === "closed") {
      if (lastState !== "disconnected") {
        lastState = "disconnected";
        onChange("disconnected");
      }
    } else {
      try {
        const stats = await pc.getStats();
        let packetsLost = 0;
        let packetsReceived = 0;
        let rtt = 0;
        stats.forEach((report) => {
          if (report.type === "inbound-rtp" && report.kind === "audio") {
            packetsLost += report.packetsLost ?? 0;
            packetsReceived += report.packetsReceived ?? 0;
          }
          if (report.type === "candidate-pair" && report.state === "succeeded" && report.currentRoundTripTime) {
            rtt = Math.max(rtt, report.currentRoundTripTime);
          }
        });
        const lossRatio = packetsReceived > 0 ? packetsLost / (packetsLost + packetsReceived) : 0;
        const next = lossRatio > 0.08 || rtt > 0.4 ? "poor" : "good";
        if (next !== lastState) {
          lastState = next;
          onChange(next);
        }
      } catch {
        // getStats can throw briefly right after a peer connection closes —
        // harmless, just skip this tick.
      }
    }
    if (!cancelled) setTimeout(tick, 2500);
  }

  tick();
  return () => {
    cancelled = true;
  };
}

/**
 * Web Audio-based active-speaker detector. Attaches an AnalyserNode to a
 * MediaStream and calls onSpeaking(true/false) as volume crosses a simple
 * threshold — drives the "Speaking" indicator from the spec.
 */
export function watchActiveSpeaker(stream, onSpeaking, threshold = 12) {
  if (!stream || stream.getAudioTracks().length === 0) return () => {};
  const AudioCtx = window.AudioContext || window.webkitAudioContext;
  if (!AudioCtx) return () => {};

  const ctx = new AudioCtx();
  const source = ctx.createMediaStreamSource(stream);
  const analyser = ctx.createAnalyser();
  analyser.fftSize = 512;
  source.connect(analyser);

  const data = new Uint8Array(analyser.frequencyBinCount);
  let speaking = false;
  let rafId;

  function tick() {
    analyser.getByteFrequencyData(data);
    const avg = data.reduce((sum, v) => sum + v, 0) / data.length;
    const isSpeaking = avg > threshold;
    if (isSpeaking !== speaking) {
      speaking = isSpeaking;
      onSpeaking(speaking);
    }
    rafId = requestAnimationFrame(tick);
  }
  tick();

  return () => {
    cancelAnimationFrame(rafId);
    source.disconnect();
    analyser.disconnect();
    ctx.close().catch(() => {});
  };
}
