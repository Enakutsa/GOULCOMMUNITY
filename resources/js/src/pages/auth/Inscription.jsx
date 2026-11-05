import { useState } from 'react';
import { Link, Navigate } from 'react-router-dom';
import AuthLayout from '../../components/layout/AuthLayout.jsx';
import { useAuth } from '../../context/AuthContext.jsx';
import { inscription, messageErreur } from '../../services/authService.js';
import '../../assets/css/pages/auth/Inscription.css';

const FORMULAIRE_VIDE = { nom: '', prenom: '', email: '', telephone: '' };

function Champ({ id, label, type = 'text', valeur, onChange, erreur, facultatif, ...reste }) {
    return (
        <div>
            <label htmlFor={id} className="champ-label">
                {label}
                {facultatif && <span className="champ-aide">(facultatif)</span>}
            </label>
            <input
                id={id}
                type={type}
                value={valeur}
                onChange={onChange}
                className={`champ ${erreur ? 'champ-erreur' : ''}`}
                {...reste}
            />
            {erreur && <p className="champ-message-erreur">{erreur}</p>}
        </div>
    );
}

export default function Inscription() {
    const { estConnecte } = useAuth();

    const [form, setForm] = useState(FORMULAIRE_VIDE);
    const [accepte, setAccepte] = useState(false);
    const [erreurs, setErreurs] = useState({});
    const [erreurGenerale, setErreurGenerale] = useState('');
    const [chargement, setChargement] = useState(false);
    const [emailInscrit, setEmailInscrit] = useState('');

    // Déjà connecté : retour à l'accueil
    if (estConnecte) {
        return <Navigate to="/" replace />;
    }

    const modifier = (champ) => (e) => setForm({ ...form, [champ]: e.target.value });

    const envoyer = async (e) => {
        e.preventDefault();
        setErreurs({});
        setErreurGenerale('');
        setChargement(true);

        const email = form.email.trim().toLowerCase();

        try {
            await inscription({
                nom: form.nom.trim(),
                prenom: form.prenom.trim(),
                email,
                telephone: form.telephone.trim(),
            });
            setEmailInscrit(email);
        } catch (err) {
            const champs = err.response?.data?.errors;
            if (champs) {
                // On garde le premier message de chaque champ
                setErreurs(
                    Object.fromEntries(Object.entries(champs).map(([k, v]) => [k, v[0]]))
                );
            } else {
                setErreurGenerale(messageErreur(err));
            }
        } finally {
            setChargement(false);
        }
    };

    return (
        <AuthLayout
            titre="Rejoignez la communauté"
            sousTitre="Créez votre compte en quelques secondes."
            points={[
                'Suivez vos établissements et vos clubs',
                'Recevez uniquement les annonces qui vous concernent',
                'Connexion sans mot de passe, par code email',
            ]}
        >
            {emailInscrit ? (
                <div className="inscription-succes">
                    <span className="inscription-succes-icone" aria-hidden="true">
                        ✓
                    </span>
                    <h1 className="auth-titre">Compte créé</h1>
                    <p className="auth-sous-titre">
                        Bienvenue ! Connectez-vous avec <strong>{emailInscrit}</strong>. Un code
                        vous sera envoyé par email.
                    </p>
                    <Link
                        to="/connexion"
                        state={{ email: emailInscrit }}
                        className="btn btn-accent btn-bloc inscription-bouton"
                    >
                        Se connecter →
                    </Link>
                </div>
            ) : (
                <>
                    <h1 className="auth-titre">Créer un compte</h1>
                    <p className="auth-sous-titre">
                        Quelques informations suffisent. Pas de mot de passe à retenir.
                    </p>

                    {erreurGenerale && (
                        <p className="alerte-erreur inscription-alerte">{erreurGenerale}</p>
                    )}

                    <form onSubmit={envoyer} className="inscription-formulaire">
                        <div className="inscription-grille">
                            <Champ
                                id="prenom"
                                label="Prénom"
                                valeur={form.prenom}
                                onChange={modifier('prenom')}
                                erreur={erreurs.prenom}
                                required
                                autoComplete="given-name"
                            />
                            <Champ
                                id="nom"
                                label="Nom"
                                valeur={form.nom}
                                onChange={modifier('nom')}
                                erreur={erreurs.nom}
                                required
                                autoComplete="family-name"
                            />
                        </div>

                        <Champ
                            id="email"
                            label="Adresse email"
                            type="email"
                            valeur={form.email}
                            onChange={modifier('email')}
                            erreur={erreurs.email}
                            required
                            autoComplete="email"
                            placeholder="vous@exemple.com"
                        />

                        <Champ
                            id="telephone"
                            label="Téléphone"
                            type="tel"
                            facultatif
                            valeur={form.telephone}
                            onChange={modifier('telephone')}
                            erreur={erreurs.telephone}
                            autoComplete="tel"
                            placeholder="90 00 00 00"
                        />

                        <label className="inscription-consentement">
                            <input
                                type="checkbox"
                                checked={accepte}
                                onChange={(e) => setAccepte(e.target.checked)}
                            />
                            <span>
                                J'accepte les conditions d'utilisation et que mes informations
                                soient utilisées pour mon compte.
                            </span>
                        </label>

                        <button
                            type="submit"
                            disabled={chargement || !accepte}
                            className="btn btn-accent btn-bloc inscription-bouton"
                        >
                            {chargement ? 'Création…' : 'Créer mon compte →'}
                        </button>
                    </form>

                    <p className="inscription-pied">
                        Déjà inscrit ? <Link to="/connexion">Se connecter</Link>
                    </p>
                </>
            )}
        </AuthLayout>
    );
}