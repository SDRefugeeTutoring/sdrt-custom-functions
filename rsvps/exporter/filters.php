<ul id="rsvp-filters" class="export-filters">
    <li>
        <label>
            <fieldset>
                <legend class="label">Event Dates:</legend>
                <label for="rsvp-start-date" class="label-responsive">Start date:</label>
                <input type="text" name="rsvp-start-date" value="<?= date('m/d/Y', strtotime('-30 days')) ?>" />
                <label for="rsvp-end-date" class="label-responsive">End date:</label>
                <input type="text" name="rsvp-end-date" value="<?= date('m/d/Y') ?>" />
            </fieldset>
        </label>
    </li>
</ul>

<script>
    jQuery(document).ready(function($) {
        const $rsvpFilters = $('#rsvp-filters');

        $('input[name="rsvp-start-date"]').datepicker();
        $('input[name="rsvp-end-date"]').datepicker();

        // Move after rsvp radio button
        $('input:radio[value="rsvp"]').closest('p').after($rsvpFilters)

        // Display when rsvp radio is selected
        $('#export-filters input:radio').change(function() {
            if ( $(this).val() === 'rsvp' ) {
                $rsvpFilters.slideDown();
            }
        })
    });
</script>