<?php

/**
 * ウィジェットテンプレート
 * @var $faker \Faker\Generator
 * @var $index integer
 */
return [
    'id' => $index + 1,
    'tenant_id' => $faker->numberBetween(1, 2),
    'widget_no' => $faker->numberBetween(1, 10),
    'widget_name' => $faker->text(20),
    'element1' => $faker->numberBetween(0, 5),
    'element2' => $faker->numberBetween(0, 5),
    'element3' => $faker->numberBetween(0, 5),
    'element4' => $faker->numberBetween(0, 5),
    'valid_chk' => $faker->numberBetween(0, 1),
    'widget_layout_id' => $faker->numberBetween(1, 10),
    'sort' => $faker->randomNumber(),
];
