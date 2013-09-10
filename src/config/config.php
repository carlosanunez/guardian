<?php

return array(

    'models' => array(
        
        'user' => 'Elphie\Guardian\Models\User'
    ),

    'throttle' => array(

        'enabled' => true,

        'throttling_time_interval' => '15',

        'max_throttling_limit' => '3',

        'throttling_delay_interval' => '10',
    )

);