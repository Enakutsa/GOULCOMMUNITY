import api from './api.js';

export async function getEtablissements({ q = '', secteur = '', page = 1 } = {}) {
    const params = { page };
    if (q) params.q = q;
    if (secteur) params.secteur = secteur;

    const { data } = await api.get('/etablissements', { params });
    return data;
}