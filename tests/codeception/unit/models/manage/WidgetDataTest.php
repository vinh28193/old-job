<?php

namespace models\manage;

use Yii;
use app\models\manage\Widget;
use app\models\manage\WidgetData;
use app\models\manage\WidgetDataArea;
use tests\codeception\unit\JmTestCase;
use tests\codeception\unit\fixtures\WidgetDataFixture;
use yii\helpers\ArrayHelper;
use yii\web\UploadedFile;

/**
 * @group widgets
 * @property WidgetDataFixture $widget_data
 */
class WidgetDataTest extends JmTestCase
{
    /**
     * テーブル名テスト
     */
    public function testTableName()
    {
        $model = new WidgetData();
        verify($model->tableName())->equals('widget_data');
    }

    // initは非常に単純なメソッドなので省略

    /**
     * 要素テスト
     */
    public function testAttributeLabels()
    {
        $model = new WidgetData();
        verify(count($model->attributeLabels()))->notEmpty();
    }

    /**
     * ルールテスト
     */
    public function testRules()
    {
        $this->specify('数字チェック', function () {
            $model = $this->getModel([
                'tenant_id' => '文字列',
                'widget_id' => '文字列',
                'sort' => '文字列',
            ]);
            $model->validate();
            verify($model->hasErrors('tenant_id'))->true();
            verify($model->hasErrors('widget_id'))->true();
            verify($model->hasErrors('sort'))->true();
        });

        $this->specify('sort最大最小値チェック', function () {
            $model = $this->getModel(['sort' => 0]);
            $model->validate();
            verify($model->hasErrors('sort'))->true();

            $model = $this->getModel(['sort' => 256]);
            $model->validate();
            verify($model->hasErrors('sort'))->true();

            //sortの最小値
            $model = $this->getModel(['sort' => 1]);
            $model->validate();
            verify($model->hasErrors('sort'))->false();
        });

        $this->specify('boolチェック', function () {
            $model = $this->getModel(['valid_chk' => 10]);
            $model->validate();
            verify($model->hasErrors('valid_chk'))->true();
        });

        $this->specify('文字列チェック', function () {
            $model = $this->getModel([
                'description' => 1,
                'title' => 1,
                'movieTag' => [1, 2, 3],
            ]);
            $model->validate();
            verify($model->hasErrors('description'))->true();
            verify($model->hasErrors('title'))->true();
            verify($model->hasErrors('movieTag'))->true();
        });

        $this->specify('文字列最大値チェック(url以外)', function () {
            $model = $this->getModel([
                'description' => str_repeat('a', 201),
                'title' => str_repeat('a', 101),
                'movieTag' => str_repeat('a', 256),
            ]);
            $model->validate();
            verify($model->hasErrors('description'))->true();
            verify($model->hasErrors('title'))->true();
            verify($model->hasErrors('movieTag'))->true();
        });

        $this->specify('日付チェック', function () {
            $model = $this->getModel([
                'disp_start_date' => '文字列',
                'disp_end_date' => 123456,
            ]);
            $model->validate();
            verify($model->hasErrors('disp_start_date'))->true();
            verify($model->hasErrors('disp_end_date'))->true();
        });

        $this->specify('compare', function () {
            $model = $this->getModel([
                'disp_start_date' => '2017-06-28',
                'disp_end_date' => '2017-06-27',
            ]);
            $model->validate();
            verify($model->hasErrors('disp_end_date'))->true();
        });

        $this->specify('共通必須チェック', function () {
            $model = $this->getModel();
            $model->validate();
            verify($model->hasErrors('widget_id'))->true();
            verify($model->hasErrors('disp_start_date'))->true();
            verify($model->hasErrors('valid_chk'))->true();
            verify($model->hasErrors('sort'))->true();
            verify($model->hasErrors('areaIds'))->true();
        });

        $this->specify('pict必須チェック', function () {
            // 新規
            $model = $this->getModel([], ['element1' => Widget::ELEMENT_PICT]);
            $model->validate();
            verify($model->hasErrors('pict'))->true();
            // elementにpictが含まれないときは必須チェックはかからない
            $model = $this->getModel();
            $model->validate();
            verify($model->hasErrors('pict'))->false();

            // 更新
            $widget = new Widget(['element1' => Widget::ELEMENT_PICT]);
            /** @var WidgetData $model */
            $model = WidgetData::find()->one();
            $model->populateRelation('widget', $widget);
            $model->validate();
            verify($model->hasErrors('pict'))->false();
            // elementにpictが含まれないときは必須チェックはかからない
            $model = $this->getModel();
            $model->validate();
            verify($model->hasErrors('pict'))->false();
        });

        $this->specify('urlsチェック(scenario別)', function () {
            $scenarios = [
                WidgetData::SCENARIO_DEFAULT,
                WidgetData::SCENARIO_REGISTER,
            ];
            $baseUrl = 'http://demo.job-maker.jp/';
            $validUrl = $baseUrl . str_repeat('a', (1999 - strlen($baseUrl))); // 正しいurl書式かつ文字数ギリギリ
            $invalidUrls = [
                1 => 'this is not url',
                2 => $baseUrl . str_repeat('a', (2000 - strlen($baseUrl))), // 正しいurl書式だが文字数over
            ];
            foreach ($scenarios as $scenario) {
                // 不正な値の検査
                foreach ($invalidUrls as $url) {
                    $model = $this->getModel([
                        'urls' => $url,
                    ]);
                    $model->setScenario($scenario);
                    $model->validate();
                    if ($model->scenario == WidgetData::SCENARIO_REGISTER) {
                        // 書き込みシナリオの時は検査しない
                        verify($model->hasErrors('urls'))->false();
                    } else {
                        // その他は検査され、弾かれる
                        verify($model->hasErrors('urls'))->true();
                    }
                }
                // 正常な値の検査（全シナリオvalidationが通る）
                $model = $this->getModel([
                    'urls' => $validUrl,
                ]);
                $model->validate();
                verify($model->hasErrors('urls'))->false();
            }
        });

        $this->specify('動的必須項目チェック', function () {
            $elements = ['title', 'description', 'pict', 'movieTag'];
            foreach ($elements as $i => $elem) {
                // 適当なelement attributeに検査elementを代入したWidgetを作る
                $elemNo = ($i % 3) + 1;
                $model = $this->getModel([], [
                    "element{$elemNo}" => Widget::INPUT_ELEMENTS[$elem],
                ]);
                $model->validate();
                // Widgetが持っている検査elementは必須だが、その他は必須ではない
                verify($model->hasErrors($elem))->true();
                foreach ($elements as $v) {
                    if ($v != $elem) {
                        verify($model->hasErrors($v))->false();
                    }
                }
            }
        });

        $this->specify('正しい値', function () {
            $widgetParams = [
                'id' => 9999999,
                'element1' => Widget::ELEMENT_MOVIE, // movieTag
                'element2' => Widget::ELEMENT_TITLE, // title
                'element3' => Widget::ELEMENT_DESCRIPTION, // description
            ];

            $widgetDataParams = [
                'widget_id' => $widgetParams['id'],
                'movieTag' => str_repeat('a', 255),
                'title' => str_repeat('a', 100),
                'description' => str_repeat('a', 200),
                'sort' => 255, // 最小値はsortのspecifyで検証済み
                'disp_start_date' => '2017/6/12',
                'areaIds' => [0, 2],
                'urls' => 'http://demo2.job-maker.jp',
                'valid_chk' => 1,
            ];
            $model = $this->getModel($widgetDataParams, $widgetParams);
            verify($model->validate())->true();
        });
    }

