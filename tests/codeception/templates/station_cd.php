<?php

/**
 * 駅名マスタテンプレート
 * @var $faker \Faker\Generator
 * @var $index integer
 */
return [
    'railroad_company_cd' => $faker->numberBetween(1, 30),
    'railroad_company_name' => $faker->text(10),
    'route_cd' => $faker->numberBetween(1, 100),
    'route_name' => $faker->text(20),
    'station_cd' => $index + 1,
    'station_name' => $faker->text(5),
    'station_name_kana' => $faker->text(6),
    'sort_no' => $faker->numberBetween(1, 100),
    'pref_cd' => $faker->numberBetween(1, 4),
    'valid_chk' => $faker->numberBetween(0, 1),
];
