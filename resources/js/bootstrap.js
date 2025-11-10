import axios from 'axios';
window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

// CRITICAL: Enable credentials (cookies) for all Axios requests
// This allows session cookies to be sent with API requests
window.axios.defaults.withCredentials = true;

// Set base URL to ensure requests go to the same domain
window.axios.defaults.baseURL = window.location.origin;

// Function to get fresh CSRF token
function getCsrfToken() {
    const token = document.head.querySelector('meta[name="csrf-token"]');
    return token ? token.content : null;
}

// Set initial CSRF token
const initialToken = getCsrfToken();
if (initialToken) {
    window.axios.defaults.headers.common['X-CSRF-TOKEN'] = initialToken;
} else {
    console.error('CSRF token not found: https://laravel.com/docs/csrf#csrf-x-csrf-token');
}

// Add request interceptor to ensure fresh CSRF token on every request
window.axios.interceptors.request.use(
    config => {
        const token = getCsrfToken();
        if (token) {
            config.headers['X-CSRF-TOKEN'] = token;
        }
        return config;
    },
    error => {
        return Promise.reject(error);
    }
);

// Add response interceptor to handle CSRF token mismatch
window.axios.interceptors.response.use(
    response => response,
    error => {
        if (error.response?.status === 419) {
            // CSRF token mismatch - reload page to get fresh token
            console.error('CSRF token mismatch - reloading page');
            alert('Session expired. Please try again.');
            window.location.reload();
        }
        return Promise.reject(error);
    }
);
