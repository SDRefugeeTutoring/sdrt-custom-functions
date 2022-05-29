export async function fetchRestApi(endpoint, method, body): Promise<Response> {
    return fetch(`${window.sdrtVolunteerPortal.restApi.url}${endpoint}`, {
        method: method,
        body: body,
        credentials: 'include',
        headers: {
            'Content-Type': 'application/json',
            'X-WP-Nonce': window.sdrtVolunteerPortal.restApi.nonce,
        },
    });
}

export function fetchSdrtApi(endpoint, method = 'POST', body = {}): Promise<Response> {
    return fetchRestApi(`sdrt/v1/${endpoint}`, method, body);
}
