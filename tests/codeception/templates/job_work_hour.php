<?php

/**
 * 希望勤務時間テストテンプレート
 * @var $faker \Faker\Generator
 * @var $index integer
 */
return [
    'tenant_id' => $faker->numberBetween(1, 2),
    'job_master_id' => $faker->numberBetween(1, 10),
    'work_hour_cd_id' => $faker->numberBetween(1, 50),
];
