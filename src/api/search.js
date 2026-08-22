import { api } from "./client.js";

export function searchAll(q) {
  return api.get(`/search?q=${encodeURIComponent(q)}`);
}
