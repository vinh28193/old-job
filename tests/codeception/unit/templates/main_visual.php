<?php
/**
 * Created by PhpStorm.
 * User: proseeds
 * Date: 2018/01/19
 * Time: 15:29
 *
 * main_visualのfaker template
 * areaと同レコード（8レコード/tenant）入れる
 * 5テナントで40レコード
 * @var $faker \Faker\Generator
 * @var $index integer
 */

$recordPerTenant = \tests\codeception\fixtures\AreaFixture::RECORDS_PER_TENANT;
$id = $index + 1;
$tenantId = (int)($index / $recordPerTenant) + 1;
return [
    'id' => $id,
    'tenant_id' => $tenantId,
    'area_id' => $id,
    'type' => $faker->randomElement(['banner', 'slide']),
    'valid_chk' => $faker->boolean(),
    'memo' => $faker->text(),
    'created_at' => time(),
    'updated_at' => time(),
];
