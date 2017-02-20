<?php
/**
 * Adds custom User Meta to User Profile
 * But only Admin can edit it
 */

add_action( 'show_user_profile', 'sdrt_user_meta_fields' );
add_action( 'edit_user_profile', 'sdrt_user_meta_fields' );

function sdrt_user_meta_fields( $user ) { ?>
	<?php
	    $background_check = get_user_meta( $user->ID, 'background_check', true );
	?>

	<h2>Volunteer Profile Information</h2>
	<?php

	if ( ! current_user_can( 'update_plugins' ) ) : ?>
		<table class="form-table">
			<tbody>
			<tr>
				<?php if ( $background_check == 'No' || empty($background_check) ) { ?>
					<td colspan="2" style="background: #fc8585; border-radius: 6px; padding: 10px 20px;">
                        <p><strong>We do not currently have a background on file for you. Please submit your background check to us by emailing us at <a  href="mailto:info@sdrefugeetutoring.com">info@sdrefuegeetutoring.com</a></strong></p>
					</td>
				<?php } elseif ($background_check == 'Yes') { ?>
					<td colspan="2" style="background: #96dd99; border-radius: 6px; padding: 10px 20px;">
						<p><strong>We have a background check on file for you. Great! You're set to volunteer whenever you
                                like.</strong></p>
					</td>
				<?php } ?>
			</tr>
			</tbody>
		</table>

	<?php else : ?>

		<table class="form-table">
			<tbody>
			<tr>
				<th>
					<label for="background_check">Background check on file?</label>
				</th>

				<td>
					<p><input type="radio" name="background_check"
					          value="Yes" <?php  checked( $background_check, 'Yes' ); ?>>
						Yes
						<input type="radio" name="background_check"
						       value="No" <?php  checked( $background_check, 'No' ); ?>>
						No</p>
					<p class="description">Indicates whether we have a background check on file for this volunteer or
						not.</p>
				</td>
			</tr>
			</tbody>
		</table>
	<?php endif;
}

add_action( 'personal_options_update', 'sdrt_save_user_meta_fields' );
add_action( 'edit_user_profile_update', 'sdrt_save_user_meta_fields' );

function sdrt_save_user_meta_fields( $user_id ) {

	if ( !current_user_can( 'update_plugins', $user_id ) )
		return false;

	/* Copy and paste this line for additional fields. Make sure to change 'twitter' to the field ID. */
	update_user_meta( $user_id, 'background_check', $_POST['background_check'] );
}