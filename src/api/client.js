// const BASE_URL = import.meta.env.VITE_API_URL ?? "http://localhost:4000/api";

// export function getToken() {
//   return localStorage.getItem("kiu_token");
// }

// export function setToken(token) {
//   if (token) localStorage.setItem("kiu_token", token);
//   else localStorage.removeItem("kiu_token");
// }

// /**
//  * Every backend response has the shape { success, data, message, meta }.
//  * This wrapper unwraps that consistently and throws a real Error (with the
//  * Kinyarwanda `message` from the server) on failure, so callers can just
//  * try/catch instead of re-checking `success` everywhere.
//  */
// async function request(path, { method = "GET", body, headers = {} } = {}) {
//   const token = getToken();
//   const res = await fetch(`${BASE_URL}${path}`, {
//     method,
//     credentials: "include",
//     headers: {
//       "Content-Type": "application/json",
//       ...(token ? { Authorization: `Bearer ${token}` } : {}),
//       ...headers,
//     },
//     body: body ? JSON.stringify(body) : undefined,
//   });

//   const payload = await res.json().catch(() => ({}));

//   if (!res.ok || payload.success === false) {
//     const err = new Error(payload.message || "Habaye ikibazo. Ongera ugerageze.");
//     err.status = res.status;
//     err.payload = payload;
//     throw err;
//   }

//   return payload;
// }

// export const api = {
//   get: (path) => request(path),
//   post: (path, body) => request(path, { method: "POST", body }),
//   patch: (path, body) => request(path, { method: "PATCH", body }),
//   put: (path, body) => request(path, { method: "PUT", body }),
//   del: (path) => request(path, { method: "DELETE" }),
// };


const BASE_URL =
  import.meta.env.VITE_API_URL || "http://172.20.10.5:4000/api";

console.log("API BASE URL:", BASE_URL);

export function getToken() {
  return localStorage.getItem("kiu_token");
}

export function setToken(token) {
  if (token) {
    localStorage.setItem("kiu_token", token);
  } else {
    localStorage.removeItem("kiu_token");
  }
}

async function request(
  path,
  { method = "GET", body, headers = {} } = {}
) {
  const token = getToken();

  const url = `${BASE_URL}${path}`;

  console.log(`${method} ${url}`);

  const res = await fetch(url, {
    method,
    credentials: "include",
    headers: {
      "Content-Type": "application/json",
      ...(token
        ? {
            Authorization: `Bearer ${token}`,
          }
        : {}),
      ...headers,
    },
    body: body ? JSON.stringify(body) : undefined,
  });

  const payload = await res.json().catch(() => ({}));

  if (!res.ok || payload.success === false) {
    const err = new Error(
      payload.message || "Habaye ikibazo. Ongera ugerageze."
    );

    err.status = res.status;
    err.payload = payload;

    throw err;
  }

  return payload;
}

/**
 * For multipart/form-data requests (file uploads). Deliberately does NOT
 * set Content-Type — the browser sets it (with the correct boundary) only
 * when it sees we're sending a FormData body untouched.
 */
async function requestForm(path, { method = "POST", formData } = {}) {
  const token = getToken();
  const url = `${BASE_URL}${path}`;
  const res = await fetch(url, {
    method,
    credentials: "include",
    headers: {
      ...(token ? { Authorization: `Bearer ${token}` } : {}),
    },
    body: formData,
  });

  const payload = await res.json().catch(() => ({}));
  if (!res.ok || payload.success === false) {
    const err = new Error(payload.message || "Habaye ikibazo. Ongera ugerageze.");
    err.status = res.status;
    err.payload = payload;
    throw err;
  }
  return payload;
}

export const api = {
  get: (path) => request(path),
  postForm: (path, formData) => requestForm(path, { method: "POST", formData }),
  patchForm: (path, formData) => requestForm(path, { method: "PATCH", formData }),
  post: (path, body) =>
    request(path, {
      method: "POST",
      body,
    }),
  patch: (path, body) =>
    request(path, {
      method: "PATCH",
      body,
    }),
  put: (path, body) =>
    request(path, {
      method: "PUT",
      body,
    }),
  del: (path) =>
    request(path, {
      method: "DELETE",
    }),
};