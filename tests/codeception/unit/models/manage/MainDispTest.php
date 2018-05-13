<?php

namespace models\manage;

use app\models\manage\DispType;
use tests\codeception\unit\JmTestCase;
use Yii;
use app\models\manage\MainDisp;
use app\models\manage\JobColumnSet;
use tests\codeception\unit\fixtures\MainDispFixture;
use tests\codeception\fixtures\JobColumnSetFixture;
use yii\helpers\ArrayHelper;

/**
 * @property MainDispFixture $main_disp
 * @property JobColumnSetFixture $job_column_set
 */
class MainDispTest extends JmTestCase
{
    /**
     * 一応
     */
    public function testTableName()
    {
        $model = new MainDisp();
        verify($model->tableName())->equals('main_disp');
    }

    /**
     * 要素テスト
     */
    public function testAttributeLabels()
    {
        $model = new MainDisp();
        verify(is_array($model->attributeLabels()))->true();
    }

    /**
     * rulesテスト
     */
    public function testRules()
    {
        $this->specify('必須チェック', function () {
            $model = new MainDisp();
            $model->load([
                $model->formName() => [
                    'main_disp_name' => null,
                    'disp_type_id' => null,
                    'disp_chk' => null,
                ],
            ]);
            $model->validate();
            verify($model->hasErrors('main_disp_name'))->true();
            verify($model->hasErrors('disp_type_id'))->true();
            verify($model->hasErrors('disp_chk'))->true();
        });

        $this->specify('数字チェック', function () {
            $model = new MainDisp();
            $model->load([
                $model->formName() => [
                    'disp_type_id' => '文字列',
                ],
            ]);
            $model->validate();
            verify($model->hasErrors('disp_type_id'))->true();
        });

        $this->specify('boolチェック', function () {
            $model = new MainDisp();
            $model->load([
                $model->formName() => [
                    'disp_chk' => '文字列',
                ],
            ]);
            $model->validate();
            verify($model->hasErrors('disp_chk'))->true();

            $model->load([
                $model->formName() => [
                    'disp_chk' => 2,
                ],
            ]);
            $model->validate();
            verify($model->hasErrors('disp_chk'))->true();
        });

        $this->specify('最大ストリング', function () {
            $model = new MainDisp();
            $model->load([
                $model->formName() => [
                    'main_disp_name' => str_repeat('a', 21),
                    'column_name' => str_repeat('a', 31),
                ],
            ]);
            $model->validate();
            verify($model->hasErrors('main_disp_name'))->true();
            verify($model->hasErrors('column_name'))->true();
        });

        $this->specify('main_disp_nameチェック', function () {
            $model = new MainDisp();
            $model->load([
                $model->formName() => [
                    'main_disp_name' => str_repeat('a', 20),
                ],
            ]);
            $model->validate();
            verify($model->hasErrors('main_disp_name'))->true();
        });

        $this->specify('正しい値', function () {
            $model = new MainDisp();
            $model->load([
                $model->formName() => [
                    'tenant_id' => 1,
                    'main_disp_name' => MainDisp::DISPLAY_NAMES[0],
                    'disp_type_id' => 1,
                    'column_name' => str_repeat('a', 30),
                    'disp_chk' => 1,
                ],
            ]);
            verify($model->validate())->true();
        });
    }

    public function testItems()
    {
        $dispTypeId = 3;
        // fixtureから取り出したいレコード生成
        // 有効になっているカラム名
        $validItems = array_filter(self::getFixtureInstance('job_column_set')->data(), function ($records) {
            return $records['valid_chk'];
        });
        $validColumnNames = ArrayHelper::getColumn($validItems, 'column_name');
        // 条件でフィルタリング
        $targetRecords = array_filter(self::getFixtureInstance('main_disp')->data(), function ($record) use ($validColumnNames, $dispTypeId) {
            return $record['tenant_id'] == Yii::$app->tenant->id && $record['disp_type_id'] == $dispTypeId && $record['disp_chk'] == 1 && ArrayHelper::isIn($record['column_name'], $validColumnNames);
        });

        $targetRecords = ArrayHelper::index($targetRecords, 'main_disp_name');
        $items = MainDisp::items($dispTypeId);
        foreach ($items as $i => $item) {
            verify($item instanceof JobColumnSet)->true();
            verify($item->column_name)->equals($targetRecords[$i]['column_name']);
        }
    }

    /**
     * bothItemsのテスト
     */
    public function testGetBothMainItems()
    {
        $dispTypeId = DispType::find()->where(['valid_chk' => DispType::VALID])->one()->id;
        $bothItems = MainDisp::bothItems($dispTypeId);

        verify(JobColumnSet::$dispTypeId)->equals($dispTypeId);
        verify($bothItems['mainItems'])->notEmpty();
        verify($bothItems['notMainItems'])->notEmpty();

        // メイン表示アイテムの検証
        $this->checkListItems($bothItems['mainItems'], $dispTypeId);

        // メイン非表示アイテムの検証
        foreach ($bothItems['notMainItems'] as $i => $item) {
            /** @var JobColumnSet $item */
            $this->commonCheck($item);
            // disp_type_idがない
            verify($item->mainDisp)->null();
        }
    }

    /**
     * @param JobColumnSet[] $mainItems
     * @param integer $dispTypeId
     */
    public function checkListItems($mainItems, $dispTypeId)
    {
        JobColumnSet::$dispTypeId = $dispTypeId;
        foreach ($mainItems as $i => $item) {
            /** @var JobColumnSet $item */
            unset($item->mainDisp);
            $this->commonCheck($item);
            // disp_type_idが一致
            verify($item->mainDisp->disp_type_id)->equals($dispTypeId);
            verify($item->mainDisp->disp_chk)->equals(MainDisp::FLAG_VALID);
        }
    }

    /**
     * アイテム共通チェック
     * @param JobColumnSet $item
     */
    public function commonCheck(JobColumnSet $item)
    {
        // 有効な項目である
        verify($item->valid_chk)->equals(JobColumnSet::VALID);
    }

    /**
     * isDisplayNameのテスト
     */
    public function testIsDisplayName()
    {
        verify(MainDisp::isDisplayName('test'))->false();
        foreach (MainDisp::DISPLAY_NAMES as $name) {
            verify(MainDisp::isDisplayName($name))->true();
        }
    }
}
