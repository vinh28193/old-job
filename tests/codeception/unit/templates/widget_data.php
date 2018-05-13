<?php
/**
 * @var $faker \Faker\Generator
 * @var $index integer
 * 60レコード作る想定
 */
// セッティング
use tests\codeception\unit\fixtures\WidgetDataFixture;
use tests\codeception\unit\fixtures\WidgetFixture;

$recordPerTenant = WidgetDataFixture::RECORDS_PER_TENANT;
$id = $index + 1;
$tenantId = floor($index / $recordPerTenant) + 1;
$no = $id % $recordPerTenant ?: $recordPerTenant;
// テナントのwidgetIdのみを入れる
$recordPerTenant = WidgetFixture::RECORDS_PER_TENANT;
$widgetId = $faker->numberBetween(($tenantId - 1) * $recordPerTenant + 1, $tenantId * $recordPerTenant);
// 日付生成
$year = 60 * 60 * 24 * 365;
$dispStartDate = $faker->numberBetween(time() - ($year * 2), time() + $year);
$dispEndDate = $faker->numberBetween($dispStartDate, 2147483647);

return [
    'id' => $index + 1,
    'tenant_id' => $tenantId,
    'widget_id' => $widgetId,
    'title' => $faker->realText(255),
    'pict' => $faker->text(255),
    'description' => $faker->realText(255),
    'sort' => $faker->numberBetween(1, 5),
    'disp_start_date' => $dispStartDate,
    'disp_end_date' => $dispEndDate,
    'valid_chk' => $faker->numberBetween(0, 1),
];
