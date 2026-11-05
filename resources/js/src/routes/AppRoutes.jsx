import { Routes, Route } from 'react-router-dom';
import Accueil from '../pages/commun/Accueil.jsx';
import Annuaire from '../pages/commun/Annuaire.jsx';
import Connexion from '../pages/auth/Connexion.jsx';
import Inscription from '../pages/auth/Inscription.jsx';

export default function AppRoutes() {
    return (
        <Routes>
            <Route path="/" element={<Accueil />} />
            <Route path="/annuaire" element={<Annuaire />} />
            <Route path="/connexion" element={<Connexion />} />
            <Route path="/inscription" element={<Inscription />} />
        </Routes>
    );
}