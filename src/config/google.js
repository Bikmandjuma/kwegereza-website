// A placeholder is the safe default — the app still runs and every other
// feature works. GoogleRegisterModal checks isGoogleConfigured() and simply
// doesn't render the Google button (falls back to "Iyandikishe"/"Injira")
// until a real Client ID from Google Cloud Console is set.
export const GOOGLE_CLIENT_ID = import.meta.env.VITE_GOOGLE_CLIENT_ID || "REPLACE_WITH_YOUR_GOOGLE_CLIENT_ID";

export function isGoogleConfigured() {
  return GOOGLE_CLIENT_ID !== "REPLACE_WITH_YOUR_GOOGLE_CLIENT_ID" && GOOGLE_CLIENT_ID.length > 0;
}
