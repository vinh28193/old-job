<?php

/**
 * オプション検索キーテストテンプレート
 * @var $faker \Faker\Generator
 * @var $index integer
 */
return [
    'tenant_id' => $faker->numberBetween(1, 2),
    'job_master_id' => $faker->numberBetween(1, 10),
    'option_search_cd_id' => $faker->numberBetween(1, 50),
];
