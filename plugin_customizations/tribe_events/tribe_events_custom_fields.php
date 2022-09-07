<?php

add_action("add_meta_boxes", "compositions_add_top_meta_box");

function compositions_add_top_meta_box()
{
    add_meta_box("rsvp-info", "RSVPs", "rsvp_metabox_markup", "tribe_events", "side", "high", null);
}

function rsvp_metabox_markup()
{
    global $post;
    wp_nonce_field(basename(__FILE__), "sdrt_rsvp_enabler_nonce");
    $rsvp_enable = get_post_meta($post->ID, "enable_rsvps", true);
    $rsvp_form_id = get_post_meta($post->ID, "rsvp_form", true);
    $rsvps_limit = get_post_meta($post->ID, "rsvps_limit", true);
    $volunteer_time = get_post_meta($post->ID, "volunteer_time", true);
    $logged_in_status = get_post_meta($post->ID, "logged_in_status", true);
    $send_reminder = get_post_meta($post->ID, 'rsvp_send_reminder', true);

    if (empty($rsvp_enable)) {
        $rsvp_enable = 'enabled';
    }
    if (empty($rsvp_form_id)) {
        $rsvp_form_id = '';
    }
    if (empty($rsvps_limit)) {
        $rsvps_limit = '0';
    }
    if (empty($logged_in_status)) {
        $logged_in_status = 'yes';
    }
    ?>

    <style>
        .rsvp_options {
            margin: 10px 0;
        }

        .rsvp_options label.main_label {
            font-weight: 700;
            display: block;
            line-height: 2;
        }
    </style>

    <script>
        jQuery(document).ready(function ($) {

            if ($('div#enable_rsvps_option input').attr('value') === 'enabled') {
                $('#rsvps_limit_option_wrap').slideDown("slow");
            } else {
                $('#rsvps_limit_option_wrap').slideUp("slow");
            }

            $('div#enable_rsvps_option input[type="radio"]').click(function () {
                if ($(this).attr('value') === 'enabled') {
                    $('#rsvps_limit_option_wrap').slideDown("slow");
                } else {
                    $('#rsvps_limit_option_wrap').slideUp("slow");
                }
            });
        });
    </script>

    <div id="enable_rsvps_option" class="rsvp_options enabler">
        <label for="enable_rsvps" class="main_label">Enable RSVPs</label>
        <fieldset>
            <label>
                <input name="enable_rsvps" value="enabled" type="radio" <?php
                checked($rsvp_enable, 'enabled'); ?>> Enabled</label>

            <label><input name="enable_rsvps" value="disabled" type="radio" <?php
                checked($rsvp_enable, 'disabled'); ?>> Disabled</label>
        </fieldset>
    </div>

    <div id="rsvps_limit_option_wrap" class="rsvp_options options sdrt-hide">
        <?php
        $forms = GFAPI::get_forms(true, false, 'title');
        ?>

        <p>
            <label for="rsvp_form" class="main_label">Choose Your RSVP Form</label>

            <select id="rsvp_form" name="rsvp_form">
                <option disabled value <?php
                selected($rsvp_form_id, ''); ?>> -- Select Your Form --
                </option>
                <?php

                foreach ($forms as $form) {
                    $selected = selected($rsvp_form_id, $form['id'], false);
                    echo "<option value={$form['id']} $selected>{$form['title']}</option>";
                }

                ?>
            </select>
        </p>

        <p>
            <label for="rsvps_limit" class="main_label">Limit RSVPs</label>
            <input name="rsvps_limit" type="number" value="<?php
            echo $rsvps_limit; ?>" id="rsvps_limit">
        </p>
        <p>
            <label for="volunteer_time" class="main_label">Volunteer Time (in minutes)</label>
            <input name="volunteer_time" type="number" value="<?php
            echo $volunteer_time; ?>" id="volunteer_time">
        </p>
        <p>
            <label for="logged_in_status" class="main_label">Must Users be Logged In?</label>
        <fieldset>
            <label><input name="logged_in_status" value="yes" type="radio" <?php
                checked($logged_in_status, 'yes', true); ?>> Yes</label>

            <label><input name="logged_in_status" value="no" type="radio" <?php
                checked($logged_in_status, 'no', true); ?>> No</label>
        </fieldset>
        </p>
        <p>
            <label for="rsvp_send_reminder" class="main_label">Send reminder the day before</label>
        <fieldset>
            <label><input name="rsvp_send_reminder" value="yes" type="radio" <?php
                checked($send_reminder, 'yes', true); ?>> Yes</label>

            <label><input name="rsvp_send_reminder" value="no" type="radio" <?php
                checked($send_reminder, 'no', true); ?>> No</label>
        </fieldset>
        </p>
    </div>

    <?php
}

add_action("save_post", "save_rsvp_options", 10, 3);

/**
 * @param int     $post_id
 * @param WP_Post $post
 * @param bool    $update
 *
 * @return void
 */
function save_rsvp_options($post_id, $post, $update)
{
    if ( ! isset($_POST["sdrt_rsvp_enabler_nonce"]) || ! wp_verify_nonce($_POST["sdrt_rsvp_enabler_nonce"],
            basename(__FILE__))) {
        return;
    }

    if ( ! current_user_can("edit_post", $post_id)) {
        return;
    }

    if (defined("DOING_AUTOSAVE") && DOING_AUTOSAVE) {
        return;
    }

    if ('tribe_events' !== $post->post_type) {
        return;
    }

    $update_meta = static function ($key, $default) use ($post_id) {
        update_post_meta($post_id, $key, $_POST[$key] ?: $default);
    };

    $update_meta('enable_rsvps', 'disabled');
    $update_meta('rsvp_form', '');
    $update_meta('rsvps_limit', '30');
    $update_meta('logged_in_status', 'yes');
    $update_meta('rsvp_send_reminder', 'no');
    $update_meta('volunteer_time', 0);
}
