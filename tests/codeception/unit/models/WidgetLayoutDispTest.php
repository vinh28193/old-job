<?php

namespace models\manage;

use app\models\manage\searchkey\Area;
use app\models\manage\Widget;
use app\models\manage\WidgetData;
use app\models\manage\WidgetLayout;
use tests\codeception\unit\fixtures\WidgetDataAreaFixture;
use tests\codeception\unit\fixtures\WidgetDataFixture;
use tests\codeception\unit\fixtures\WidgetFixture;
use tests\codeception\unit\JmTestCase;
use app\models\WidgetLayoutDisp;
use tests\codeception\unit\fixtures\WidgetLayoutFixture;

/**
 * @group widgets
 */
class WidgetLayoutDispTest extends JmTestCase
{
    /**
     * フィクスチャ設定
     * @return array
     */
    public function fixtures()
    {
        return [
            'widget_layout' => WidgetLayoutFixture::className(),
            'widget' => WidgetFixture::className(),
            'widget_data' => WidgetDataFixture::className(),
            'widget_data_area' => WidgetDataAreaFixture::className(),
        ];
    }

    /**
     * フィクスチャロード
     */
    public function setUp()
    {
        parent::setUp();
    }

    /**
     * リレーションテスト
     */
    public function testRelations()
    {
        $model = WidgetLayoutDisp::findOne($this->id(1, 'widget_layout'));
        $this->specify('ウィジェット', function () use ($model) {
            verify(count($model->widget))->equals(2);
        });
    }

    /**
     * 表示ウィジェットレイアウト取得テスト
     * fixtureのデータが下記条件を満たしていることに依存
     * 全国エリアと任意の有効なエリアに表示するwidgetがそれぞれ存在すること
     * 全国エリアにしか表示しない設定のlayoutが存在すること
     */
    public function testGetShowWidgetLayouts()
    {
        $this->specify('任意の有効なエリアで検証', function () {
            /** @var Area $area */
            $area = Area::find()->where(['valid_chk' => Area::FLAG_VALID])->one();
            $layouts = WidgetLayoutDisp::getShowWidgetLayouts($area->id);
            verify($layouts)->notEmpty();

            //取得したウィジェットに表示してはいけないデータが混じっていないかをチェック
            foreach ($layouts as $layout) {
                // widgetLayoutの満たすべき条件
                verify($layout->widget)->notEmpty();
                $this->checkWidgets($layout->widget, $area->id);
                verify($layout->area_flg)->equals(WidgetLayout::AREA_COMMON);
            }
        });

        $this->specify('全国エリアで検証', function () {
            $areaNationwideExists = false;
            $layouts = WidgetLayoutDisp::getShowWidgetLayouts(Area::NATIONWIDE_ID);
            verify($layouts)->notEmpty();

            //取得したウィジェットに表示してはいけないデータが混じっていないかをチェック
            foreach ($layouts as $layout) {
                // widgetLayoutの満たすべき条件
                verify($layout->widget)->notEmpty();
                $this->checkWidgets($layout->widget, Area::NATIONWIDE_ID);
                if (!$layout->area_flg) {
                    $areaNationwideExists = true;
                }
            }
            // 全国エリアのみ表示のwidgetLayoutが一つ以上取得されている
            verify($areaNationwideExists)->true();
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
