import { useState } from 'react';
import { Link, Navigate, useLocation, useNavigate } from 'react-router-dom';
import AuthLayout from '../../components/layout/AuthLayout.jsx';
import { useAuth } from '../../context/AuthContext.jsx';
import { envoyerCode, messageErreur } from '../../services/authService.js';
import '../../assets/css/pages/auth/Connexion.css';

function IconeEmail() {
    return (
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" aria-hidden="true">
            <rect x="3" y="5" width="18" height="14" rx="2" />
            <path d="m3 7 9 6 9-6" />
        </svg>
    );
}

export default function Connexion() {
    const { estConnecte, connecter } = useAuth();
    const navigate = useNavigate();
    const location = useLocation();

    const [etape, setEtape] = useState('email'); // 'email' puis 'code'
    const [email, setEmail] = useState(location.state?.email ?? '');
    const [code, setCode] = useState('');
    const [info, setInfo] = useState('');
    const [erreur, setErreur] = useState('');
    const [chargement, setChargement] = useState(false);

    // Déjà connecté : retour à l'accueil
    if (estConnecte) {
        return <Navigate to="/" replace />;
    }

    const emailNettoye = email.trim().toLowerCase();

    const demanderCode = async (e) => {
        e.preventDefault();
        setErreur('');
        setInfo('');
        setChargement(true);
        try {
            const reponse = await envoyerCode(emailNettoye);
            setInfo(reponse.message);
            setEtape('code');
        } catch (err) {
            setErreur(messageErreur(err));
        } finally {
            setChargement(false);
        }
    };

    const validerCode = async (e) => {
        e.preventDefault();
        setErreur('');
        setChargement(true);
        try {
            await connecter(emailNettoye, code);
            navigate('/', { replace: true });
        } catch (err) {
            setErreur(messageErreur(err));
        } finally {
            setChargement(false);
        }
    };

    const changerEmail = () => {
        setEtape('email');
        setCode('');
        setErreur('');
        setInfo('');
    };

    return (
        <AuthLayout
            titre="Content de vous revoir"
            sousTitre="Retrouvez votre fil d'actualité, vos abonnements et vos notifications."
            points={[
                'Connexion sans mot de passe',
                'Un code unique envoyé par email',
                'Vos abonnements toujours à jour',
            ]}
        >
            <div className="connexion-etapes" aria-hidden="true">
                <span className="actif" />
                <span className={etape === 'code' ? 'actif' : ''} />
            </div>

            <h1 className="auth-titre">
                {etape === 'email' ? 'Connexion' : 'Vérifiez votre email'}
            </h1>
            <p className="auth-sous-titre">
                {etape === 'email'
                    ? 'Entrez votre email, nous vous envoyons un code.'
                    : `Saisissez le code à 6 chiffres envoyé à ${emailNettoye}.`}
            </p>

            {erreur && <p className="alerte-erreur connexion-alerte">{erreur}</p>}
            {info && etape === 'code' && <p className="alerte-info connexion-alerte">{info}</p>}

            {etape === 'email' ? (
                <form onSubmit={demanderCode} className="connexion-formulaire">
                    <div>
                        <label htmlFor="email" className="champ-label">
                            Adresse email
                        </label>
                        <div className="connexion-champ">
                            <IconeEmail />
                            <input
                                id="email"
                                type="email"
                                required
                                autoComplete="email"
                                value={email}
                                onChange={(e) => setEmail(e.target.value)}
                                placeholder="vous@exemple.com"
                                className="champ"
                            />
                        </div>
                    </div>
                    <button
                        type="submit"
                        disabled={chargement}
                        className="btn btn-accent btn-bloc connexion-bouton"
                    >
                        {chargement ? 'Envoi…' : 'Recevoir mon code →'}
                    </button>
                </form>
            ) : (
                <form onSubmit={validerCode} className="connexion-formulaire">
                    <div>
                        <label htmlFor="code" className="champ-label">
                            Code de connexion
                        </label>
                        <input
                            id="code"
                            type="text"
                            inputMode="numeric"
                            autoComplete="one-time-code"
                            maxLength={6}
                            required
                            autoFocus
                            value={code}
                            onChange={(e) => setCode(e.target.value.replace(/\D/g, ''))}
                            placeholder="000000"
                            className="champ connexion-code"
                        />
                    </div>
                    <button
                        type="submit"
                        disabled={chargement || code.length !== 6}
                        className="btn btn-accent btn-bloc connexion-bouton"
                    >
                        {chargement ? 'Vérification…' : 'Me connecter'}
                    </button>

                    <div className="connexion-liens">
                        <button type="button" onClick={changerEmail} className="connexion-lien">
                            Changer d'email
                        </button>
                        <button
                            type="button"
                            onClick={demanderCode}
                            disabled={chargement}
                            className="connexion-lien"
                        >
                            Renvoyer le code
                        </button>
                    </div>
                </form>
            )}

            <p className="connexion-pied">
                Pas encore de compte ? <Link to="/inscription">S'inscrire</Link>
            </p>
        </AuthLayout>
    );
}