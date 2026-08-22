import { api } from "./client.js";

export function listNotifications() {
  return api.get("/notifications");
}

export function markNotificationRead(id) {
  return api.post(`/notifications/${id}/read`);
}

export function markAllNotificationsRead() {
  return api.post("/notifications/read-all");
}

export function getNotificationHistory({ page = 1, perPage = 20, category = "" } = {}) {
  const params = new URLSearchParams();
  params.set("page", page);
  params.set("perPage", perPage);
  if (category) params.set("category", category);
  return api.get(`/notifications/history?${params.toString()}`);
}

export function getNotificationPreferences() {
  return api.get("/notifications/preferences");
}

export function updateNotificationPreference(category, changes) {
  return api.put("/notifications/preferences", { category, ...changes });
}
