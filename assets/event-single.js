jQuery(document).ready(function ($) {
    $('.attended-email').on('click', function (event) {
        const $this = $(this);
        event.preventDefault();

        if (!window.confirm('Are you sure you want to send an email to ' + $this.data('email') + '?')) {
            return;
        }

        $.post({
            url: rsvpExports.ajaxUrl,
            data: {
                action: 'sdrt_rsvp_email_no_show',
                nonce: rsvpExports.nonce,
                rsvp_id: $this.data('rsvpId'),
            },
        }).then(function (data) {
            if (!data.success) {
                alert('Failed to send email. Please contact admin.');
                console.error(data);
            }

            $this.replaceWith('<span>Sent!</span>');
        });
    });

    $('.js-delete-rsvp').on('click', function (event) {
        if (!window.confirm('Are you sure you want to delete the RSVP for ' + $(this).data('name') + '?')) {
            event.preventDefault();
        }
    });
});