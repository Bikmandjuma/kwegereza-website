import { api } from "./client.js";

export function getAnalyticsOverview(range = "weekly") {
  return api.get(`/analytics/overview?range=${range}`);
}

export function getStudentAnalytics(id) {
  return api.get(`/analytics/students/${id}`);
}
