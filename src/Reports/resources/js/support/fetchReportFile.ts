export function fetchReportFile(endpoint: string, parameters: FormData) {
    // @ts-ignore
    const queryParams = new URLSearchParams(parameters).toString();

    return fetch(`${window.sdrtReports.restApi.reportsUrl}${endpoint}?${queryParams}`, {
        method: 'GET',
        credentials: 'include',
        headers: {
            'X-WP-Nonce': window.sdrtReports.restApi.nonce,
        },
    });
}

export async function fetchAndDownloadReportFile(endpoint: string, parameters: FormData): Promise<void> {
    const data = await fetchReportFile(endpoint, parameters);
    const fileName = getFileNameFromDisposition(data.headers.get('content-disposition'));
    const file = await data.blob();

    const url = window.URL.createObjectURL(file);
    const link = document.createElement('a');
    link.href = url;
    link.setAttribute('download', fileName);
    document.body.appendChild(link);
    link.click();
}

function getFileNameFromDisposition(disposition: string): string {
    const filename = disposition.split(/;(.+)/)[1].split(/=(.+)/)[1];
    if (filename.toLowerCase().startsWith("utf-8''"))
        return decodeURIComponent(filename.replace(/utf-8''/i, ''));
    else
        return filename.replace(/['"]/g, '');
}