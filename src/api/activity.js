import { api } from "./client.js";

export function trackEvent(type, meta = {}) {
  return api.post("/activity/track", { type, meta });
}

export function sendHeartbeat() {
  return api.post("/activity/heartbeat");
}
