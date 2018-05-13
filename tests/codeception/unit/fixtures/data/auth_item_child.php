<?php
/**
 * 権限系なので原則編集禁止
 */
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
        'child' => 'isOwnApplication',
    ],
    [
        'parent' => 'client_admin',
        'child' => 'isOwnClient',
    ],
    [
        'parent' => 'corp_admin',
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
        'parent' => 'corp_admin',
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
];