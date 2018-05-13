<?php
namespace tests\codeception\unit\common;

use yii;
use tests\codeception\unit\JmTestCase;
use app\models\manage\JobMasterSearch;
use yii\helpers\Json;
use tests\codeception\unit\fixtures\JobMasterFixture;
use yii\helpers\ArrayHelper;
use yii\db\ActiveQuery;

class SearchModelTraitTest extends JmTestCase
{
    /**
     * @var \UnitTester
     */
    protected $tester;

    /**
     * fixture設定
     * @return array
     */
    public function fixtures()
    {
        return array_merge(parent::fixtures(), [
            // メイン
            'job_master' => JobMasterFixture::className(),
        ]);
    }

    public function testParse()
    {
        $model = new JobMasterSearch();
        $params = [
            'gridData' => Json::encode([
                'selected' => [
                    Json::encode(['id' => $this->id(1, 'job_master'), 'tenant_id' => Yii::$app->tenant->id]),
                    Json::encode(['id' => $this->id(2, 'job_master'), 'tenant_id' => Yii::$app->tenant->id]),
                ]
            ]),
            $model->formName() => 1,
        ];

        // selectedあり、formNameなし
        verify(ArrayHelper::getValue($model->parse($params, $model->formName()), $model->formName() . '.selected'))->equals(['1', '2']);
        // selectedなし、formNameなし
        verify(ArrayHelper::getValue($model->parse([], $model->formName()), $model->formName() . '.selected'))->null();
        // selectedあり、formNameあり
        verify(ArrayHelper::getValue($model->parse($params, 'testForm'), 'testForm.selected'))->equals(['1', '2']);
    }

    public function testSelected()
    {
        $selected = ['1', '2', '3'];
        $condition = ['valid_chk' => 1];
        $model = new JobMasterSearch();
        $testQuery = $model::find()->where($condition);

        // select無し
        $model->selected($testQuery);
        $models = $testQuery->all();
        foreach ($models as $model) {
            verify($model->valid_chk)->equals(1);
        }

        //allCheckあり
        $model->load([$model->formName() => ['selected' => $selected, 'allCheck' => true]]);
        $model->selected($testQuery);
        $models = $testQuery->all();
        foreach ($models as $model) {
            verify($model->valid_chk)->equals(1);
            verify(in_array($model->id, [1, 2, 3]))->false();
        }

        //インスタンス初期化
        $testQuery = $model::find()->where($condition);

        //allCheckなし
        $model->load([$model->formName() => ['selected' => $selected, 'allCheck' => false]]);
        $model->selected($testQuery);
        $models = $testQuery->all();
        foreach ($models as $model) {
            verify($model->valid_chk)->equals(1);
            verify(in_array($model->id, [1, 2, 3]))->true();
        }
    }

    // protected functionなので不要
//    public function testIsEmpty(){}
}