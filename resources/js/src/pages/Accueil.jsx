import { Link } from 'react-router-dom';

export default function Accueil() {
    return (
        <div className="p-8">
            <h1 className="text-3xl font-bold">GOULCOMMUNITY</h1>
            <p className="mt-2">React fonctionne. Connectés pour impacter.</p>
            <Link to="/annuaire" className="mt-4 inline-block underline">
                Voir l'annuaire
            </Link>
        </div>
    );
}