    /**
     * modelを取得する
     * 必要ならwidgetをpopulateRelationする
     * @param array $widgetDataParams
     * @param array $widgetParams
     * @return WidgetData
     */
    private function getModel($widgetDataParams = [], $widgetParams = [])
    {
        $model = new WidgetData();

        if ($widgetParams !== null) {
            $widget = new Widget();
            $widget->load([$widget->formName() => $widgetParams]);
            $model->populateRelation('widget', $widget);
        }
        $model->load([$model->formName() => $widgetDataParams]);
        return $model;
    }

    /**
     * ほぼ意味無いが一応作成
     */
    public function testValidChkArray()
    {
        verify(WidgetData::validChkArray()[WidgetData::INVALID])->equals(Yii::t('app', '無効'));
        verify(WidgetData::validChkArray()[WidgetData::VALID])->equals(Yii::t('app', '有効'));
    }

    public function testLoadFileInfo()
    {
        $picWidget = new Widget(['element1' => Widget::ELEMENT_PICT]);
        $notPicWidget = new Widget(['element1' => Widget::ELEMENT_TITLE]);

        $this->specify('ファイルpost無し、新規、画像ありwidget', function () use ($picWidget) {
            $model = new WidgetData();
            $model->populateRelation('widget', $picWidget);
            $model->pict = 'testPic';
            $model->loadFileInfo();
            verify($model->pict)->equals('testPic');
        });

        $this->specify('ファイルpost無し、更新、画像ありwidget', function () use ($picWidget) {
            /** @var WidgetData $model */
            $model = WidgetData::find()->where(['not', ['pict' => '']])->one();
            $oldPict = $model->pict;
            $model->populateRelation('widget', $picWidget);
            $model->pict = 'testPic';
            $model->loadFileInfo();
            verify($model->pict)->notEquals('testPic');
            verify($model->pict)->equals($oldPict);
        });

        $this->specify('ファイルpostあり、更新、画像ありwidget', function () use ($picWidget) {
            /** @var WidgetData $model */
            $model = WidgetData::find()->one();
            $model->populateRelation('widget', $picWidget);
            $this->loadFilePost($model->formName(), 'pict');
            $model->loadFileInfo();
            verify($model->getOldAttribute('pict'))->notEquals($model->pict);
        });

        $this->specify('ファイルpostあり、新規、画像無しwidget', function () use ($notPicWidget) {
            $model = new WidgetData();
            $model->populateRelation('widget', $notPicWidget);
            $model->pict = 'testPic';
            $model->loadFileInfo();
            verify($model->pict)->equals('testPic');
        });
    }

