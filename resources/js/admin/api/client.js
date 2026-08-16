import axios from 'axios'

/**
 * Now served from inside the same Laravel app (resources/js/admin),
 * mounted at /admin — so this can finally be a plain relative path
 * instead of a hardcoded origin+port. That absolute-URL default
 * (http://localhost:8000/api/owner) was the actual root cause of every
 * CORS/port-mismatch issue hit while this was a separate standalone
 * Vite app on its own port talking cross-origin to Laravel. Same
 * origin now — there is no cross-origin request left to misconfigure.
 */
const client = axios.create({
  baseURL: import.meta.env.VITE_API_BASE_URL || '/api/owner',
  headers: { Accept: 'application/json' },
})

client.interceptors.request.use((config) => {
  const token = localStorage.getItem('kwegereza_token')
  if (token) config.headers.Authorization = `Bearer ${token}`
  return config
})

// A 401 anywhere means the token is gone/invalid — clear it and let the
// AuthContext-driven route guard bounce to /login on next render.
client.interceptors.response.use(
  (response) => response,
  (error) => {
    if (error.response?.status === 401) {
      localStorage.removeItem('kwegereza_token')
      localStorage.removeItem('kwegereza_user')
    }
    return Promise.reject(error)
  }
)

export default client
