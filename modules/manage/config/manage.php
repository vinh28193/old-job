<?php
/**
 * Created by IntelliJ IDEA.
 * User: ueda
 * Date: 15/10/06
 * Time: 19:14
 */
return [
    'app' => [
        'homeUrl' => [
            '/manage/secure/index'
        ],
    ],
    'components' => [
        'errorHandler' => [
            'errorAction' => '/manage/default/error',
        ],
        'user' => [
            'identityClass' => 'app\modules\manage\models\Manager',
            'loginUrl' => '/manage/login',
//            'autoRenewCookie' => true,
            'enableAutoLogin' => false,
        ],
        'session' => [
            'sessionTable' => 'manager_session',
            'name' => 'ADMIN_SSID',
            'timeout' => 60*60*24,
        ],
    ],
];