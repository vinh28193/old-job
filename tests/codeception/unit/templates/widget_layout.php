<?php
/**
 * @var $faker \Faker\Generator
 * @var $index integer
 * 12レコード作る想定
 */
// セッティング
$recordPerTenant = \tests\codeception\unit\fixtures\WidgetLayoutFixture::RECORDS_PER_TENANT;
$id = $index + 1;
$tenantId = floor($index / $recordPerTenant) + 1;
$no = $id % $recordPerTenant ?: $recordPerTenant;
return [
    'id' => $index + 1,
    'tenant_id' => $tenantId,
    'area_flg' => $faker->numberBetween(0, 1),
    'widget_layout_no' => $no,
];
