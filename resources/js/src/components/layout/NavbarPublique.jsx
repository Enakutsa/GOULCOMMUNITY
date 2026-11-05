import { Link } from 'react-router-dom';
import { useAuth } from '../../context/AuthContext.jsx';

function initiales(utilisateur) {
    const prenom = utilisateur?.prenom?.[0] ?? '';
    const nom = utilisateur?.nom?.[0] ?? '';
    return (prenom + nom).toUpperCase() || '?';
}

export default function NavbarPublique() {
    const { estConnecte, utilisateur, deconnecter } = useAuth();

    return (
        <header className="border-b border-bordure bg-white">
            <div className="mx-auto flex h-20 max-w-6xl items-center justify-between px-6">
                <Link to="/" className="flex items-center gap-3">
                    <span className="flex h-10 w-10 items-center justify-center rounded-lg bg-accent-500 text-lg font-extrabold text-bleu-900">
                        G
                    </span>
                    <span className="text-lg font-extrabold tracking-tight text-bleu-900">
                        GOULCOMMUNITY
                    </span>
                </Link>

                <nav className="hidden items-center gap-8 md:flex">
                    <Link to="/annuaire" className="font-medium hover:text-bleu-700">
                        Établissements
                    </Link>
                    <Link to="/clubs" className="font-medium hover:text-bleu-700">
                        Clubs
                    </Link>
                    <a href="/#avantages" className="font-medium hover:text-bleu-700">
                        Avantages
                    </a>
                </nav>

                {estConnecte ? (
                    <div className="flex items-center gap-3">
                        <button
                            type="button"
                            onClick={deconnecter}
                            className="rounded-lg border border-bleu-700 px-4 py-2 text-sm font-semibold text-bleu-700 hover:bg-bleu-50"
                        >
                            Déconnexion
                        </button>
                        <span
                            title={`${utilisateur.prenom} ${utilisateur.nom}`}
                            className="flex h-11 w-11 items-center justify-center rounded-full bg-bleu-700 text-sm font-bold text-white"
                        >
                            {initiales(utilisateur)}
                        </span>
                    </div>
                ) : (
                    <div className="flex items-center gap-3">
                        <Link
                            to="/connexion"
                            className="rounded-lg border border-bleu-700 px-4 py-2 text-sm font-semibold text-bleu-700 hover:bg-bleu-50"
                        >
                            Connexion
                        </Link>
                        <Link
                            to="/inscription"
                            className="rounded-lg bg-bleu-700 px-4 py-2 text-sm font-semibold text-white hover:bg-bleu-600"
                        >
                            S'inscrire
                        </Link>
                    </div>
                )}
            </div>
        </header>
    );
}