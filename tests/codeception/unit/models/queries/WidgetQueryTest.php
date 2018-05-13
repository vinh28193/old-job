<?php
namespace models\queries;

use app\models\manage\searchkey\Area;
use tests\codeception\unit\JmTestCase;
use app\models\manage\Widget;
use app\models\manage\WidgetData;

/**
 * Created by PhpStorm.
 * User: User
 * Date: 24/6/2017
 * Time: 10:26 AM
 */
class WidgetQueryTest extends JmTestCase
{
    /**
     * WidgetQuery::innerJoinWithData()のtest
     * fixtureのデータが下記条件を満たしていることに依存
     * 全国エリアと任意の有効なエリアに表示するwidgetがそれぞれ存在すること
     */
    public function testInnerJoinWithData()
    {
        $this->specify('任意の有効なエリアで検証', function () {
            /** @var Area $area */
            $area = Area::find()->where(['valid_chk' => Area::FLAG_VALID])->one();

            $widgets = Widget::find()->innerJoinWithData($area->id)->all();
            verify($widgets)->notEmpty();

            //取得したウィジェットに表示してはいけないデータが混じっていないかをチェック
            $this->checkWidgets($widgets, $area->id);
        });

        $this->specify('全国エリアで検証', function () {
            $widgets = Widget::find()->innerJoinWithData(Area::NATIONWIDE_ID)->all();
            verify($widgets)->notEmpty();

            //取得したウィジェットに表示してはいけないデータが混じっていないかをチェック
            $this->checkWidgets($widgets, Area::NATIONWIDE_ID);
        });
    }

    /**
     * widgetの配列が全て表示していいものかどうかを検証する
     * @param Widget[] $widgets
     * @param $areaId
     */
    public function checkWidgets($widgets, $areaId)
    {
        $now = time();
        $today = strtotime('today');
        foreach ($widgets as $widget) {
            // widgetの満たすべき条件
            verify($widget->widgetData)->notEmpty();
            verify($widget->valid_chk)->equals(Widget::VALID);

            foreach ($widget->widgetData as $widgetData) {
                // widgetDataの満たすべき条件
                verify($widgetData->widgetDataArea)->notEmpty();
                verify($widgetData->valid_chk)->equals(WidgetData::VALID);
                verify($widgetData->disp_start_date)->lessOrEquals($now);
                if ($widgetData->disp_end_date !== null) {
                    verify($widgetData->disp_end_date)->greaterOrEquals($today);
                }

                foreach ($widgetData->widgetDataArea as $widgetDataArea) {
                    // widgetDataAreaの満たすべき条件
                    verify($widgetDataArea->area_id)->equals($areaId);
                }
            }
        }
    }
}
