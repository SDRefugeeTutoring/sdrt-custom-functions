<?php

function sdrt_trigger_email( $emailargs ) {

    $defaults = array(
        'fromname'      => 'San Diego Refugee Tutoring',
        'fromaddress'   => 'info@sdrefugeetutoring.com',
        'toemail'       => 'info@mattcromwell.com',
        'tosubject'     => 'New Email',
        'emailcontent'  => ''
    );

    $args = array_merge( $defaults, $emailargs);

    $sendmail = new Sendmail();
    $sendmail->_from_name = $args['fromname'];
    $sendmail->_from_address = $args['fromaddress'];

    // Let's go send email
    $sendmail->send($args['toemail'], $args['tosubject'], $args['emailcontent']);

}