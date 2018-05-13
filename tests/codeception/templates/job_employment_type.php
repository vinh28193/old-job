<?php

/**
 * 雇用形態テストテンプレート
 * @var $faker \Faker\Generator
 * @var $index integer
 */
return [
    'tenant_id' => $faker->numberBetween(1, 2),
    'job_master_id' => $faker->numberBetween(1, 10),
    'employment_type_cd_id' => $faker->numberBetween(1, 50),
];
