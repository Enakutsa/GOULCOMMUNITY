import { createContext, useContext, useEffect, useState } from 'react';
import {
    verifierCode,
    deconnexion as supprimerJeton,
    aUnJeton,
} from '../services/authService.js';

const CLE_UTILISATEUR = 'goulcommunity_utilisateur';

const AuthContext = createContext(null);

// Relit l'utilisateur enregistré, seulement si un jeton existe encore
function lireUtilisateurEnregistre() {
    if (!aUnJeton()) return null;
    try {
        const brut = localStorage.getItem(CLE_UTILISATEUR);
        return brut ? JSON.parse(brut) : null;
    } catch {
        return null;
    }
}

export function AuthProvider({ children }) {
    const [utilisateur, setUtilisateur] = useState(lireUtilisateurEnregistre);

    // Connexion : vérifie le code, puis garde l'utilisateur
    const connecter = async (email, code) => {
        const user = await verifierCode(email, code);
        localStorage.setItem(CLE_UTILISATEUR, JSON.stringify(user));
        setUtilisateur(user);
        return user;
    };

    const deconnecter = () => {
        supprimerJeton();
        localStorage.removeItem(CLE_UTILISATEUR);
        setUtilisateur(null);
    };

    // Si le serveur dit que le jeton n'est plus valable (voir api.js)
    useEffect(() => {
        const surExpiration = () => {
            localStorage.removeItem(CLE_UTILISATEUR);
            setUtilisateur(null);
        };
        window.addEventListener('auth:expire', surExpiration);
        return () => window.removeEventListener('auth:expire', surExpiration);
    }, []);

    const valeur = {
        utilisateur,
        estConnecte: utilisateur !== null,
        connecter,
        deconnecter,
    };

    return <AuthContext.Provider value={valeur}>{children}</AuthContext.Provider>;
}

export function useAuth() {
    const contexte = useContext(AuthContext);
    if (!contexte) {
        throw new Error('useAuth doit être utilisé dans un AuthProvider');
    }
    return contexte;
}