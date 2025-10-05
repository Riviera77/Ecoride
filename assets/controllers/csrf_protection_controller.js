
const nameCheck = /^[-_a-zA-Z0-9]{4,22}$/;
const tokenCheck = /^[-_\/+a-zA-Z0-9]{24,}$/;

const SELECTOR = 'input[data-controller="csrf-protection"], input[name="_csrf_token"]';

function getCsrfField(formElement) {
    return formElement?.querySelector(SELECTOR) ?? null;
}

function cookieName(field) {
  // data-csrf-protection-cookie-value  =>  field.dataset.csrfProtectionCookieValue
    return field?.dataset?.csrfProtectionCookieValue ?? '';
}

document.addEventListener('submit', (event) => {
    generateCsrfToken(event.target);
}, true);

document.addEventListener('turbo:submit-start', (event) => {
    const h = generateCsrfHeaders(event.detail.formSubmission.formElement);
    for (const k in h) {
        event.detail.formSubmission.fetchRequest.headers[k] = h[k];
    }
});

document.addEventListener('turbo:submit-end', (event) => {
    removeCsrfToken(event.detail.formSubmission.formElement);
});

export function generateCsrfToken(formElement) {
    const csrfField = getCsrfField(formElement);
    if (!csrfField) return;

    let csrfCookie = cookieName(csrfField);
    let csrfToken  = csrfField.value;

    // première visite : le cookie-name n'est pas encore fixé => on l'initialise depuis la valeur
    if (!csrfCookie && nameCheck.test(csrfToken)) {
        csrfField.dataset.csrfProtectionCookieValue = csrfCookie = csrfToken;

        // puis on génère un token aléatoire
        const bytes = (window.crypto || window.msCrypto).getRandomValues(new Uint8Array(18));
        csrfField.defaultValue = csrfToken = btoa(String.fromCharCode.apply(null, bytes));
        csrfField.dispatchEvent(new Event('change', { bubbles: true }));
    }

    if (csrfCookie && tokenCheck.test(csrfToken)) {
        const cookie = `${csrfCookie}_${csrfToken}=${csrfCookie}; path=/; samesite=strict`;
        document.cookie = window.location.protocol === 'https:' ? `__Host-${cookie}; secure` : cookie;
    }
}

export function generateCsrfHeaders(formElement) {
    const headers = {};
    const csrfField = getCsrfField(formElement);
    if (!csrfField) return headers;

    const cName = cookieName(csrfField);
    if (tokenCheck.test(csrfField.value) && nameCheck.test(cName)) {
        headers[cName] = csrfField.value;
    }
    return headers;
}

export function removeCsrfToken(formElement) {
    const csrfField = getCsrfField(formElement);
    if (!csrfField) return;

    const cName = cookieName(csrfField);
    if (tokenCheck.test(csrfField.value) && nameCheck.test(cName)) {
        const cookie = `${cName}_${csrfField.value}=0; path=/; samesite=strict; max-age=0`;
        document.cookie = window.location.protocol === 'https:' ? `__Host-${cookie}; secure` : cookie;
    }
}

/* stimulusFetch: 'lazy' */
export default 'csrf-protection-controller';