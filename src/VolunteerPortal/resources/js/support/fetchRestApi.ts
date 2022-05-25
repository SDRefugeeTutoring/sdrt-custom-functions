export default async function fetchProfileApi<T extends {status: number}>(endpoint, method, body): Promise<Response> {
    return fetch(`${window.sdrtVolunteerPortal.restApi.url}sdrt/v1/portal/${endpoint}`, {
        method: method,
        body: body,
        credentials: 'include',
        headers: {
            'Content-Type': 'application/json',
            'X-WP-Nonce': window.sdrtVolunteerPortal.restApi.nonce,
        },
    });
}

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
