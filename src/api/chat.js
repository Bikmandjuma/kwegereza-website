import { api } from "./client.js";

export function startConversation(withUserId) {
  return api.post("/chat/start", { withUserId });
}

export function listConversations() {
  return api.get("/chat/conversations");
}

export function listMessages(conversationId) {
  return api.get(`/chat/conversations/${conversationId}/messages`);
}

export function markConversationRead(conversationId) {
  return api.post(`/chat/conversations/${conversationId}/read`);
}
