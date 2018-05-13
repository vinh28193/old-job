<?php

/**
 * 市区町村テストテンプレート
 * @var $faker \Faker\Generator
 * @var $index integer
 */
return [
    'id' => $index + 1,
    'tenant_id' => 1,
    'pref_cd_id' => 1,
    'dist_name' => $faker->text(20),
    'valid_chk' => 1,
    'dist_sub_cd' => 1,
    'sort' => $index + 1,
    'dist_cd' => $index + 1,
];
