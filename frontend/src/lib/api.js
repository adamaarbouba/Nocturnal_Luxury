import axios from 'axios'

const api = axios.create({
  baseURL: 'http://localhost:8000',
  withCredentials: true,
  withXSRFToken: true,
  headers: { Accept: 'application/json' },
})

let csrfReady = null
export function ensureCsrf() {
  csrfReady ??= api.get('/sanctum/csrf-cookie')
  return csrfReady
}

// Convenience: all /api calls; mutating calls fetch the CSRF cookie first.
const request = async (method, url, ...args) => {
  if (method !== 'get') await ensureCsrf()
  return api[method](`/api${url}`, ...args)
}

export default {
  get: (url, config) => request('get', url, config),
  post: (url, data, config) => request('post', url, data, config),
  put: (url, data, config) => request('put', url, data, config),
  patch: (url, data, config) => request('patch', url, data, config),
  delete: (url, config) => request('delete', url, config),
}
