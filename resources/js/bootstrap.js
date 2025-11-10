import axios from 'axios';
window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

// CRITICAL: Enable credentials (cookies) for all Axios requests
// This allows session cookies to be sent with API requests
window.axios.defaults.withCredentials = true;

// Set base URL to ensure requests go to the same domain
window.axios.defaults.baseURL = window.location.origin;

// Add CSRF token to all requests
const token = document.head.querySelector('meta[name="csrf-token"]');
if (token) {
    window.axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content;
} else {
    console.error('CSRF token not found: https://laravel.com/docs/csrf#csrf-x-csrf-token');
}
