<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Self-registration
    |--------------------------------------------------------------------------
    |
    | Whether people who sign up themselves can use the app straight away.
    | Set this to false to create them deactivated instead, so an administrator
    | has to approve each account on the staff page before it can clock in.
    |
    */

    'activate_signups_immediately' => env('HR_ACTIVATE_SIGNUPS', true),

];
