/**
 * Cultulangues API Client
 * =======================
 * Thin JSON client for the Laravel backend (Bearer token in localStorage).
 * Exposes Auth + helpers used by the public site and the three portals.
 */
(function () {
  'use strict';

  const API_BASE = (function () {
    const meta = document.querySelector('meta[name="api-base"]');
    return (meta && meta.getAttribute('content')) || window.API_BASE || '/api/v1';
  })();

  const TOKEN_KEY = 'cultulangues_token';
  const USER_KEY = 'cultulangues_user';

  function getToken() { return localStorage.getItem(TOKEN_KEY); }
  function setToken(t) { t ? localStorage.setItem(TOKEN_KEY, t) : localStorage.removeItem(TOKEN_KEY); }
  function getUser() {
    try { return JSON.parse(localStorage.getItem(USER_KEY) || 'null'); } catch (e) { return null; }
  }
  function setUser(u) { u ? localStorage.setItem(USER_KEY, JSON.stringify(u)) : localStorage.removeItem(USER_KEY); }

  function getRole() {
    const u = getUser();
    return u && u.role ? u.role : null;
  }

  function isAuthenticated() { return !!getToken(); }

  async function request(path, options) {
    options = options || {};
    const headers = Object.assign({ 'Accept': 'application/json' }, options.headers || {});

    if (options.body && !(options.body instanceof FormData)) {
      headers['Content-Type'] = 'application/json';
    }

    const token = getToken();
    if (token) headers['Authorization'] = 'Bearer ' + token;

    let resp;
    try {
      resp = await fetch(API_BASE + path, Object.assign({}, options, { headers }));
    } catch (e) {
      throw { success: false, message: 'Network error: could not reach the server.', network: true };
    }

    let payload = null;
    try { payload = await resp.json(); } catch (e) { /* non-JSON response */ }

    if (!resp.ok) {
      const err = payload || { success: false, message: 'Request failed (' + resp.status + ').' };
      if (resp.status === 401) {
        setToken(null);
        setUser(null);
      }
      throw err;
    }

    return payload && payload.success !== undefined ? payload : { success: true, data: payload };
  }

  function qs(params) {
    if (!params) return '';
    const parts = [];
    Object.keys(params).forEach(function (k) {
      if (params[k] !== undefined && params[k] !== null && params[k] !== '') {
        parts.push(encodeURIComponent(k) + '=' + encodeURIComponent(params[k]));
      }
    });
    return parts.length ? '?' + parts.join('&') : '';
  }

  async function login(email, password) {
    const data = await request('/auth/login', {
      method: 'POST',
      body: JSON.stringify({ email: email, password: password }),
    });
    setToken(data.data.token);
    setUser(data.data.user);
    return data.data.user;
  }

  async function register(payload) {
    const data = await request('/auth/register', {
      method: 'POST',
      body: JSON.stringify(payload),
    });
    setToken(data.data.token);
    setUser(data.data.user);
    return data.data.user;
  }

  async function logout() {
    try { await request('/auth/logout', { method: 'POST' }); } catch (e) { /* ignore */ }
    setToken(null);
    setUser(null);
  }

  async function me() {
    const data = await request('/me');
    const user = data.data.user;
    setUser(user);
    return user;
  }

  async function get(path, params) { return request(path + qs(params)); }
  async function post(path, body) { return request(path, { method: 'POST', body: JSON.stringify(body || {}) }); }
  async function put(path, body) { return request(path, { method: 'PUT', body: JSON.stringify(body || {}) }); }
  async function del(path) { return request(path, { method: 'DELETE' }); }

  // ────────────────────────────────────────────────────────────────
  //  STATIC COURSE → DB SERVICE ID MAPPING
  //  The static booking page keys courses by its own catalog
  //  (booking.html?course=...). These numeric ids come from the
  //  seeded services table (see ServiceSeeder order).
  // ────────────────────────────────────────────────────────────────
  var SOLO_PACKAGE_IDS = { '5h': 15, '10h': 16, '15h': 17, '20h': 18 };
  var COURSE_TO_SERVICE = {
    'francais-express': 1,
    'soiree-linguo': 2,
    'samedi': 3,
    'samedis-francais': 3,
    'samedis-en-francais': 3,
    'english-express': 4,
    'english-linguistic-pathway': 4,
    'evening-lingo': 5,
    'saturdays-english': 6,
    'oral': 7,
    'oral-b': 7,
    'oral-bc': 7,
    'oral-b-partiel': 7,
    'oral-b-intensif': 8,
    'oral-c': 9,
    'oral-c-partiel': 9,
    'oral-c-intensif': 10,
    'tcf': 11,
    'tcf-quebec': 11,
    'tcf-quebec-partiel': 11,
    'tcf-quebec-intensif': 12,
    'tcf-canada': 13,
    'tcf-canada-partiel': 13,
    'tcf-canada-intensif': 14,
    'intensif': 1,
    'groupe': 1,
    'workshop': 19,
    'workshop-conversation': 19,
    'workshop-culture': 20,
    'workshop-maintenance': 21,
    'solo': 15,
    'private': 15
  };

  function resolveServiceId(courseId, soloPackage) {
    if ((courseId === 'solo' || courseId === 'private') && soloPackage && SOLO_PACKAGE_IDS[soloPackage]) {
      return SOLO_PACKAGE_IDS[soloPackage];
    }
    return COURSE_TO_SERVICE[courseId] || 15;
  }

  function splitFullName(fullName) {
    var parts = (fullName || '').trim().split(/\s+/);
    var first = parts.shift() || '';
    return { first_name: first, last_name: parts.join(' ') || '' };
  }

  async function createBooking(payload) {
    return request('/bookings', { method: 'POST', body: JSON.stringify(payload || {}) });
  }

  async function sendContact(payload) {
    return request('/contact', { method: 'POST', body: JSON.stringify(payload || {}) });
  }

  window.CultulanguesAPI = {
    base: API_BASE,
    getToken: getToken,
    isAuthenticated: isAuthenticated,
    getRole: getRole,
    getUser: getUser,
    login: login,
    register: register,
    logout: logout,
    me: me,
    get: get,
    post: post,
    put: put,
    del: del,
    request: request,
    resolveServiceId: resolveServiceId,
    splitFullName: splitFullName,
    createBooking: createBooking,
    sendContact: sendContact,
  };
})();
