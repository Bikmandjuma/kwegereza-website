import { api } from "./client.js";

export function getPublishedAnnouncements(params = {}) {
  const qs = new URLSearchParams(params).toString();
  return api.get(`/announcements/published${qs ? `?${qs}` : ""}`);
}

export function listAdminAnnouncements(params = {}) {
  const qs = new URLSearchParams(params).toString();
  return api.get(`/announcements${qs ? `?${qs}` : ""}`);
}

function toFormData(fields, files) {
  const fd = new FormData();
  Object.entries(fields).forEach(([k, v]) => {
    if (v !== undefined && v !== null) fd.append(k, v);
  });
  Object.entries(files).forEach(([k, file]) => {
    if (file) fd.append(k, file);
  });
  return fd;
}

export function createAnnouncement(fields, files) {
  return api.postForm("/announcements", toFormData(fields, files));
}

export function updateAnnouncement(id, fields, files = {}) {
  return api.patchForm(`/announcements/${id}`, toFormData(fields, files));
}

export function deleteAnnouncement(id) {
  return api.del(`/announcements/${id}`);
}

export function publishAnnouncement(id) {
  return api.post(`/announcements/${id}/publish`, {});
}

export function unpublishAnnouncement(id) {
  return api.post(`/announcements/${id}/unpublish`, {});
}
