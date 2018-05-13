<?php

namespace models\manage;

use app\models\manage\WidgetData;
use app\models\manage\WidgetDataSearch;
use tests\codeception\unit\JmTestCase;
use tests\codeception\unit\fixtures\WidgetDataFixture;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;

/**
 * @group widgets
 * @property WidgetDataFixture $widget_data
 */
class WidgetDataSearchTest extends JmTestCase
{
    /**
     * 要素テスト
     */
    public function testAttributeLabels()
    {
        $model = new WidgetDataSearch();
        verify(count($model->attributeLabels()))->notEmpty();
    }

    /**
     * ルールテスト
     */
    public function testRules()
    {
        $this->specify('数字チェック', function () {
            $model = new WidgetDataSearch();
            $model->load([
                $model->formName() => [
                    'widget_id' => '文字列',
                    'valid_chk' => '文字列',
                    'areaId' => '文字列',
                ],
            ]);
            $model->validate();
            verify($model->hasErrors('widget_id'))->true();
            verify($model->hasErrors('valid_chk'))->true();
            verify($model->hasErrors('areaId'))->true();
        });
        $this->specify('boolチェック', function () {
            $model = new WidgetDataSearch();
            $model->load([
                $model->formName() => [
                    'valid_chk' => 10,
                ],
            ]);
            $model->validate();
            verify($model->hasErrors('valid_chk'))->true();
        });
        $this->specify('文字列チェック', function () {
            $model = new WidgetDataSearch();
            $model->load([
                $model->formName() => [
                    'searchText' => 1,
                    'searchItem' => 1,
                ],
            ]);
            $model->validate();
            verify($model->hasErrors('searchText'))->true();
            verify($model->hasErrors('searchItem'))->true();
        });
        $this->specify('日付チェック', function () {
            $model = new WidgetDataSearch();
            $model->load([
                $model->formName() => [
                    'startFrom' => '文字列',
                    'startTo' => [1, 12],
                ],
            ]);
            $model->validate();
            verify($model->hasErrors('startFrom'))->true();
            verify($model->hasErrors('startTo'))->true();
        });
        $this->specify('正しい値', function () {
            $model = new WidgetDataSearch();
            $model->load([
                $model->formName() => [
                    'widget_id' => 1,
                    'areaId' => 1,
                    'valid_chk' => 1,
                    'searchText' => 'all',
                    'searchItem' => 'st',
                    'types' => [0, 2],
                    'startFrom' => '2015/04/27',
                    'startTo' => '2017/04/01',
                ],
            ]);
            verify($model->validate())->true();
        });
    }

    /**
     * 検索テスト
     */
    public function testSearch()
    {
        $this->specify('初期状態で検索', function () {
            $models = $this->getWidgetDataSearch([
                'searchItem' => 'all',
                'searchText' => '',
                'widget_id' => '',
                'types' => '',
                'startFrom' => '',
                'startTo' => '',
                'valid_chk' => '',
                'areaId' => '',
            ]);
            verify($models)->notEmpty();
            verify($models)->count((int)WidgetData::find()->count());
        });

        $this->specify('クリアボタン押下時', function () {
            $models = $this->getWidgetDataSearch(1);
            verify($models)->notEmpty();
            verify($models)->count((int)WidgetData::find()->count());
        });

        $this->specify('キーワード（title）で検索', function () {
            /** @var WidgetData $sample */
            $sample = WidgetData::find()->one();
            $keyWord = mb_substr($sample->title, 3, 5);
            $models = $this->getWidgetDataSearch([
                'searchItem' => 'title',
                'searchText' => $keyWord,
            ]);
            verify($models)->notEmpty();
            foreach ($models as $model) {
                verify(ArrayHelper::getValue($model, 'title'))->contains($keyWord);
            }
        });

        $this->specify('キーワード(all）で検索', function () {
            /** @var WidgetData $sample */
            $sample = WidgetData::find()->one();
            $keyWord = mb_substr($sample->title, 3, 5);
            $models = $this->getWidgetDataSearch([
                'searchItem' => 'all',
                'searchText' => $keyWord,
            ]);
            verify($models)->notEmpty();
            foreach ($models as $model) {
                $title = ArrayHelper::getValue($model, 'title');
                $description = ArrayHelper::getValue($model, 'description');
                verify(strpos($title, $keyWord) !== false || strpos($description, $keyWord) !== false)->true();
            }
        });

        $this->specify('widget_idで検索', function () {
            $widgetId = $this->id(2, 'widget');

            $models = $this->getWidgetDataSearch([
                'searchItem' => 'all',
                'widget_id' => $widgetId,
            ]);
            verify($models)->notEmpty();
            foreach ($models as $model) {
                verify(ArrayHelper::getValue($model, 'widget_id'))->equals($widgetId);
            }
        });

        $this->specify('公開開始日で検索', function () {
            $models = $this->getWidgetDataSearch([
                'searchItem' => 'all',
                'startFrom' => '1995/01/17',
            ]);
            verify($models)->notEmpty();
            foreach ($models as $model) {
                verify(ArrayHelper::getValue($model, 'disp_start_date'))->greaterOrEquals(strtotime('1995-01-17'));
            }
            $models = $this->getWidgetDataSearch([
                'searchItem' => 'all',
                'startTo' => '2017/09/16',
            ]);
            verify($models)->notEmpty();
            foreach ($models as $model) {
                verify(ArrayHelper::getValue($model, 'disp_start_date'))->lessOrEquals(strtotime('2017-09-16'));
            }
        });

        $this->specify('valid_chkで検索', function () {
            $models = $this->getWidgetDataSearch([
                'searchItem' => 'all',
                'valid_chk' => 1,
                'areaId' => '',
            ]);
            verify($models)->notEmpty();
            foreach ($models as $model) {
                verify(ArrayHelper::getValue($model, 'valid_chk'))->equals(1);
            }
        });

        $this->specify('areaIdで検索', function () {
            $areaId = $this->id(2, 'area');

            $models = $this->getWidgetDataSearch([
                'searchItem' => 'all',
                'areaId' => $areaId,
            ]);
            verify($models)->notEmpty();
            foreach ($models as $model) {
                $widgetDataAreas = ArrayHelper::getValue($model, 'widgetDataArea');
                $areaIds = ArrayHelper::getColumn($widgetDataAreas, 'area_id');
                verify(ArrayHelper::isIn($areaId, $areaIds))->true();
            }
        });
    }

