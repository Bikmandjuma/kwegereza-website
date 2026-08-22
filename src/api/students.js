import { api } from "./client.js";

export function fetchPendingStudents(search = "") {
  const q = search ? `?search=${encodeURIComponent(search)}` : "";
  return api.get(`/students/pending${q}`);
}

export function getStudentDetail(id) {
  return api.get(`/students/${id}`);
}

export function approveStudentRequest(id) {
  return api.post(`/students/${id}/approve`);
}

export function rejectStudentRequest(id) {
  return api.post(`/students/${id}/reject`);
}

export function blockStudentRequest(id) {
  return api.post(`/students/${id}/block`);
}
