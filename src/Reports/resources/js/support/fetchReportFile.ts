export function fetchReportFile(endpoint: string, parameters: FormData) {
    // @ts-ignore
    const queryParams = new URLSearchParams(parameters).toString();

    return fetch(`${window.sdrtReports.restApi.url}${endpoint}?${queryParams}`, {
        method: 'GET',
        credentials: 'include',
        headers: {
            'X-WP-Nonce': window.sdrtReports.restApi.nonce,
        },
    });
}
