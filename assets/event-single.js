jQuery(document).ready(function ($) {
    /**
     * Gets data from the row for the RSVP
     *
     * @param {jQuery} $row
     * @returns {{rsvpId, name, email}}
     */
    function getRsvpData($row) {
        return {
            rsvpId: $row.data('rsvpId'),
            email: $row.data('email'),
            name: $row.data('name'),
        };
    }

    /**
     * Posts an AJAX action for the RSVP
     *
     * @param {string} action
     * @param {number} rsvpId
     * @param {Object} data
     * @returns {Promise}
     */
    function postRsvpAction(action, rsvpId, data = {}) {
        return $.post({
            url: rsvpExports.ajaxUrl,
            data: {
                action,
                nonce: rsvpExports.nonce,
                rsvp_id: rsvpId,
                ...data,
            },
        });
    }

    $('.js-email-no-show').on('click', function (event) {
        event.preventDefault();

        const $this = $(this);
        const {rsvpId, email} = getRsvpData($this.closest('tr'));

        if (!window.confirm('Are you sure you want to send an email to ' + email + '?')) {
            return;
        }

        postRsvpAction('sdrt_rsvp_email_no_show', rsvpId)
            .then(function (data) {
                if (!data.success) {
                    alert('Failed to send email. Please contact admin.');
                    console.error(data);
                }

                $this.replaceWith('<span>Sent!</span>');
            });
    });

    $('.js-set-attended').on('click', function (event) {
        event.preventDefault();

        const $this = $(this);
        const {rsvpId} = getRsvpData($this.closest('tr'));
        const attended = $this.data('attended') ? 1 : 0;

        postRsvpAction('sdrt_set_event_attendance', rsvpId, {
            attended,
        })
            .then(function (data) {
                if (!data.success) {
                    alert('Failed to set rsvp attendance. Please contact admin.');
                    console.error(data);
                }

                $this.hide();
                $this
                    .closest('tr')
                    .find('.js-set-attended[data-attended="' + ( attended ? 0 : 1 ) + '"]')
                    .show();
            });
    });

    $('.js-delete-rsvp').on('click', function (event) {
        const {name} = getRsvpData($(this).closest('tr'));

        if (!window.confirm('Are you sure you want to delete the RSVP for ' + name + '?')) {
            event.preventDefault();
        }
    });
});