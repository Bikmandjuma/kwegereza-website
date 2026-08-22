import { api } from "./client.js";

export function listAllUsers({ search = "", role = "", status = "", page = 1, perPage = 20, sort = "createdAt", order = "desc" } = {}) {
  const params = new URLSearchParams();
  if (search) params.set("search", search);
  if (role) params.set("role", role);
  if (status) params.set("status", status);
  params.set("page", page);
  params.set("perPage", perPage);
  params.set("sort", sort);
  params.set("order", order);
  return api.get(`/admin/users?${params.toString()}`);
}

export function getPermissionCatalog() {
  return api.get("/admin/permissions-catalog");
}

export function updateUserRole(id, role) {
  return api.patch(`/admin/users/${id}/role`, { role });
}

export function updateUserPermissions(id, permissions) {
  return api.patch(`/admin/users/${id}/permissions`, { permissions });
}

export function blockAnyUser(id) {
  return api.post(`/admin/users/${id}/block`);
}

export function unblockAnyUser(id) {
  return api.post(`/admin/users/${id}/unblock`);
}

export function bulkUpdateUserStatus(ids, action) {
  return api.post(`/admin/users/bulk-status`, { ids, action });
}
