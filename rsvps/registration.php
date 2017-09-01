<?php
/**
 * Interacting with the Checkr.io API
 * All functions necessary for volunteer registration are here
 *
 */

/**
 * Create a candidate on Checkr.io
 */

function sdrt_checkr_create_candidate($response) {
    $data = array(
        'first_name'        => 'John',
        'no_middle_name'    => true,
        'last_name'         => 'Smith',
        'email'             => 'john.smith@gmail.com',
        'dob'               => '1970-01-22',
        'ssn'               => '111-11-2001',
        'zipcode'           => '90401',
        'headers'           => array(
            'Authorization' => 'Basic ' . base64_encode( '38901bf2df04e0818895f7a4b9af79b849179d5d'  . ':' . '' )
        )
    );

    $response = wp_remote_post( 'https://api.checkr.com/v1/candidates', array( 'data' => $data ) );

    return $response;
}