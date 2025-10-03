// public/js/carpooling-async.js
(function () {
    const RESULTS_ID = 'results';

    function qs(sel, root = document) { return root.querySelector(sel); }

    function toQueryString(form) {
        const fd = new FormData(form);
        return new URLSearchParams(fd).toString();
    }

    async function fetchFragment(url, push = true) {
        const box = document.getElementById(RESULTS_ID);
        if (!box) return;

        // petit indicateur
        const prev = box.innerHTML;
        box.innerHTML = '<div class="text-muted py-3">Chargement…</div>';

        try {
        const res = await fetch(url, {
            headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'text/html'
            },
            credentials: 'same-origin'
        });
        const html = await res.text();

        if (!res.ok) throw new Error(`HTTP ${res.status}`);

        // le contrôleur renvoie directement le fragment _results.html.twig
        box.innerHTML = html;

        // re-binder le formulaire de filtre qui vient d'être re-rendu
        bindFilterForm();

        if (push) history.pushState({ url }, '', url);
        } catch (e) {
        box.innerHTML = prev; // restore
        alert('Erreur lors du chargement des résultats: ' + e.message);
        }
    }

    function onSearchSubmit(e) {
        const form = e.target;
        if (form.method?.toUpperCase() !== 'GET') return; // on attend du GET
        e.preventDefault();

        const action = form.getAttribute('action') || window.location.pathname;
        const qs = toQueryString(form);
        const url = qs ? `${action}?${qs}` : action;

        fetchFragment(url, true);
    }

    function onFilterSubmit(e) {
        const form = e.target;
        if (form.method?.toUpperCase() !== 'GET') return;
        e.preventDefault();

        // l'action contient déjà les paramètres de recherche (filterFormAction côté Twig)
        const action = form.getAttribute('action') || window.location.href;
        const qs = toQueryString(form);
        const sep = action.includes('?') ? '&' : '?';
        const url = qs ? `${action}${sep}${qs}` : action;

        fetchFragment(url, true);
    }

    function bindSearchForm() {
        const form = document.querySelector('form[name="carpooling_search"]');
        if (form && !form.__bound) {
        form.addEventListener('submit', onSearchSubmit);
        form.__bound = true;
        }
    }

    function bindFilterForm() {
        const form = document.querySelector(`#${RESULTS_ID} form[name="carpooling_filter"]`);
        if (form && !form.__bound) {
        form.addEventListener('submit', onFilterSubmit);
        form.__bound = true;
        }
    }

    // navigation Back/Forward : recharger l’état courant
    window.addEventListener('popstate', () => {
        fetchFragment(location.href, false);
    });

    document.addEventListener('DOMContentLoaded', () => {
        bindSearchForm();
        bindFilterForm();
    });
})();