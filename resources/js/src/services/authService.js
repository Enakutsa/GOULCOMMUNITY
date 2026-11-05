import api, { TOKEN_KEY } from './api.js';

// Étape 1 : créer le compte
export async function inscription({ nom, prenom, email, telephone }) {
    const { data } = await api.post('/auth/inscription', {
        nom,
        prenom,
        email,
        telephone: telephone || null,
    });
    return data;
}

// Étape 2 : demander l'envoi du code par email
export async function envoyerCode(email) {
    const { data } = await api.post('/auth/envoyer-code', { email });
    return data;
}

// Étape 3 : vérifier le code, enregistrer le jeton et renvoyer l'utilisateur
export async function verifierCode(email, code) {
    const { data } = await api.post('/auth/verifier-code', { email, code });
    localStorage.setItem(TOKEN_KEY, data.token);
    return data.utilisateur;
}

export function deconnexion() {
    localStorage.removeItem(TOKEN_KEY);
}

export function aUnJeton() {
    return Boolean(localStorage.getItem(TOKEN_KEY));
}

// Extrait un message lisible d'une erreur renvoyée par Laravel
export function messageErreur(erreur) {
    const reponse = erreur.response?.data;
    if (reponse?.errors) {
        return Object.values(reponse.errors).flat()[0];
    }
    return reponse?.message || 'Une erreur est survenue. Réessayez.';
}