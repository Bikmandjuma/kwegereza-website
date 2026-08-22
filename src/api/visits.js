import { api } from "./client.js";

const VISITOR_KEY = "kiu_visitor_id";

/** Anonymous, client-generated id — identifies a *browser*, not a person,
 * purely so the "today/total visits" counters can dedupe repeat page loads
 * from the same visit into one. No name/email/IP is ever attached to it. */
export function getVisitorId() {
  let id = localStorage.getItem(VISITOR_KEY);
  if (!id) {
    id = crypto.randomUUID();
    localStorage.setItem(VISITOR_KEY, id);
  }
  return id;
}

export function recordVisit(path) {
  return api.post("/public-stats/visit", { path, visitorId: getVisitorId() });
}

export function getPublicStats() {
  return api.get("/public-stats");
}