    public function testFileInstance()
    {
        $model = new WidgetData();
        $this->loadFilePost($model->formName(), 'pict');

        $this->specify('画像無し', function () {
            $notPicWidget = new Widget(['element1' => Widget::ELEMENT_TITLE]);
            $model = new WidgetData();
            $model->populateRelation('widget', $notPicWidget);
            verify($model->fileInstance())->null();
        });

        $this->specify('画像あり', function () {
            $picWidget = new Widget(['element1' => Widget::ELEMENT_PICT]);
            $model = new WidgetData();
            $model->populateRelation('widget', $picWidget);
            verify($model->fileInstance())->equals(UploadedFile::getInstance($model, 'pict'));
        });
    }

    public function testUpdateRelations()
    {
        /** @var WidgetDataArea $widgetDataArea */
        $widgetDataArea = WidgetDataArea::find()->one();
        $widgetData = $widgetDataArea->widgetData;

        verify($widgetData->updateRelations())->false();

        $widgetDataAreaIds = ArrayHelper::getColumn($widgetData->widgetDataArea, 'id');

        $widgetData->areaIds = [1, 2];
        $widgetData->urls = [
            1 => 'http://test.1',
            2 => 'http://test.2',
        ];

        $widgetData->updateRelations();
        foreach ($widgetDataAreaIds as $id) {
            $this->tester->dontSeeInDatabase(WidgetDataArea::tableName(), ['id' => $id]);
        }

        $this->tester->seeInDatabase(WidgetDataArea::tableName(), ['url' => 'http://test.1']);
        $this->tester->seeInDatabase(WidgetDataArea::tableName(), ['url' => 'http://test.2']);
        static::getFixtureInstance('widget_data_area')->initTable();
    }

