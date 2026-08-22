import { api } from "./client.js";

export function searchUsers(q) {
  return api.get(`/users/search?q=${encodeURIComponent(q)}`);
}
