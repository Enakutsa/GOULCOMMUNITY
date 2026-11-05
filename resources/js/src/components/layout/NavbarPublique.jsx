import { Link } from 'react-router-dom';
import { useAuth } from '../../context/AuthContext.jsx';
import '../../assets/css/layout/NavbarPublique.css';

function initiales(utilisateur) {
    const prenom = utilisateur?.prenom?.[0] ?? '';
    const nom = utilisateur?.nom?.[0] ?? '';
    return (prenom + nom).toUpperCase() || '?';
}

export default function NavbarPublique() {
    const { estConnecte, utilisateur, deconnecter } = useAuth();

    return (
        <header className="navbar">
            <div className="conteneur navbar-interieur">
                <Link to="/" className="navbar-logo">
                    <span className="navbar-logo-pastille">G</span>
                    <span className="navbar-logo-texte">GOULCOMMUNITY</span>
                </Link>

                <nav className="navbar-liens">
                    <Link to="/annuaire">Établissements</Link>
                    <Link to="/clubs">Clubs</Link>
                    <a href="/#avantages">Avantages</a>
                </nav>

                {estConnecte ? (
                    <div className="navbar-actions">
                        <button type="button" onClick={deconnecter} className="btn btn-contour">
                            Déconnexion
                        </button>
                        <span
                            title={`${utilisateur.prenom} ${utilisateur.nom}`}
                            className="avatar avatar-rond navbar-avatar"
                        >
                            {initiales(utilisateur)}
                        </span>
                    </div>
                ) : (
                    <div className="navbar-actions">
                        <Link to="/connexion" className="btn btn-contour">
                            Connexion
                        </Link>
                        <Link to="/inscription" className="btn btn-primaire">
                            S'inscrire
                        </Link>
                    </div>
                )}
            </div>
        </header>
    );
}