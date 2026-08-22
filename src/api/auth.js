import { api } from "./client.js";

export function registerRequest({ fullName, email, password, phone, gender, kunia }) {
  return api.post("/auth/register", { fullName, email, password, phone, gender, kunia });
}

export function loginRequest({ email, password }) {
  return api.post("/auth/login", { email, password });
}

export function meRequest() {
  return api.get("/auth/me");
}

export function logoutRequest() {
  return api.post("/auth/logout");
}

export function googleAuthRequest(idToken) {
  return api.post("/auth/google", { idToken });
}
