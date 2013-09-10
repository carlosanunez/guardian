<?php

return array(

    'models' => array(
        
        'user' => 'Elphie\Guardian\Models\User'
    ),

    'throttle' => array(

        'enabled' => true,

        'max_throttling_limit' => '3',

        'throttling_delay_interval' => '10',

        'show_captcha' => true
    )

);