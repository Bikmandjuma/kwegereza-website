// Kwegereza service worker — real push handling, not a placeholder.
// Registered from src/utils/push.js.

self.addEventListener("install", () => {
  self.skipWaiting();
});

self.addEventListener("activate", (event) => {
  event.waitUntil(self.clients.claim());
});

self.addEventListener("push", (event) => {
  let payload = { title: "Kwegereza", body: "Ubutumwa bushya.", url: "/" };
  try {
    if (event.data) payload = { ...payload, ...event.data.json() };
  } catch {
    // If the payload isn't JSON for some reason, fall back to the default text.
  }

  event.waitUntil(
    self.registration.showNotification(payload.title, {
      body: payload.body,
      icon: "/icon.png",
      badge: "/icon.png",
      data: { url: payload.url || "/" },
      tag: payload.url, // collapses rapid repeats of the same link into one OS notification
    })
  );
});

self.addEventListener("notificationclick", (event) => {
  event.notification.close();
  const targetUrl = event.notification.data?.url || "/";

  event.waitUntil(
    (async () => {
      const allClients = await self.clients.matchAll({ type: "window", includeUncontrolled: true });
      // Focus an already-open Kwegereza tab instead of opening a duplicate one.
      for (const client of allClients) {
        if ("focus" in client) {
          await client.focus();
          if ("navigate" in client) await client.navigate(targetUrl);
          return;
        }
      }
      await self.clients.openWindow(targetUrl);
    })()
  );
});
