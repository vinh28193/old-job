<?php
$settingFile = Yii::getAlias('@runtime') . '/setting/' . $_SERVER['HTTP_HOST'] . '.json';
$tenantRules = [];
if (file_exists($settingFile)) {
    $tenantRules = json_decode(file_get_contents($settingFile), true);
}

$topRules = [
    'manage/login'                                       => 'manage/default/login',
    'manage/logout'                                      => 'manage/default/logout',
    'manage/error'                                       => 'manage/default/error',
    'manage/secure'                                      => 'manage/secure/index',
    'manage/secure/searchkey<no:[\d-]+>/<action:[\w-]+>' => 'manage/secure/searchkey/<action>',
    'manage/preview/<job_no:[\d-]+>'                     => 'kyujin/preview',
    'manage/preview/'                                    => 'kyujin/preview',
    'manage/top/preview/'                                => 'top/preview',
    '/'                                                  => 'top/index',
    'sitemap_index.xml'                                  => 'sitemap/index',
    'systemdata/<param:.*>'                              => 'systemdata/index',
];

$defaultRules = [
    'contents/<urlDirectory:[\w-]+>'                      => 'contents/index',
    'apply/<job_no:\d+>'                                  => 'apply/index',
    '<controller:[\w-]+>'                                 => '<controller>/index',
    '<controller:[\w-]+>/<action:[\w-]+>'                 => '<controller>/<action>',
    '<module:[\w-]+>/<controller:[\w-]+>'                 => '<module>/<controller>/index',
    '<module:[\w-]+>/<controller:[\w-]+>/<action:[\w-]+>' => '<module>/<controller>/<action>',
];

return array_merge(
    $topRules,
    [
        // 検索結果独自
        [
            'class'      => 'app\components\JobsUrlRule',
            'job'        => $tenantRules['job'],
            'areas'      => $tenantRules['areas'],
            'conditions' => $tenantRules['conditions'],
        ],
    ],
    $defaultRules
);
