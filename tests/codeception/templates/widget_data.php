<?php

/**
 * ウィジェットデータテンプレート
 * @var $faker \Faker\Generator
 * @var $index integer
 */
//日付データの生成
$day = 86400;
$startTimestamp = time() + $faker->numberBetween(-30, 30) * $day;
$endTimestamp = $startTimestamp + $faker->numberBetween(0, 30) * $day;

return [
    'id' => $index + 1,
    'tenant_id' => $faker->numberBetween(1, 2),
    'widget_id' => $faker->numberBetween(1, 10),
    'title' => $faker->title,
    'pict' => $faker->word . '.png',
    'description' => $faker->realText(50),
    'movie_tag' => $faker->text(200),
    'url' => $faker->url,
    'sort' => $faker->randomNumber(),
    'disp_start_date' => date("Y-m-d", $startTimestamp),
    'disp_end_date' => date("Y-m-d", $endTimestamp),
    'valid_chk' => $faker->numberBetween(0, 1),
];
