import { api } from "../api/client.js";

function urlBase64ToUint8Array(base64String) {
  const padding = "=".repeat((4 - (base64String.length % 4)) % 4);
  const base64 = (base64String + padding).replace(/-/g, "+").replace(/_/g, "/");
  const rawData = atob(base64);
  return Uint8Array.from([...rawData].map((c) => c.charCodeAt(0)));
}

export function pushSupported() {
  return "serviceWorker" in navigator && "PushManager" in window && "Notification" in window;
}

export function notificationPermission() {
  return pushSupported() ? Notification.permission : "unsupported";
}

/**
 * Registers the real service worker, asks the browser for notification
 * permission (a genuine permission prompt — never bypassed or assumed),
 * subscribes to the browser's push service with our VAPID public key, and
 * sends that subscription to the backend to store.
 */
export async function enablePushNotifications() {
  if (!pushSupported()) {
    throw new Error("Iyi burowseri ntishyigikira push notifications.");
  }

  const permission = await Notification.requestPermission();
  if (permission !== "granted") {
    throw new Error("Ntabwo wemeye ubutumwa. Ushobora kubyemera nyuma muri parametero za burowseri.");
  }

  const registration = await navigator.serviceWorker.register("/sw.js");
  await navigator.serviceWorker.ready;

  const { data } = await api.get("/push/vapid-public-key");
  const applicationServerKey = urlBase64ToUint8Array(data.publicKey);

  const subscription = await registration.pushManager.subscribe({
    userVisibleOnly: true,
    applicationServerKey,
  });

  await api.post("/push/subscribe", { subscription: subscription.toJSON() });
  return subscription;
}

export async function disablePushNotifications() {
  if (!pushSupported()) return;
  const registration = await navigator.serviceWorker.getRegistration();
  const subscription = await registration?.pushManager.getSubscription();
  if (subscription) {
    await api.post("/push/unsubscribe", { endpoint: subscription.endpoint });
    await subscription.unsubscribe();
  }
}
