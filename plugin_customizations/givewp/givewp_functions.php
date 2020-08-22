<?php

/*
 *  Customizations for the GiveWP plugin
 *
 */
 
 add_filter('give_global_script_vars', 'custom_max_amount_error_message', 10, 1);

function custom_max_amount_error_message($localize_give_vars) {

	//$localize_give_vars = array(); 

	$localize_give_vars['bad_maximum'] = 'For large donation amounts, please use the "Mail a Check" option, particularly for any amount over ';

	return $localize_give_vars;
}

/*
 * Save updated user info on the Profile page IF new userdata is being passed over _POST method
 * 
 */

 add_action('give_profile_editor_before', 'maybe_save_volunteer_reqs_updated_data');

 function maybe_save_volunteer_reqs_updated_data() {
	if ( ( !is_user_logged_in() ) || ! isset( $_POST['sdrt_reqs_nonce_field'] ) || ! wp_verify_nonce( $_POST['sdrt_reqs_nonce_field'], 'sdrt_reqs_nonce_action' )) {
		$agreed = 'Sorry! No nonce here!';
	} else {
	// process form data
		$user_id       	= get_current_user_id();
		$coc_agree 		= sanitize_text_field($_POST['sdrt_coc_agree']);
		$zoom_agree 	= sanitize_text_field($_POST['sdrt_vol_release_agree']);

		/* Copy and paste this line for additional fields. */
		update_user_meta( $user_id, 'sdrt_coc_agree', $coc_agree );
		update_user_meta( $user_id, 'sdrt_vol_release_agree', $zoom_agree );
	}
	
	/*echo '<h3>$_POST Data</h3>';
	var_dump( $_POST );
	
	echo '<h3>User Meta</h3>';
	$all_meta_for_user = get_user_meta( $user_id );
	var_dump( $all_meta_for_user );
	*/
 }

/*
 *  Add the Volunteer Requirements section below the GiveWP Profile Editor
 * 
 */

add_action('give_profile_editor_after', 'sdrt_add_code_of_coduct_agreement');

function sdrt_add_code_of_coduct_agreement() {

	if ( ! is_user_logged_in() ) {
		return;
	} else {

	$user_id       = get_current_user_id();
	$user_meta = get_user_meta( $user_id );

	?>
	<div id="sdrt_volunteer_reqs_wrap" class="give-clearfix" style="width: 100%; clear: both;">
		<h2>Volunteer Requirements</h2>
		<p>We take the safety and health of our students very seriously. At the same time, we are extremely fortunate to have such a generous group of volunteers eager and willing to tutor. It's our goal to ensure the safety of our students and also make volunteering have as low a barrier to entry as possible.</p>

		<p>To volunteer, we currently require the following:</p>
			<ul>
				<li>Acceptance of our Code of Conduct</li>
				<li>Acceptance of our Virtual Tutoring Code of Conduct</li>
				<li>Attendance to a one-hour orientation session</li>
				<li>Pass an online background check.</li>
			</ul>
		<p>Each of those items and your current status are listed below. For any questions about your status below, please feel free to reach out to our volunteer coordinator via our <a href="<?php echo get_home_url(); ?>/contact-us" target="_blank" rel="noopener noreferrer">Contact Form</a>.</p>
		
		<?php sdrt_user_meta_fields( $user_meta ); ?>
	</div>
	<?php 
	} // end if_is_user_logged_in();
}