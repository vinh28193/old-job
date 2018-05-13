<?php
/**
 * @var $faker \Faker\Generator
 * @var $index integer
 * 200レコード作る想定
 */
// セッティング
use tests\codeception\fixtures\AreaFixture;
use tests\codeception\unit\fixtures\WidgetDataAreaFixture;
use tests\codeception\unit\fixtures\WidgetDataFixture;

$recordPerTenant = WidgetDataAreaFixture::RECORDS_PER_TENANT;
$id = $index + 1;
$tenantId = floor($index / $recordPerTenant) + 1;
$no = $id % $recordPerTenant ?: $recordPerTenant;
// テナントのwidgetDataIdのみを入れる
$recordPerTenant = WidgetDataFixture::RECORDS_PER_TENANT;
$widgetDataId = $faker->numberBetween(($tenantId - 1) * $recordPerTenant + 1, $tenantId * $recordPerTenant);
// テナントのエリアidのみを入れる
$recordPerTenant = AreaFixture::RECORDS_PER_TENANT;
$areaId = $faker->numberBetween(($tenantId - 1) * $recordPerTenant + 1, $tenantId * $recordPerTenant);

return [
    'id' => $index + 1,
    'tenant_id' => $tenantId,
    'widget_data_id' => $widgetDataId,
    'area_id' => $areaId,
    'url' => $faker->url,
    'movie_tag' => $faker->text(255),
];
