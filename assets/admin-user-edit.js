window.addEventListener('DOMContentLoaded', async function () {
    const button = document.querySelector('.js-sdrt-new-background-check');

    if (button === null) {
        return;
    }

    button.addEventListener('click', async function (event) {
        event.preventDefault();

        try {
            const response = fetch(`${ajaxurl}?action=sdrt_new_background_check`, {
                method: 'GET',
            });
        } catch(e) {
            console.log(e);
        }
    });
});