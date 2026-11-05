export default function Footer() {
    return (
        <footer className="bg-bleu-900 text-white">
            <div className="mx-auto flex max-w-6xl flex-col gap-4 px-6 py-8 text-sm md:flex-row md:items-center md:justify-between">
                <p className="text-white/80">
                    © GOULCOMMUNITY · Connectés pour impacter
                </p>
                <nav className="flex flex-wrap gap-x-5 gap-y-2 text-white/80">
                    <a href="#" className="hover:text-accent-500">
                        Contact
                    </a>
                    <a href="#" className="hover:text-accent-500">
                        Confidentialité
                    </a>
                    <a href="#" className="hover:text-accent-500">
                        Conditions
                    </a>
                </nav>
            </div>
        </footer>
    );
}