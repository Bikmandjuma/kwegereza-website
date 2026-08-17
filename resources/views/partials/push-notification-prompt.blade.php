<!-- <div id="kiuPushPrompt" style="display:none; position:fixed; bottom:16px; right:16px; z-index:9998; max-width:320px; background:#fff; border-radius:20px; box-shadow:0 10px 30px rgba(0,0,0,.25); padding:16px 18px; border:1px solid #eee;">
  <div style="display:flex; align-items:flex-start; gap:12px;">
    <span style="font-size:22px;">🔔</span>
    <div style="flex:1;">
      <p style="margin:0 0 6px; font-weight:700; font-size:14px; color:#094939;">Wifuza kubona ubutumwa?</p>
      <p style="margin:0 0 12px; font-size:12px; color:#666;">Tuzakumenyesha igihe ikizamini gishya kizaba giteganyijwe.</p>
      <div style="display:flex; gap:8px;">
        <button onclick="kiuEnablePushNotifications()" style="flex:1; padding:8px 12px; border:none; border-radius:12px; background:#058e48; color:#fff; font-weight:700; font-size:12px; cursor:pointer;">
          Emeza
        </button>
        <button onclick="kiuDismissPushPrompt()" style="padding:8px 12px; border:none; border-radius:12px; background:#f2f2f2; color:#666; font-weight:600; font-size:12px; cursor:pointer;">
          Singombwa
        </button>
      </div>
    </div>
  </div>
</div>

<script>
(function() {
    const VAPID_PUBLIC_KEY = "{{ config('webpush.vapid_public_key') }}";

    function urlBase64ToUint8Array(base64String) {
        const padding = '='.repeat((4 - base64String.length % 4) % 4);
        const base64 = (base64String + padding).replace(/-/g, '+').replace(/_/g, '/');
        const rawData = window.atob(base64);
        const outputArray = new Uint8Array(rawData.length);
        for (let i = 0; i < rawData.length; i++) {
            outputArray[i] = rawData.charCodeAt(i);
        }
        return outputArray;
    }

    function kiuIsPushSupported() {
        return 'serviceWorker' in navigator && 'PushManager' in window && 'Notification' in window;
    }

    window.kiuDismissPushPrompt = function() {
        document.getElementById('kiuPushPrompt').style.display = 'none';
        localStorage.setItem('kiu_push_prompt_dismissed_at', Date.now().toString());
    };

    window.kiuEnablePushNotifications = function() {
        Notification.requestPermission().then(function(permission) {
            document.getElementById('kiuPushPrompt').style.display = 'none';
            if (permission !== 'granted') {
                localStorage.setItem('kiu_push_prompt_dismissed_at', Date.now().toString());
                return;
            }
            if (!VAPID_PUBLIC_KEY) {
                console.warn('Push notifications: VAPID public key is not configured.');
                return;
            }

            navigator.serviceWorker.ready.then(function(registration) {
                return registration.pushManager.subscribe({
                    userVisibleOnly: true,
                    applicationServerKey: urlBase64ToUint8Array(VAPID_PUBLIC_KEY),
                });
            }).then(function(subscription) {
                return fetch('{{ route("push.subscribe") }}', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                    body: JSON.stringify(subscription),
                });
            }).catch(function(err) {
                console.error('Failed to subscribe to push notifications:', err);
            });
        });
    };

    if (!kiuIsPushSupported()) return;
    if (Notification.permission !== 'default') return;

    const dismissedAt = parseInt(localStorage.getItem('kiu_push_prompt_dismissed_at') || '0', 10);
    if (Date.now() - dismissedAt < 7 * 24 * 60 * 60 * 1000) return;

    document.getElementById('kiuPushPrompt').style.display = 'block';
})();
</script>
 -->

 <div id="kiuPushPrompt" style="display:none; position:fixed; bottom:16px; right:16px; z-index:9998; max-width:320px; background:#fff; border-radius:20px; box-shadow:0 10px 30px rgba(0,0,0,.25); padding:16px 18px; border:1px solid #eee;">
  <div style="display:flex; align-items:flex-start; gap:12px;">
    <span style="font-size:22px;">🔔</span>
    <div style="flex:1;">
      <p style="margin:0 0 6px; font-weight:700; font-size:14px; color:#094939;">Wifuza kubona ubutumwa?</p>
      <p style="margin:0 0 12px; font-size:12px; color:#666;">Tuzaguhamagara igihe ikizamini gishya giteganijwe, na make yindi makuru y'ingenzi — no iyo utari kuri iyi paji.</p>
      <div style="display:flex; gap:8px;">
        <button onclick="kiuEnablePushNotifications()" style="flex:1; padding:8px 12px; border:none; border-radius:12px; background:#058e48; color:#fff; font-weight:700; font-size:12px; cursor:pointer;">
          Emeza
        </button>
        <button onclick="kiuDismissPushPrompt()" style="padding:8px 12px; border:none; border-radius:12px; background:#f2f2f2; color:#666; font-weight:600; font-size:12px; cursor:pointer;">
          Ntabwo ubu
        </button>
      </div>
    </div>
  </div>
