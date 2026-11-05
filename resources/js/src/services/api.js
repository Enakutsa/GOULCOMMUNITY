import axios from 'axios';

export const TOKEN_KEY = 'goulcommunity_token';

const api = axios.create({
    baseURL: '/api',
    headers: { Accept: 'application/json' },
});

// Ajoute le jeton à chaque requête, s'il existe
api.interceptors.request.use((config) => {
    const token = localStorage.getItem(TOKEN_KEY);
    if (token) {
        config.headers.Authorization = `Bearer ${token}`;
    }
    return config;
});

// Si le jeton n'est plus valable (401), on déconnecte proprement
api.interceptors.response.use(
    (reponse) => reponse,
    (erreur) => {
        if (erreur.response?.status === 401 && localStorage.getItem(TOKEN_KEY)) {
            localStorage.removeItem(TOKEN_KEY);
            window.dispatchEvent(new Event('auth:expire'));
        }
        return Promise.reject(erreur);
    }
);

export default api;