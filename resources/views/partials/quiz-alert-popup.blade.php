<style>
#kiuQuizAlertModal { animation: kiuAlertFadeIn .25s ease-out; }
#kiuQuizAlertModal .kiu-alert-card { animation: kiuAlertPopIn .3s var(--ease-spring, cubic-bezier(0.16,1,0.3,1)) both; }
@keyframes kiuAlertFadeIn { from { opacity: 0; } to { opacity: 1; } }
@keyframes kiuAlertPopIn { from { opacity: 0; transform: scale(0.94) translateY(8px); } to { opacity: 1; transform: scale(1) translateY(0); } }
.kiu-alert-dismiss { transition: transform .2s var(--ease-spring, cubic-bezier(0.16,1,0.3,1)), filter .2s; }
.kiu-alert-dismiss:hover { filter: brightness(1.08); transform: translateY(-1px); }
.kiu-alert-dismiss:focus-visible { outline: 2px solid var(--green, #0B6D20); outline-offset: 2px; }
.kiu-alert-close { transition: opacity .2s; }
.kiu-alert-close:hover { opacity: .7; }
</style>

<div id="kiuQuizAlertModal" style="display:none; position:fixed; inset:0; z-index:9999; align-items:center; justify-content:center; padding:16px; background:rgba(9,73,57,.55);">
  <div class="kiu-alert-card" style="width:100%; max-width:380px; background:#fff; border-radius:24px; box-shadow:0 20px 60px rgba(0,0,0,.3); overflow:hidden;">
    <div style="background:var(--green-dark, #094939); padding:20px 24px; color:#fff;">
      <p style="font-size:11px; text-transform:uppercase; letter-spacing:.05em; opacity:.8; margin:0 0 4px;">Ikizamini Giteganijwe</p>
      <p id="kiuQuizAlertTitle" style="font-size:18px; font-weight:700; margin:0;"></p>
    </div>
    <div style="padding:20px 24px;">
      <p style="font-size:13px; color:#666; margin:0 0 12px;">
        <i class="fa-solid fa-calendar-clock" style="color:var(--gold-light, #e2b45f);"></i>
        <span id="kiuQuizAlertDate"></span>
      </p>
      <div style="background:#faece7; border-radius:16px; padding:14px; text-align:center; margin-bottom:16px;">
        <p style="font-size:11px; color:#994c1d; text-transform:uppercase; letter-spacing:.05em; margin:0 0 4px;">Igihe gisigaye</p>
        <p id="kiuQuizAlertCountdown" style="font-size:22px; font-weight:700; color:#994c1d; margin:0;">--:--:--</p>
      </div>
      <!-- Was missing onclick="kiuDismissQuizAlert()" entirely, so this
           main button did nothing when clicked — only the small red
           "Close - X" text link below actually dismissed the popup.
           Wired up now so the primary action button works. -->
      <button onclick="kiuDismissQuizAlert()" class="kiu-alert-dismiss" style="width:100%; padding:12px; border:none; border-radius:16px; background:var(--gold-light, #e2b45f); color:#fff; font-weight:700; font-size:13px; cursor:pointer;">
        Nahamukanya In shaa Allah, Jazakallahu khayran
      </button>
      <div style="display: flex; justify-content: center;">
          <button onclick="kiuDismissQuizAlert()" class="kiu-alert-close" style="color: #b91c1c; font-size: 13px; margin-top: 10px; background:none; border:none; cursor:pointer;">
              Funga
          </button>
      </div>
    </div>
  </div>
</div>

<script>
(function() {
  let kiuQuizAlertCountdownInterval = null;
  let kiuQuizAlertCurrentId = null;

  function kiuGetDismissedQuizAlerts() {
    try {
      return JSON.parse(localStorage.getItem('kiu_dismissed_quiz_alerts') || '[]');
    } catch (e) {
      return [];
    }
  }

  window.kiuDismissQuizAlert = function() {
    const dismissed = kiuGetDismissedQuizAlerts();
    if (kiuQuizAlertCurrentId && !dismissed.includes(kiuQuizAlertCurrentId)) {
      dismissed.push(kiuQuizAlertCurrentId);
      localStorage.setItem('kiu_dismissed_quiz_alerts', JSON.stringify(dismissed));
    }
    document.getElementById('kiuQuizAlertModal').style.display = 'none';
    if (kiuQuizAlertCountdownInterval) clearInterval(kiuQuizAlertCountdownInterval);
  };

  function kiuStartQuizAlertCountdown(startsAtIso) {
    const target = new Date(startsAtIso).getTime();
    const el = document.getElementById('kiuQuizAlertCountdown');

    function tick() {
      const diff = target - Date.now();
      if (diff <= 0) {
        el.textContent = "Ubu ni bwo!";
        clearInterval(kiuQuizAlertCountdownInterval);
        return;
      }
      const totalSeconds = Math.floor(diff / 1000);
      const h = Math.floor(totalSeconds / 3600);
      const m = Math.floor((totalSeconds % 3600) / 60);
      const s = totalSeconds % 60;
      const pad = n => String(n).padStart(2, '0');
      el.textContent = (h > 0 ? h + ':' : '') + pad(m) + ':' + pad(s);
    }

    tick();
    kiuQuizAlertCountdownInterval = setInterval(tick, 1000);
  }

  function kiuPlayQuizAlertSound() {
    try {
      const AudioCtx = window.AudioContext || window.webkitAudioContext;
      const ctx = new AudioCtx();
      const now = ctx.currentTime;

      [{ freq: 880, start: 0, dur: 0.18 }, { freq: 660, start: 0.16, dur: 0.28 }].forEach(note => {
        const osc = ctx.createOscillator();
        const gain = ctx.createGain();
        osc.type = 'sine';
        osc.frequency.value = note.freq;
        gain.gain.setValueAtTime(0.0001, now + note.start);
        gain.gain.exponentialRampToValueAtTime(0.25, now + note.start + 0.02);
        gain.gain.exponentialRampToValueAtTime(0.0001, now + note.start + note.dur);
        osc.connect(gain);
        gain.connect(ctx.destination);
        osc.start(now + note.start);
        osc.stop(now + note.start + note.dur + 0.05);
      });
    } catch (e) {
      console.warn('Could not play quiz alert sound:', e);
    }
  }

  fetch('{{ route("quiz.alert.upcoming") }}')
    .then(r => r.ok ? r.json() : Promise.reject(new Error('HTTP ' + r.status)))
    .then(data => {
      if (!data.quiz) return;

      const dismissed = kiuGetDismissedQuizAlerts();
      if (dismissed.includes(data.quiz.id)) return;

      kiuQuizAlertCurrentId = data.quiz.id;
      document.getElementById('kiuQuizAlertTitle').textContent = data.quiz.title;
      document.getElementById('kiuQuizAlertDate').textContent = data.quiz.starts_at_local;
      document.getElementById('kiuQuizAlertModal').style.display = 'flex';
      kiuPlayQuizAlertSound();
      kiuStartQuizAlertCountdown(data.quiz.starts_at);
    })
    .catch(err => console.error('Failed to check for upcoming quiz alert:', err));
})();
</script>