    /**
     * @param $searchParam
     * @return WidgetDataSearch[]
     */
    private function getWidgetDataSearch($searchParam)
    {
        $model = new WidgetDataSearch();
        $dataProvider = $model->search([$model->formName() => $searchParam]);

        return $dataProvider->query->all();
    }

    /**
     * deleteSearchメソッドのテスト
     */
    public function testDeleteSearch()
    {
        self::getFixtureInstance('widget_data')->initTable();
        self::getFixtureInstance('widget_data_area')->initTable();
        $widgetDataId1 = $this->id(1, 'widget_data');
        $widgetDataId2 = $this->id(2, 'widget_data');
        $searchModel = new WidgetDataSearch();
        $this->tester->canSeeInDatabase('widget_data_area', ['widget_data_id' => 1]);
        // allCheck無し、id=1のみ選択
        $post = [
            'gridData' => Json::encode([
                'searchParams' => [$searchModel->formName() => 1],
                'totalCount' => WidgetDataFixture::RECORDS_PER_TENANT,
                'selected' => [$widgetDataId1],
                'allCheck' => false,
            ]),
            $searchModel->formName() => 1,
        ];

        // deleteSearchで取得したmodelの内容検証
        $deleteModels = $searchModel->deleteSearch($post);
        verify($deleteModels)->count(1);
        verify($deleteModels[0]->id)->equals($widgetDataId1);
        // 削除
        $deleteCount = $searchModel->deleteAll(['id' => ArrayHelper::getColumn($deleteModels, 'id')]);
        // 削除件数の検証
        verify($deleteCount)->equals(1);
        // delete後にWidgetDataAreaテーブルに入っているレコードの内容の検証
        $this->tester->cantSeeInDatabase('widget_data_area', ['widget_data_id' => $widgetDataId1]);

        // allCheckあり、1,2のみ未選択（削除件数のみ検証）
        $post = [
            'gridData' => Json::encode([
                'searchParams' => [$searchModel->formName() => 1],
                'totalCount' => WidgetDataFixture::RECORDS_PER_TENANT,
                'selected' => [$widgetDataId1, $widgetDataId2],
                'allCheck' => true,
            ]),
            $searchModel->formName() => 1,
        ];
        $deleteModelss = $searchModel->deleteSearch($post);
        $deleteCount = $searchModel->deleteAll(['id' => $deleteModelss]);
        verify($deleteCount)->equals(WidgetDataFixture::RECORDS_PER_TENANT - 2);

        // 削除したものを元に戻す
        self::getFixtureInstance('widget_data')->initTable();
        self::getFixtureInstance('widget_data_area')->initTable();
    }
}
