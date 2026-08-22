import { createContext, useContext, useEffect, useState, useCallback } from "react";
import { googleAuthRequest, loginRequest, logoutRequest, meRequest, registerRequest } from "../api/auth.js";
import { setToken } from "../api/client.js";

const AuthContext = createContext(null);

export function AuthProvider({ children }) {
  const [user, setUser] = useState(null);
  const [loading, setLoading] = useState(true); // true while we validate any existing session

  // Session restoration: never trust localStorage blindly — ask the backend
  // to re-validate the token against the current account status every time
  // the app boots (this is what catches "you were blocked while away").
  useEffect(() => {
    meRequest()
      .then((res) => setUser(res.data.user))
      .catch(() => {
        setToken(null);
        setUser(null);
      })
      .finally(() => setLoading(false));
  }, []);

  const refreshUser = useCallback(async () => {
    try {
      const res = await meRequest();
      setUser(res.data.user);
      return res.data.user;
    } catch {
      setToken(null);
      setUser(null);
      return null;
    }
  }, []);

  const login = useCallback(async (email, password) => {
    const res = await loginRequest({ email, password });
    setToken(res.data.token);
    setUser(res.data.user);
    return res.data.user;
  }, []);

  const register = useCallback(async (fields) => {
    const res = await registerRequest(fields);
    return res.data.user; // status will be PENDING — caller routes to waiting screen
  }, []);

  // Returns the raw response (not just the user) because a Google sign-in
  // can legitimately land as PENDING/BLOCKED too — callers need res.message
  // and status, not just a token, to route correctly (see GoogleRegisterModal).
  const loginWithGoogle = useCallback(async (idToken) => {
    const res = await googleAuthRequest(idToken);
    if (res.data.token) {
      setToken(res.data.token);
    }
    if (res.data.user?.status === "ACTIVE") {
      setUser(res.data.user);
    }
    return res;
  }, []);

  const logout = useCallback(async () => {
    try {
      await logoutRequest();
    } finally {
      setToken(null);
      setUser(null);
    }
  }, []);

  return (
    <AuthContext.Provider value={{ user, loading, login, register, loginWithGoogle, logout, refreshUser }}>
      {children}
    </AuthContext.Provider>
  );
}

export function useAuth() {
  const ctx = useContext(AuthContext);
  if (!ctx) throw new Error("useAuth must be used inside <AuthProvider>");
  return ctx;
}
