<?php
add_filter('gform_stripe_payment_intent_pre_create', function ($args, $feed, $submission_data, $form, $entry) {
    if ($form['id'] == 7) { 
        $args['payment_method_types'] = ['ideal'];
    }
    return $args;
}, 10, 5);
