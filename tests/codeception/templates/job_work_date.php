<?php

/**
 * 希望勤務日付テストテンプレート
 * @var $faker \Faker\Generator
 * @var $index integer
 */
return [
    'tenant_id' => $faker->numberBetween(1, 2),
    'job_master_id' => $faker->numberBetween(1, 10),
    'work_date_cd_id' => $faker->numberBetween(1, 50),
];
