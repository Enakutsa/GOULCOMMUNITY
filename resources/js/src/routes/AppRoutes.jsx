import { Routes, Route } from 'react-router-dom';
import Accueil from '../pages/Accueil.jsx';
import Annuaire from '../pages/Annuaire.jsx';

export default function AppRoutes() {
    return (
        <Routes>
            <Route path="/" element={<Accueil />} />
            <Route path="/annuaire" element={<Annuaire />} />
        </Routes>
    );
}