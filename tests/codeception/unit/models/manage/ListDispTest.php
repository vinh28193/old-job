<?php

namespace models\manage;

use app\models\manage\DispType;
use app\models\manage\JobColumnSet;
use app\models\manage\JobMaster;
use app\models\manage\ListDisp;
use tests\codeception\unit\fixtures\ClientChargePlanFixture;
use tests\codeception\fixtures\JobColumnSetFixture;
use tests\codeception\fixtures\ListDispFixture;
use Yii;
use tests\codeception\unit\JmTestCase;
use yii\helpers\ArrayHelper;

/**
 * @property ListDispFixture $list_disp
 * @property JobColumnSetFixture $job_column_set
 * @property ClientChargePlanFixture $client_charge_plan
 */
class ListDispTest extends JmTestCase
{
    /**
     * テーブル名の設定
     */
    public static function tableName()
    {
        verify(ListDisp::tableName())->equals('list_disp');
    }

    /**
     * ラベル設定テスト
     */
    public function testAttributeLabels()
    {
        $model = new ListDisp();
        verify($model->attributeLabels())->notEmpty();
    }

    /**
     * itemsのtest
     */
    public function testItems()
    {
        $dispTypeId = DispType::find()->where(['valid_chk' => DispType::VALID])->one()->id;
        $items = ListDisp::items($dispTypeId);
        verify($items)->notEmpty();
        // リスト表示アイテムの検証
        $this->checkListItems($items, $dispTypeId);
    }

    /**
     * editableDetailAttributesのテスト
     */
    public function testEditableDetailAttributes()
    {
        $jobMasterId = $this->id(23, 'job_master');
        $dispTypeId = $this->id(2, 'disp_type');
        $jobMaster = JobMaster::findOne($jobMasterId);
        // 更新時
        $editableAttributes = ListDisp::editableDetailAttributes($jobMaster, $dispTypeId);
        $items = ListDisp::items($dispTypeId);

        verify($editableAttributes)->notEmpty();
        foreach ($editableAttributes as $i => $attribute) {
            verify($attribute['label'])->equals($items[$i]['label']);
            if ($items[$i]->column_name == 'job_no') {
                verify($attribute['value'])->equals('job_no');
            }
        }
        // 新規作成時(他は上でやっているのでjob_noのみのチェック)
        $editableAttributes = ListDisp::editableDetailAttributes(new JobMaster(), $dispTypeId);
        $items = ListDisp::items($dispTypeId);

        verify($editableAttributes)->notEmpty();
        foreach ($editableAttributes as $i => $attribute) {
            if ($items[$i]->column_name == 'job_no') {
                verify($attribute['value'])->equals(Yii::t('app', '※仕事IDは自動で採番されます'));
            }
        }
    }

    /**
     * getJobAttributesWithFormatとremoveEmptyJobAttributesのテスト
     */
    public function testGetJobAttributesWithFormat()
    {
        $dispTypeId = $this->id(1, 'disp_type');
        $items = ListDisp::items($dispTypeId);

        verify($items)->notEmpty();

        $availableAttributes = ArrayHelper::getColumn($items, 'column_name');
        $emptyAttribute1 = $availableAttributes[0];
        $emptyAttribute2 = $availableAttributes[1];
        // 2つ空にする
        $jobMaster = JobMaster::findOne($this->id(12, 'job_master'));
        $jobMaster->$emptyAttribute1 = '';
        $jobMaster->$emptyAttribute2 = '';
        $attributes = ListDisp::getJobAttributesWithFormat($jobMaster);
        // 全部の数比較
        verify($attributes)->notEmpty();
        verify($attributes)->count(count($items) - 2);
        // 中身比較
        foreach ($items as $item) {
            if ($item->column_name == $emptyAttribute1 || $item->column_name == $emptyAttribute2) {
                verify(ArrayHelper::isIn($item->columnNameWithFormat, $attributes))->false();
            } else {
                verify(ArrayHelper::isIn($item->columnNameWithFormat, $attributes))->true();
            }
        }
    }

    /**
     * bothItemsのテスト
     */
    public function testBothItems()
    {
        $dispTypeId = DispType::find()->where(['valid_chk' => DispType::VALID])->one()->id;
        $bothItems = ListDisp::bothItems($dispTypeId);

        verify(JobColumnSet::$dispTypeId)->equals($dispTypeId);
        verify($bothItems['listItems'])->notEmpty();
        verify($bothItems['notListItems'])->notEmpty();

        // リスト表示アイテムの検証
        $this->checkListItems($bothItems['listItems'], $dispTypeId);

        // リスト非表示アイテムの検証
        $this->checkNotListItems($bothItems['notListItems'], $dispTypeId);
    }

    /**
     * 与えられた項目群がlist表示する設定になっているかどうかを検証する
     * @param JobColumnSet[] $listItems
     * @param int $dispTypeId
     */
    public function checkListItems($listItems, $dispTypeId)
    {
        JobColumnSet::$dispTypeId = $dispTypeId;
        $sort = 0;
        foreach ($listItems as $i => $item) {
            /** @var JobColumnSet $item */
            unset($item->listDisp);
            $this->commonCheck($item);
            // disp_type_idが一致
            verify($item->listDisp->disp_type_id)->equals($dispTypeId);
            // 並び替え検証
            verify($item->listDisp->sort_no)->greaterOrEquals($sort);
            $sort = $item->listDisp->sort_no;
        }
    }

    /**
     * 与えられた項目群がlist表示しない設定になっているかどうかを検証する
     * @param JobColumnSet[] $items
     * @param int $dispTypeId
     */
    public function checkNotListItems($items, $dispTypeId)
    {
        JobColumnSet::$dispTypeId = $dispTypeId;
        foreach ($items as $i => $item) {
            /** @var JobColumnSet $item */
            $this->commonCheck($item);
            // disp_type_idがない
            verify($item->listDisp)->null();
        }
    }

    /**
     * アイテム共通チェック
     * @param JobColumnSet $item
     */
    public function commonCheck(JobColumnSet $item)
    {
        // listItemになれない項目ではない
        verify(ArrayHelper::isIn($item->column_name, JobColumnSet::NOT_AVAILABLE_LIST_DISP_ITEMS))->equals(false);
        // 有効な項目である
        verify($item->valid_chk)->equals(JobColumnSet::VALID);
    }
}
