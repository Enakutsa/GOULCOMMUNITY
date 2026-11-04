import { Link } from 'react-router-dom';

export default function Annuaire() {
    return (
        <div className="p-8">
            <h1 className="text-2xl font-bold">Annuaire des établissements</h1>
            <Link to="/" className="mt-4 inline-block underline">
                Retour à l'accueil
            </Link>
        </div>
    );
}