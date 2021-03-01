jQuery(document).ready(function ($) {
    $('.attended-email').on('click', function (event) {
        const $this = $(this);
        event.preventDefault();

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

            $this.replaceWith('<span>Sent!</span>')
        });
    });
});