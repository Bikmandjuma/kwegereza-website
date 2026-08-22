import { useActivityTracking } from "../hooks/useActivityTracking.js";

export default function ActivityTracker() {
  useActivityTracking();
  return null;
}
