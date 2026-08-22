import { api } from "./client.js";

export function getPublishedBooks(params = {}) {
  const qs = new URLSearchParams(params).toString();
  return api.get(`/books/published${qs ? `?${qs}` : ""}`);
}

export function trackBookDownload(id) {
  return api.post(`/books/${id}/download`, {});
}

export function listAdminBooks(params = {}) {
  const qs = new URLSearchParams(params).toString();
  return api.get(`/books${qs ? `?${qs}` : ""}`);
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

export function createBook(fields, files) {
  return api.postForm("/books", toFormData(fields, files));
}

export function updateBook(id, fields, files = {}) {
  return api.patchForm(`/books/${id}`, toFormData(fields, files));
}

export function deleteBook(id) {
  return api.del(`/books/${id}`);
}
