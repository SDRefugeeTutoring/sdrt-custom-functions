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
    $rsvp_enable = get_post_meta($post->ID, "enable_rsvps", true );
    $rsvp_form_id = get_post_meta($post->ID, "rsvp_form", true );
    $rsvps_limit = get_post_meta($post->ID, "rsvps_limit", true );
    $logged_in_status = get_post_meta($post->ID, "logged_in_status", true );

    if ( empty($rsvp_enable) ) {$rsvp_enable = 'enabled';}
    if ( empty($rsvp_form_id) ) {$rsvp_form_id = '';}
    if ( empty($rsvps_limit) ) {$rsvps_limit = '0';}
    if ( empty($logged_in_status) ) {$logged_in_status = 'yes';}
    ?>

    <style>
        .rsvp_options {
            margin: 10px 0;
        }
        .rsvp_options label.main_label {
            font-weight: 700;
            display: block;
            line-height:2;
        }
    </style>

    <script>
        jQuery(document).ready(function($) {

            if($('div#enable_rsvps_option input').attr('value') == 'enabled') {
                $('#rsvps_limit_option_wrap').slideDown("slow");
            }
            else {
                $('#rsvps_limit_option_wrap').slideUp("slow");
            }

            $('div#enable_rsvps_option input[type="radio"]').click(function() {
                if($(this).attr('value') == 'enabled') {
                    $('#rsvps_limit_option_wrap').slideDown("slow");
                }

                else {
                    $('#rsvps_limit_option_wrap').slideUp("slow");
                }
            });
        });
    </script>

    <div id="enable_rsvps_option" class="rsvp_options enabler">
        <label for="enable_rsvps" class="main_label">Enable RSVPs</label>
        <fieldset>
            <label><input name="enable_rsvps" value="enabled" type="radio" <?php  checked( $rsvp_enable, 'enabled' ); ?>> Enabled</label>

            <label><input name="enable_rsvps" value="disabled" type="radio" <?php  checked( $rsvp_enable, 'disabled' ); ?>> Disabled</label>
        </fieldset>
    </div>

    <div id="rsvps_limit_option_wrap" class="rsvp_options options sdrt-hide">
        <?php
            $forms = Caldera_Forms_Forms::get_forms($with_details = true);
        ?>

        <p>
            <label for="rsvp_form" class="main_label">Choose Your RSVP Form</label>

            <select id="rsvp_form" name="rsvp_form">
                <option disabled value <?php selected( $rsvp_form_id, '' ); ?>> -- Select Your Form -- </option>
                <?php

                foreach ( $forms as $form ) {
                    echo '<option value="' . $form['ID'] . '" ' .  selected( $rsvp_form_id, $form['ID'] ) . '>' . $form['name'] . '</option>';
                }

                ?>
            </select>
        </p>

        <p>
            <label for="rsvps_limit" class="main_label">Limit RSVPs</label>
            <input name="rsvps_limit" type="number" value="<?php echo $rsvps_limit; ?>" id="rsvps_limit">
        </p>
        <p>
            <label for="logged_in_status" class="main_label">Must Users be Logged In?</label>
            <fieldset>
                <label><input name="logged_in_status" value="yes" type="radio" <?php  checked( $logged_in_status, 'yes', true ); ?>> Yes</label>

                <label><input name="logged_in_status" value="no" type="radio" <?php  checked( $logged_in_status, 'no', true ); ?>> No</label>
            </fieldset>
        </p>
    </div>

    <?php
}

add_action("save_post", "save_rsvp_options", 10, 3);

function save_rsvp_options($post_id, $post, $update)
{
    if ( !isset($_POST["sdrt_rsvp_enabler_nonce"] ) || !wp_verify_nonce( $_POST["sdrt_rsvp_enabler_nonce"], basename(__FILE__)) )
        return $post_id;
    if( !current_user_can("edit_post", $post_id) )
        return $post_id;
    if( defined("DOING_AUTOSAVE") && DOING_AUTOSAVE )
        return $post_id;

    $slug = "tribe_events";

    if( $slug != $post->post_type )
        return $post_id;

    $rsvp_enable_value = 'disabled';
    $rsvps_limit_value = '30';
    $rsvp_form_value = '';
    $logged_in_status = 'yes';

    if(isset($_POST["enable_rsvps"]))
    {
        $rsvp_enable_value = $_POST["enable_rsvps"];
    }
    update_post_meta($post_id, "enable_rsvps", $rsvp_enable_value);

    if(isset($_POST["rsvp_form"]))
    {
        $rsvp_form_value = $_POST["rsvp_form"];
    }
    update_post_meta($post_id, "rsvp_form", $rsvp_form_value);

    if(isset($_POST["rsvps_limit"]))
    {
        $rsvps_limit_value = $_POST["rsvps_limit"];
    }
    update_post_meta($post_id, "rsvps_limit", $rsvps_limit_value );

    if(isset($_POST["logged_in_status"]))
    {
        $logged_in_status = $_POST["logged_in_status"];
    }
    update_post_meta($post_id, "logged_in_status", $logged_in_status );

}
