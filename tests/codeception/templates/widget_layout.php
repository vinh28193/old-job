<?php

/**
 * ウィジェットレイアウトテンプレート
 * @var $faker \Faker\Generator
 * @var $index integer
 */
return [
    'id' => $index + 1,
    'tenant_id' => $faker->numberBetween(1, 2),
    'area_flg' => $faker->numberBetween(0, 1),
    'widget_layout_no' => $index + 1,
];
