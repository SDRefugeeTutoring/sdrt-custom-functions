export async function fetchRestApi(
    endpoint,
    {method = 'POST', body = null}: {method?: string; body?: object} = {method: 'POST', body: null}
): Promise<Response> {
    return fetch(`${window.sdrtReports.restApi.url}${endpoint}`, {
        method: method,
        body: body ? JSON.stringify(body) : null,
        credentials: 'include',
        headers: {
            'Content-Type': 'application/json',
            'X-WP-Nonce': window.sdrtReports.restApi.nonce,
        },
    });
}