</div>

<script>
(function() {
    const VAPID_PUBLIC_KEY = "{{ config('webpush.vapid_public_key') }}";

    function urlBase64ToUint8Array(base64String) {
        const padding = '='.repeat((4 - base64String.length % 4) % 4);
        const base64 = (base64String + padding).replace(/-/g, '+').replace(/_/g, '/');
        const rawData = window.atob(base64);
        const outputArray = new Uint8Array(rawData.length);
        for (let i = 0; i < rawData.length; i++) {
            outputArray[i] = rawData.charCodeAt(i);
        }
        return outputArray;
    }

    function kiuIsPushSupported() {
        return 'serviceWorker' in navigator && 'PushManager' in window && 'Notification' in window;
    }

    function kiuSubscribeToPush() {
        if (!VAPID_PUBLIC_KEY) {
            console.warn('Push notifications: VAPID public key is not configured.');
            return Promise.resolve();
        }
        return navigator.serviceWorker.ready.then(function(registration) {
            return registration.pushManager.subscribe({
                userVisibleOnly: true,
                applicationServerKey: urlBase64ToUint8Array(VAPID_PUBLIC_KEY),
            });
        }).then(function(subscription) {
            return fetch('{{ route("push.subscribe") }}', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                body: JSON.stringify(subscription),
            });
        }).then(function() {
            console.log('Push subscription saved.');
        }).catch(function(err) {
            console.error('Failed to subscribe to push notifications:', err);
        });
    }

    window.kiuDismissPushPrompt = function() {
        document.getElementById('kiuPushPrompt').style.display = 'none';
        localStorage.setItem('kiu_push_prompt_dismissed_at', Date.now().toString());
    };

    window.kiuEnablePushNotifications = function() {
        Notification.requestPermission().then(function(permission) {
            document.getElementById('kiuPushPrompt').style.display = 'none';
            if (permission !== 'granted') {
                localStorage.setItem('kiu_push_prompt_dismissed_at', Date.now().toString());
                return;
            }
            kiuSubscribeToPush();
        });
    };

    if (!kiuIsPushSupported()) return;

    // Permission was already granted (possibly through the browser's own
    // site-settings UI directly, bypassing the banner below, or from an
    // earlier attempt that failed server-side before VAPID keys were
    // configured correctly) — in that case the banner never shows again
    // (browsers don't let you re-prompt for a decision already made), so
    // there would otherwise be no way to ever complete the subscribe
    // step. Check silently on every page load: if permission is granted
    // but there's no active subscription yet, (re)subscribe automatically
    // with no extra click needed.
    if (Notification.permission === 'granted') {
        navigator.serviceWorker.ready.then(function(registration) {
            return registration.pushManager.getSubscription();
        }).then(function(existingSubscription) {
            if (!existingSubscription) {
                kiuSubscribeToPush();
            }
        }).catch(function(err) {
            console.error('Could not check existing push subscription:', err);
        });
        return;
    }

    if (Notification.permission !== 'default') return;

    const dismissedAt = parseInt(localStorage.getItem('kiu_push_prompt_dismissed_at') || '0', 10);
    if (Date.now() - dismissedAt < 7 * 24 * 60 * 60 * 1000) return;

    document.getElementById('kiuPushPrompt').style.display = 'block';
})();
</script>
