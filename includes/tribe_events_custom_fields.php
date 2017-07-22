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
    $rsvps_limit = get_post_meta($post->ID, "rsvps_limit", true );

    if (empty($rsvp_enable)) {$rsvp_enable == 'enabled';}
    if (empty($rsvps_limit)) {$rsvps_limit == '0';}
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
            $('input[type="radio"]').click(function() {
                if($(this).attr('value') == 'enabled') {
                    $('#rsvps_limit_option_wrap').show();
                }

                else {
                    $('#rsvps_limit_option_wrap').hide();
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

    <div id="rsvps_limit_option_wrap" class="rsvp_options limiter">
        <label for="rsvps_limit" class="main_label">Limit RSVPs</label>
        <input name="rsvps_limit" type="number" value="<?php echo $rsvps_limit; ?>" id="rsvps_limit">
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
    $rsvp_enable_value = '';
    $rsvps_limit_value = '';

    if(isset($_POST["enable_rsvps"]))
    {
        $rsvp_enable_value = $_POST["enable_rsvps"];
    }
    update_post_meta($post_id, "enable_rsvps", $rsvp_enable_value);

    if(isset($_POST["rsvps_limit"]))
    {
        $rsvps_limit_value = $_POST["rsvps_limit"];
    }
    update_post_meta($post_id, "rsvps_limit", $rsvps_limit_value );

}
