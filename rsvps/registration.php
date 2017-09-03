<?php
/**
 * Interacting with the Checkr.io API
 * All functions necessary for volunteer registration are here
 *
 */

/**
 * Create a candidate and trigger an invite on Checkr.io
 */

add_action( 'sdrt_trigger_checkr_invite', 'sdrt_checkr_create_invite', 10, 2 );

function sdrt_checkr_create_invite( $data ) {

    $candidate_args = array(
        'method'            => 'POST',
        'headers'           => array(
            'Authorization' => 'Basic ' . base64_encode( SDRT_CHECKR_API  . ':' . '' )
        ),
        'body'              => array(
            'first_name'        => $data['first_name'],
            'no_middle_name'    => true,
            'last_name'         => $data['last_name'],
            'email'             => $data['email_address'],
            'dob'               => $data['your_date_of_birth'],
        ),
//        'body'              => array(
//            'first_name'        => 'Matt',
//            'no_middle_name'    => true,
//            'last_name'         => 'Cromwell',
//            'email'             => 'testing@sdrefugeetutoring.com',
//            'dob'               => '1977-10-23',
//        ),
    );

    $candidate_response = wp_remote_request( 'https://api.checkr.com/v1/candidates',  $candidate_args );

    if ( is_wp_error( $candidate_response) ) {
        return false; // Bail early
    }

    $candidate_body = wp_remote_retrieve_body( $candidate_response );

    $candidate_data = json_decode( $candidate_body );

    if ( ! empty( $candidate_data ) ? $candidate_id = $candidate_data->id : $candidate_id = '' );

    $invite_args = array(
        'method'            => 'POST',
        'headers'           => array(
            'Authorization' => 'Basic ' . base64_encode( SDRT_CHECKR_API  . ':' . '' )
        ),
        'body'              => array(
            'package'       => 'tasker_standard',
            'candidate_id'  => $candidate_id,
        ),
    );

    $invite_response = wp_remote_request( 'https://api.checkr.com/v1/invitations',  $invite_args );

    if ( is_wp_error( $invite_response) ) {
        return false; // Bail early
    }

    $invite_body = wp_remote_retrieve_body( $invite_response );

    $invite_data = json_decode( $invite_body );

    $invite_url = esc_url($invite_data->invitation_url);

    Caldera_Forms::set_field_data( 'fld_468416', $invite_url, $data['__form_id'], $data['__entry_id'] );
}

