import { api } from "./client.js";

// Public reading (no auth required)
export function listPublishedIfaida(search = "") {
  const q = search ? `?search=${encodeURIComponent(search)}` : "";
  return api.get(`/ifaida/published${q}`);
}
export function getPublishedIfaida(id) {
  return api.get(`/ifaida/published/${id}`);
}

// Leader writing area
export function listMyIfaida(status = "") {
  const q = status ? `?status=${status}` : "";
  return api.get(`/ifaida/mine${q}`);
}
export function getMyIfaida(id) {
  return api.get(`/ifaida/mine/${id}`);
}
export function createIfaida(title) {
  return api.post("/ifaida", { title });
}
export function updateIfaida(id, fields) {
  return api.patch(`/ifaida/${id}`, fields);
}
export function deleteIfaida(id) {
  return api.del(`/ifaida/${id}`);
}
export function publishIfaida(id) {
  return api.post(`/ifaida/${id}/publish`);
}
export function unpublishIfaida(id) {
  return api.post(`/ifaida/${id}/unpublish`);
}
