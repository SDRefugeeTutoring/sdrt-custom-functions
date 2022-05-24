export default async function fetchRestAPI(endpoint, method, body) {
    const response = await fetch(`${window.sdrtVolunteerPortal.restApi.url}/${endpoint}`, {
        method: method,
        body: body,
        credentials: 'include',
        headers: {
            'Content-Type': 'application/json',
            'X-WP-Nonce': window.sdrtVolunteerPortal.restApi.nonce,
        },
    });
    return response.json();
}
