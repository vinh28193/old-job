<?php
//admin_master.php

/**
 * @var $faker \Faker\Generator
 * @var $index integer
 * tenant_id=1と2を10レコードずつ作る想定
 * 電話番号は整形の必要あり
 * id=1,2,3はそれぞれ権限テストで使うため、整形の必要あり（詳しくはdata参照）
 */
return [
    'id' => $index + 1,
    'tenant_id' => $index < 10 ? 1 : 2,
    'admin_no' => ($index + 1) % 10,
    'corp_master_id' => $faker->numberBetween(null, 10),
    'login_id' => $faker->text(10),
    'password' => $faker->text(10),
    'created_at' => $faker->unixTime,
    'valid_chk' => $faker->numberBetween(0, 1),
    'name_sei' => $faker->text(10),
    'name_mei' => $faker->text(10),
    'tel_no' => $faker->phoneNumber,
    'client_master_id' => $faker->numberBetween(null, 10),
    'mail_address' => $faker->email,
    'option100' => $faker->realText(),
    'option101' => $faker->numberBetween(1, 10),
    'option102' => $faker->email,
];
