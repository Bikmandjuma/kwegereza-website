import { api } from "./client.js";

export function getPublicTeachers() {
  return api.get("/teachers");
}

export function getPublicTeacher(id) {
  return api.get(`/teachers/${id}`);
}

export function listAdminTeachers() {
  return api.get("/teachers/admin/all");
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

export function createTeacher(fields, files = {}) {
  return api.postForm("/teachers", toFormData(fields, files));
}

export function updateTeacher(id, fields, files = {}) {
  return api.patchForm(`/teachers/${id}`, toFormData(fields, files));
}

export function deleteTeacher(id) {
  return api.del(`/teachers/${id}`);
}
