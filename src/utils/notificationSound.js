const STORAGE_KEY = "kiu_notif_sound";

export function isNotificationSoundEnabled() {
  return localStorage.getItem(STORAGE_KEY) !== "off"; // default: on
}

export function setNotificationSoundEnabled(enabled) {
  localStorage.setItem(STORAGE_KEY, enabled ? "on" : "off");
}

/**
 * A short, synthesized two-tone chime — no audio asset to ship or fail to
 * load. Respects the stored preference and, implicitly, the browser's own
 * autoplay/user-gesture restrictions (a rejected play() promise is simply
 * swallowed, same as the spec asks: never try to bypass browser security).
 */
export function playNotificationSound() {
  if (!isNotificationSoundEnabled()) return;
  const AudioCtx = window.AudioContext || window.webkitAudioContext;
  if (!AudioCtx) return;

  try {
    const ctx = new AudioCtx();
    const now = ctx.currentTime;
    [880, 1175].forEach((freq, i) => {
      const osc = ctx.createOscillator();
      const gain = ctx.createGain();
      osc.frequency.value = freq;
      osc.type = "sine";
      gain.gain.setValueAtTime(0.0001, now + i * 0.12);
      gain.gain.exponentialRampToValueAtTime(0.15, now + i * 0.12 + 0.02);
      gain.gain.exponentialRampToValueAtTime(0.0001, now + i * 0.12 + 0.18);
      osc.connect(gain).connect(ctx.destination);
      osc.start(now + i * 0.12);
      osc.stop(now + i * 0.12 + 0.2);
    });
    setTimeout(() => ctx.close().catch(() => {}), 500);
  } catch {
    // Autoplay was blocked, or audio isn't available right now — never
    // throw over a "nice to have" sound.
  }
}
