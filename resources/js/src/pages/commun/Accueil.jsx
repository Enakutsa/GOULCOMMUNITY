import { useEffect, useState } from 'react';
import { Link, useNavigate } from 'react-router-dom';
import NavbarPublique from '../../components/layout/NavbarPublique.jsx';
import Footer from '../../components/layout/Footer.jsx';
import { getEtablissements } from '../../services/etablissementService.js';

// Initiales affichées dans la pastille de chaque établissement
function initialesEtablissement(e) {
    const base = e.sigle || e.nom;
    return base
        .split(/[\s-]+/)
        .filter(Boolean)
        .slice(0, 2)
        .map((mot) => mot[0])
        .join('')
        .toUpperCase();
}

const AVANTAGES = [
    {
        titre: 'Abonnements indépendants',
        texte: 'Suivez une école sans suivre tous ses clubs : vous choisissez.',
    },
    {
        titre: 'Fil personnalisé',
        texte: 'Uniquement les publications des pages auxquelles vous êtes abonné.',
    },
    {
        titre: 'Notifications claires',
        texte: 'Lu et non lu, propres à chaque utilisateur.',
    },
];

export default function Accueil() {
    const navigate = useNavigate();
    const [recherche, setRecherche] = useState('');
    const [vedettes, setVedettes] = useState([]);
    const [chargement, setChargement] = useState(true);

    useEffect(() => {
        let annule = false;
        getEtablissements({ secteur: 'public' })
            .then((data) => {
                if (!annule) setVedettes(data.data.slice(0, 3));
            })
            .catch(() => {
                if (!annule) setVedettes([]);
            })
            .finally(() => {
                if (!annule) setChargement(false);
            });
        return () => {
            annule = true;
        };
    }, []);

    const rechercher = (e) => {
        e.preventDefault();
        const q = recherche.trim();
        navigate(q ? `/annuaire?q=${encodeURIComponent(q)}` : '/annuaire');
    };

    return (
        <div className="min-h-screen bg-fond">
            <NavbarPublique />

            {/* Bandeau d'accueil */}
            <section className="bg-bleu-900 text-white">
                <div className="mx-auto grid max-w-6xl gap-10 px-6 py-16 md:grid-cols-2 md:items-center">
                    <div>
                        <span className="inline-block rounded-full bg-accent-500 px-3 py-1 text-xs font-bold text-bleu-900">
                            Togo · Bientôt l'Afrique
                        </span>
                        <h1 className="mt-5 text-4xl font-extrabold leading-tight md:text-5xl">
                            Connectés pour impacter
                        </h1>
                        <p className="mt-4 max-w-md text-lg text-white/80">
                            Découvrez les universités et les clubs, suivez ceux qui vous
                            intéressent et ne ratez plus aucune annonce.
                        </p>

                        <form
                            onSubmit={rechercher}
                            className="mt-8 flex max-w-lg items-center gap-2 rounded-xl bg-white p-2"
                        >
                            <input
                                type="text"
                                value={recherche}
                                onChange={(e) => setRecherche(e.target.value)}
                                placeholder="Rechercher un établissement ou un club"
                                className="min-w-0 flex-1 bg-transparent px-3 py-2 text-texte outline-none placeholder:text-texte-doux"
                            />
                            <button
                                type="submit"
                                className="rounded-lg bg-accent-500 px-4 py-2 font-semibold text-bleu-900 hover:bg-accent-600"
                            >
                                Rechercher
                            </button>
                        </form>
                    </div>

                    {/* Cartes décoratives */}
                    <div className="hidden space-y-4 md:block">
                        <div className="rounded-xl bg-white p-4 text-texte shadow-lg">
                            <p className="font-bold">Suivez vos établissements</p>
                            <p className="text-sm text-texte-doux">
                                Annonces, inscriptions, actualités
                            </p>
                        </div>
                        <div className="ml-8 rounded-xl bg-white p-4 text-texte shadow-lg">
                            <p className="font-bold">Rejoignez des clubs</p>
                            <p className="text-sm text-texte-doux">
                                Événements et rencontres près de vous
                            </p>
                        </div>
                    </div>
                </div>
            </section>

            {/* Établissements à la une */}
            <section className="mx-auto max-w-6xl px-6 py-14">
                <div className="flex items-end justify-between">
                    <h2 className="text-2xl font-extrabold text-bleu-900">
                        Établissements à la une
                    </h2>
                    <Link to="/annuaire" className="font-semibold text-bleu-700 hover:underline">
                        Tout voir →
                    </Link>
                </div>

                {chargement ? (
                    <p className="mt-6 text-texte-doux">Chargement…</p>
                ) : vedettes.length === 0 ? (
                    <p className="mt-6 text-texte-doux">
                        Les établissements s'afficheront ici.
                    </p>
                ) : (
                    <div className="mt-6 grid gap-5 md:grid-cols-3">
                        {vedettes.map((e) => (
                            <article
                                key={e.id}
                                className="rounded-xl border border-bordure bg-white p-5"
                            >
                                <div className="flex items-center gap-3">
                                    <span className="flex h-12 w-12 shrink-0 items-center justify-center rounded-lg bg-bleu-700 font-bold text-white">
                                        {initialesEtablissement(e)}
                                    </span>
                                    <div className="min-w-0">
                                        <h3 className="font-bold leading-snug">{e.nom}</h3>
                                        {e.ville && (
                                            <p className="text-sm text-texte-doux">{e.ville}</p>
                                        )}
                                    </div>
                                </div>
                                <div className="mt-4 flex flex-wrap gap-2 text-xs font-semibold">
                                    <span className="rounded-full bg-bleu-50 px-2 py-1 text-bleu-700">
                                        {e.secteur === 'public' ? 'Public' : 'Privé'}
                                    </span>
                                    {e.statut_page === 'verifiee' && (
                                        <span className="rounded-full bg-accent-50 px-2 py-1 text-accent-600">
                                            Vérifié
                                        </span>
                                    )}
                                </div>
                            </article>
                        ))}
                    </div>
                )}
            </section>

            {/* Pourquoi GOULCOMMUNITY */}
            <section id="avantages" className="mx-auto max-w-6xl px-6 pb-16">
                <h2 className="text-2xl font-extrabold text-bleu-900">
                    Pourquoi GOULCOMMUNITY ?
                </h2>
                <div className="mt-6 grid gap-5 md:grid-cols-3">
                    {AVANTAGES.map((a) => (
                        <article
                            key={a.titre}
                            className="rounded-xl border border-bordure bg-white p-6"
                        >
                            <h3 className="font-bold">{a.titre}</h3>
                            <p className="mt-3 text-texte-doux">{a.texte}</p>
                        </article>
                    ))}
                </div>
            </section>

            <Footer />
        </div>
    );
}