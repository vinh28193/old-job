<?php
/**
 * @var $faker \Faker\Generator
 * @var $index integer
 * 20レコード作る想定
 */
// セッティング
$recordPerTenant = \tests\codeception\unit\fixtures\WidgetFixture::RECORDS_PER_TENANT;
$id = $index + 1;
$tenantId = floor($index / $recordPerTenant) + 1;
$no = $id % $recordPerTenant ?: $recordPerTenant;
// widgetTypeとelementの生成
$widgetType = $faker->boolean(10) ? 1 : 0;
if ($widgetType == 1) {
    $element=[5,0,0,0];
} else {
    $elementNo = [0,1,2,3,4];
    $element[0] = $faker->numberBetween(1, 4);
    for ($i = 1; $i <= 3; $i++) {
        if ($element[$i - 1]) {
            unset($elementNo[$element[$i - 1]]);
            $element[$i] = $faker->randomElement($elementNo);
        } else {
            $element[$i] = 0;
        }
    }
}
// テナントのwidgetLayoutIdのみを入れる
$recordPerTenant = \tests\codeception\unit\fixtures\WidgetLayoutFixture::RECORDS_PER_TENANT;
$widgetLayoutId = $faker->numberBetween(($tenantId - 1) * $recordPerTenant + 1, $tenantId * $recordPerTenant);
return [
    'id' => $index + 1,
    'tenant_id' => $tenantId,
    'widget_no' => $no,
    'widget_name' => $faker->realText(255),
    'widget_type' => $widgetType,
    'element1' => $element[0],
    'element2' => $element[1],
    'element3' => $element[2],
    'element4' => $element[3],
    'valid_chk' => $faker->numberBetween(0, 1),
    'widget_layout_id' => $widgetLayoutId,
    'is_disp_widget_name' => $faker->numberBetween(0, 1),
];
