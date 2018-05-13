<?php
/**
 * Created by PhpStorm.
 * User: proseeds
 * Date: 2018/01/19
 * Time: 15:29
 *
 * main_visual_imageのfaker template
 * main_visualのレコード1つにつき3レコード入れる
 * main_visualが8レコード/tenantなので5tenantだと120レコード
 * @var $faker \Faker\Generator
 * @var $index integer
 */

$recordPerTenant = \tests\codeception\fixtures\AreaFixture::RECORDS_PER_TENANT * 3;
$id = $index + 1;
$tenantId = (int)($index / $recordPerTenant) + 1;
$mainVisualId = (int)($index / 3) + 1;
$sort = $index % 3 + 1;
return [
    'id' => $id,
    'tenant_id' => $tenantId,
    'main_visual_id' => $mainVisualId,
    'file_name' => (new \DateTime('NOW'))->format('Y-m-d') . '_' . md5(uniqid()) . '.' . '.jpg',
    'file_name_sp' => (new \DateTime('NOW'))->format('Y-m-d') . '_' . md5(uniqid()) . '.' . '.jpg',
    'url' => $faker->url,
    'url_sp' => $faker->url,
    'content' => $faker->text(64),
    'sort' => $sort,
    'valid_chk' => $faker->boolean(),
    'memo' => $faker->text(),
    'created_at' => time(),
    'updated_at' => time(),
];
