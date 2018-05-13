<?php

return [
    [
        'parent' => 'isOwnClient',
        'child' => 'clientDetail',
    ],
    [
        'parent' => 'isOwnCorp',
        'child' => 'corpDetail',
    ],
    [
        'parent' => 'client_admin',
        'child' => 'isOwnApplication',
    ],
    [
        'parent' => 'corp_admin',
        'child' => 'client_admin',
    ],
    [
        'parent' => 'client_admin',
        'child' => 'isOwnClient',
    ],
    [
        'parent' => 'corp_admin',
        'child' => 'isOwnCorp',
    ],
    [
        'parent' => 'client_admin',
        'child' => 'isOwnJob',
    ],
    [
        'parent' => 'isOwnApplication',
        'child' => 'updateApplication',
    ],
    [
        'parent' => 'isOwnClient',
        'child' => 'updateClient',
    ],
    [
        'parent' => 'isOwnJob',
        'child' => 'updateJob',
    ],
    [
        'parent' => 'owner_admin',
        'child' => 'corp_admin',
    ],
];