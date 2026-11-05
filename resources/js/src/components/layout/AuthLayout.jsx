import { Link } from 'react-router-dom';
import '../../assets/css/layout/AuthLayout.css';

function Logo({ className = '' }) {
    return (
        <Link to="/" className={`auth-logo ${className}`}>
            <span className="auth-logo-pastille">G</span>
            <span className="auth-logo-texte">GOULCOMMUNITY</span>
        </Link>
    );
}

export default function AuthLayout({ titre, sousTitre, points = [], children }) {
    return (
        <div className="auth">
            <aside className="auth-panneau">
                <span className="auth-halo auth-halo-1" aria-hidden="true" />
                <span className="auth-halo auth-halo-2" aria-hidden="true" />

                <Logo />

                <div className="auth-panneau-contenu">
                    <h2 className="auth-panneau-titre">{titre}</h2>
                    {sousTitre && <p className="auth-panneau-texte">{sousTitre}</p>}

                    {points.length > 0 && (
                        <ul className="auth-points">
                            {points.map((point) => (
                                <li key={point}>
                                    <span className="auth-point-icone" aria-hidden="true">
                                        ✓
                                    </span>
                                    {point}
                                </li>
                            ))}
                        </ul>
                    )}
                </div>

                <p className="auth-signature">Connectés pour impacter</p>
            </aside>

            <main className="auth-principal">
                <Logo className="auth-logo-mobile" />
                <div className="auth-carte">{children}</div>
            </main>
        </div>
    );
}