<?php
/**
 * @var $faker \Faker\Generator
 * @var $index integer
 * null部分は手動で補完
 */
return [
    'id' => $index + 1,
    'tenant_id' => null,
    'access_date' => null,
    'detail_count_pc' => $faker->numberBetween(10, 100),
    'application_count_pc' => $faker->numberBetween(10, 100),
    'member_count_pc' => $faker->numberBetween(10, 100),
    'detail_count_smart' => $faker->numberBetween(10, 100),
    'application_count_smart' => $faker->numberBetween(10, 100),
    'member_count_smart' => $faker->numberBetween(10, 100),
];