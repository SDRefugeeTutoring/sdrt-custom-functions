export async function fetchRestApi(endpoint, {method, body}): Promise<Response> {
    return fetch(`${window.sdrtVolunteerPortal.restApi.url}${endpoint}`, {
        method: method,
        body: body ? JSON.stringify(body) : null,
        credentials: 'include',
        headers: {
            'Content-Type': 'application/json',
            'X-WP-Nonce': window.sdrtVolunteerPortal.restApi.nonce,
        },
    });
}

export function fetchSdrtApi(
    endpoint,
    {method = 'POST', body = null}: {method?: string; body?: object} = {method: 'POST', body: null}
): Promise<Response> {
    return fetchRestApi(`sdrt/v1/${endpoint}`, {
        method,
        body,
    });
}
