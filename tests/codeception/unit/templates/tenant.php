<?php
/**
 * @var $faker \Faker\Generator
 * @var $index integer
 */
return [
    'tenant_id' => $index + 1,
    'tenant_code' => $faker->domainWord,
    'tenant_name' => $faker->company,
    'language_code' => Yii::$app->language,
    'created_at'=> $faker->unixTime,
    'updated_at'=> $faker->unixTime,
];