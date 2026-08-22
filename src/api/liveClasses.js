import { api } from "./client.js";

export function createLiveClass(title, scheduledFor) {
  return api.post("/live-classes", scheduledFor ? { title, scheduledFor } : { title });
}

export function listActiveLiveClasses() {
  return api.get("/live-classes/active");
}

export function listUpcomingLiveClasses() {
  return api.get("/live-classes/upcoming");
}

export function getLiveClass(id) {
  return api.get(`/live-classes/${id}`);
}

export function startScheduledLiveClass(id) {
  return api.post(`/live-classes/${id}/start`);
}

export function endLiveClass(id) {
  return api.post(`/live-classes/${id}/end`);
}