    /**
     * saveRelationsのtest
     */
    public function testSaveRelations()
    {
        $this->specify('url登録の検証', function () {
            // areaIdのみ、urlのみ、両方あり
            $model = new WidgetData();
            $model->id = 999;
            $model->populateRelation('widget', new Widget());
            $model->load([
                $model->formName() => [
                    'areaIds' => [1, 3],
                    'urls' => [
                        2 => 'http://demo2.job-maker.jp/hoge',
                        3 => 'http://demo2.job-maker.jp/fuga',
                    ],
                ],
            ]);
            $model->saveRelations();
            // areaIdのみの場合はurlが空の状態で登録される
            $this->tester->canSeeInDatabase(WidgetDataArea::tableName(), [
                'widget_data_id' => $model->id,
                'area_id' => 1,
                'url' => null,
            ]);
            // urlのみの場合は登録されない
            $this->tester->cantSeeInDatabase(WidgetDataArea::tableName(), [
                'widget_data_id' => $model->id,
                'area_id' => 2,
                'url' => 'http://demo2.job-maker.jp/hoge',
            ]);
            // 両方ある場合は両方登録される
            $this->tester->canSeeInDatabase(WidgetDataArea::tableName(), [
                'widget_data_id' => $model->id,
                'area_id' => 3,
                'url' => 'http://demo2.job-maker.jp/fuga',
            ]);

            self::getFixtureInstance('widget_data_area')->initTable();
        });

        $this->specify('動画登録の検証', function () {
            $model = new WidgetData();
            $model->id = 999;
            $model->populateRelation('widget', new Widget(['element1' => Widget::ELEMENT_MOVIE]));
            $model->load([
                $model->formName() => [
                    'areaIds' => [1, 3],
                    'movieTag' => 'movieTag',
                ],
            ]);
            $model->saveRelations();
            $this->tester->canSeeInDatabase(WidgetDataArea::tableName(), [
                'widget_data_id' => $model->id,
                'area_id' => 1,
                'movie_tag' => 'movieTag',
            ]);
            $this->tester->canSeeInDatabase(WidgetDataArea::tableName(), [
                'widget_data_id' => $model->id,
                'area_id' => 3,
                'movie_tag' => 'movieTag',
            ]);
            self::getFixtureInstance('widget_data_area')->initTable();
        });
    }

    /**
     * 各種getter、setterのtest
     */
    public function testGetAndSetUrls()
    {
        $array = [
            1 => 'http://demo2.job-maker.jp/hoge',
            2 => 'http://demo2.job-maker.jp/fuga',
        ];

        $this->specify('setしてsetしたものをget', function () use ($array) {
            $model = new WidgetData;
            $model->urls = $array;
            verify($model->urls)->equals($array);
        });

        $this->specify('何もsetされていない時はrelationからget', function () use ($array) {
            $model = $this->getModel();
            $widgetDataAreas = [];
            foreach ($array as $areaId => $url) {
                $widgetDataAreas[] = new WidgetDataArea([
                    'area_id' => $areaId,
                    'url' => $url,
                ]);
            }
            $model->populateRelation('widgetDataArea', $widgetDataAreas);
            verify($model->urls)->equals($array);
        });
    }

    /**
     * movieTagのgetterとsetterのtest
     */
    public function testGetAndSetMovieTag()
    {
        $movieTag = 'tag';

        $this->specify('setしてsetしたものをget', function () use ($movieTag) {
            $model = new WidgetData;
            $model->movieTag = $movieTag;
            verify($model->movieTag)->equals($movieTag);
        });

        $this->specify('何もsetされていない時はrelationからget', function () use ($movieTag) {

            $model = $this->getModel();
            $model->populateRelation('widgetDataArea', [
                new WidgetDataArea([
                    'movie_tag' => $movieTag,
                ]),
            ]);
            verify($model->movieTag)->equals($movieTag);
        });
    }

    /**
     * areaIdsのgetterとsetterのtest
     */
    public function testGetAndSetAreaIds()
    {
        $array = [1, 2, 3];

        $this->specify('setしてsetしたものをget', function () use ($array) {
            $model = new WidgetData;
            $model->areaIds = $array;
            verify($model->areaIds)->equals($array);
        });

        $this->specify('何もsetされていない時はrelationからget', function () use ($array) {
            $model = $this->getModel();
            $widgetDataAreas = [];
            foreach ($array as $areaId) {
                $widgetDataAreas[] = new WidgetDataArea([
                    'area_id' => $areaId,
                ]);
            }
            $model->populateRelation('widgetDataArea', $widgetDataAreas);
            verify($model->areaIds)->equals($array);
        });
    }

    // getWidgetDataAreaとgetWidgetはrelationなのでテストは省略
}
