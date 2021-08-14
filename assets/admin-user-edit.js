window.addEventListener('DOMContentLoaded', async function () {
    const button = document.querySelector('.js-sdrt-new-background-check');

    if (button === null) {
        return;
    }

    function showError(message) {
        const notice = document.createElement('div');
        notice.style.backgroundColor = '#ff8295';
        notice.style.margin = '0.5em 0';
        notice.style.padding = '1em';
        notice.style.fontWeight = 'bold';
        notice.textContent = `There was an error generating the background check. Please report the following to a website admin: ${message}`;

        button.parentElement.append(notice)
    }

    button.addEventListener('click', async function (event) {
        event.preventDefault();

        try {
            const urlParams = new URLSearchParams(window.location.search);
            const response = await fetch(`${ajaxurl}?action=sdrt_new_background_check&user_id=${urlParams.get('user_id')}`, {
                method: 'GET',
            });
            const {data, success} = await response.json();

            if ( !success) {
                showError(data);
                return;
            }

            if (data.candidate_id) {
                const candidateIdText = document.querySelector('input[name="background_check_candidate_id"]');
                candidateIdText.value = data.candidate_id;
            }

            if (data.invitation_url) {
                const inviteUrlText = document.querySelector('input[name="background_check_invite_url"]');
                inviteUrlText.value = data.invitation_url;
            }

            button.remove();
        } catch (e) {
            showError(e.message);
        }
    });
});