import { useEffect, useState } from 'react';
import { useSearchParams } from 'react-router-dom';
import NavbarPublique from '../../components/layout/NavbarPublique.jsx';
import Footer from '../../components/layout/Footer.jsx';
import { getEtablissements } from '../../services/etablissementService.js';

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

export default function Annuaire() {
    const [parametres, setParametres] = useSearchParams();
    const q = parametres.get('q') ?? '';

    const [saisie, setSaisie] = useState(q);
    const [secteur, setSecteur] = useState('');
    const [page, setPage] = useState(1);

    const [resultat, setResultat] = useState(null);
    const [chargement, setChargement] = useState(true);
    const [erreur, setErreur] = useState('');

    // Garde le champ de recherche aligné sur l'adresse (ex. venant de l'accueil)
    useEffect(() => {
        setSaisie(q);
        setPage(1);
    }, [q]);

    useEffect(() => {
        let annule = false;
        setChargement(true);
        setErreur('');

        getEtablissements({ q, secteur, page })
            .then((data) => {
                if (!annule) setResultat(data);
            })
            .catch(() => {
                if (!annule) setErreur("Impossible de charger l'annuaire. Réessayez.");
            })
            .finally(() => {
                if (!annule) setChargement(false);
            });

        return () => {
            annule = true;
        };
    }, [q, secteur, page]);

    const rechercher = (e) => {
        e.preventDefault();
        const valeur = saisie.trim();
        setParametres(valeur ? { q: valeur } : {});
    };

    const changerSecteur = (e) => {
        setPage(1);
        setSecteur(e.target.value);
    };

    return (
        <div className="min-h-screen bg-fond">
            <NavbarPublique />

            <main className="mx-auto max-w-6xl px-6 py-12">
                <h1 className="text-3xl font-extrabold text-bleu-900">
                    Annuaire des établissements
                </h1>
                <p className="mt-2 text-texte-doux">
                    Établissements d'enseignement supérieur accrédités au Togo.
                </p>

                <form onSubmit={rechercher} className="mt-8 flex flex-wrap gap-3">
                    <input
                        type="text"
                        value={saisie}
                        onChange={(e) => setSaisie(e.target.value)}
                        placeholder="Rechercher un nom ou un sigle (ex. ESGIS)"
                        className="min-w-0 flex-1 rounded-lg border border-bordure bg-white px-4 py-3 outline-none focus:border-bleu-600"
                    />
                    <select
                        value={secteur}
                        onChange={changerSecteur}
                        className="rounded-lg border border-bordure bg-white px-4 py-3 outline-none focus:border-bleu-600"
                    >
                        <option value="">Tous</option>
                        <option value="public">Public</option>
                        <option value="prive">Privé</option>
                    </select>
                    <button
                        type="submit"
                        className="rounded-lg bg-accent-500 px-5 py-3 font-semibold text-bleu-900 hover:bg-accent-600"
                    >
                        Rechercher
                    </button>
                </form>

                {erreur && <p className="mt-6 text-red-600">{erreur}</p>}

                {chargement && !resultat && (
                    <p className="mt-8 text-texte-doux">Chargement…</p>
                )}

                {resultat && (
                    <>
                        <p className="mt-8 text-sm text-texte-doux">
                            {resultat.total} établissement{resultat.total > 1 ? 's' : ''}
                            {chargement && ' · mise à jour…'}
                        </p>

                        {resultat.data.length === 0 ? (
                            <p className="mt-4">Aucun établissement trouvé.</p>
                        ) : (
                            <div className="mt-4 grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
                                {resultat.data.map((e) => (
                                    <article
                                        key={e.id}
                                        className="rounded-xl border border-bordure bg-white p-5"
                                    >
                                        <div className="flex items-center gap-3">
                                            <span className="flex h-12 w-12 shrink-0 items-center justify-center rounded-lg bg-bleu-700 font-bold text-white">
                                                {initialesEtablissement(e)}
                                            </span>
                                            <div className="min-w-0">
                                                <h2 className="font-bold leading-snug">{e.nom}</h2>
                                                {e.sigle && (
                                                    <p className="text-sm text-texte-doux">
                                                        {e.sigle}
                                                    </p>
                                                )}
                                            </div>
                                        </div>
                                        <div className="mt-4 flex flex-wrap gap-2 text-xs font-semibold">
                                            <span className="rounded-full bg-bleu-50 px-2 py-1 text-bleu-700">
                                                {e.secteur === 'public' ? 'Public' : 'Privé'}
                                            </span>
                                            {e.ville && (
                                                <span className="rounded-full bg-fond px-2 py-1 text-texte-doux">
                                                    {e.ville}
                                                </span>
                                            )}
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

                        <div className="mt-8 flex items-center justify-between">
                            <button
                                onClick={() => setPage((p) => p - 1)}
                                disabled={resultat.current_page <= 1}
                                className="rounded-lg border border-bordure bg-white px-4 py-2 font-medium disabled:opacity-40"
                            >
                                Précédent
                            </button>
                            <span className="text-sm text-texte-doux">
                                Page {resultat.current_page} / {resultat.last_page}
                            </span>
                            <button
                                onClick={() => setPage((p) => p + 1)}
                                disabled={resultat.current_page >= resultat.last_page}
                                className="rounded-lg border border-bordure bg-white px-4 py-2 font-medium disabled:opacity-40"
                            >
                                Suivant
                            </button>
                        </div>
                    </>
                )}
            </main>

            <Footer />
        </div>
    );
}