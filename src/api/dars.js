import { api } from "./client.js";

export function getPublishedDarsat(params = {}) {
  const qs = new URLSearchParams(params).toString();
  return api.get(`/dars/published${qs ? `?${qs}` : ""}`);
}

export function trackDarsPlay(id) {
  return api.post(`/dars/${id}/play`, {});
}

export function listAdminDarsat(params = {}) {
  const qs = new URLSearchParams(params).toString();
  return api.get(`/dars${qs ? `?${qs}` : ""}`);
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

export function createDars(fields, files) {
  return api.postForm("/dars", toFormData(fields, files));
}

export function updateDars(id, fields, files = {}) {
  return api.patchForm(`/dars/${id}`, toFormData(fields, files));
}

export function deleteDars(id) {
  return api.del(`/dars/${id}`);
}

export function publishDars(id) {
  return api.post(`/dars/${id}/publish`, {});
}

export function unpublishDars(id) {
  return api.post(`/dars/${id}/unpublish`, {});
}
