<?php

namespace models\manage;

use app\models\manage\ClientColumnSet;
use app\models\manage\ClientDisp;
use app\models\manage\DispType;
use app\models\manage\JobMaster;
use tests\codeception\unit\JmTestCase;
use Yii;
use yii\helpers\ArrayHelper;

/**
 * Class ClientDispTest
 * @package models\manage
 */
class ClientDispTest extends JmTestCase
{
    /**
     * 要素テスト
     */
    public function testAttributeLabels()
    {
        $model = new ClientDisp();
        verify(is_array($model->attributeLabels()))->true();
    }

    /**
     * ルールテスト
     */
    public function testRules()
    {
        $this->specify('数字チェック', function () {
            $model = new ClientDisp();
            $model->load([
                $model->formName() => [
                    'tenant_id' => '文字列',
                    'sort_no' => '文字列',
                    'disp_type_id' => '文字列',
                ],
            ]);
            $model->validate();
            verify($model->hasErrors('tenant_id'))->true();
            verify($model->hasErrors('sort_no'))->true();
            verify($model->hasErrors('disp_type_id'))->true();
        });

        $this->specify('文字列最大値チェック', function () {
            $model = new ClientDisp();
            $model->load([
                $model->formName() => [
                    'column_name' => str_repeat('a', 256),
                ],
            ]);
            $model->validate();
            verify($model->hasErrors('column_name'))->true();
        });

        $this->specify('必須チェック', function () {
            $model = new ClientDisp();
            $model->load([
                $model->formName() => [
                    'tenant_id' => null,
                    'sort_no' => null,
                    'disp_type_id' => null,
                ],
            ]);
            $model->validate();
            verify($model->hasErrors('tenant_id'))->true();
            verify($model->hasErrors('sort_no'))->true();
            verify($model->hasErrors('disp_type_id'))->true();
        });

        $this->specify('正しい値', function () {
            $model = new ClientDisp();
            $model->load([
                $model->formName() => [
                    'tenant_id' => 1,
                    'column_name' => str_repeat('a', 255),
                    'disp_type_id' => 1,
                    'sort_no' => 1,
                ],
            ]);
            verify($model->validate())->true();
        });
    }

    /**
     * 企業情報項目リストの取得テスト
     */
    public function testItems()
    {
        $dispTypeId = $this->id(1, 'disp_type');
        // fixtureから取り出したいレコード生成
        // 有効になっているカラム名
        $validItems = array_filter(self::getFixtureInstance('client_column_set')->data(), function ($records) {
            return $records['valid_chk'];
        });
        $validColumnNames = ArrayHelper::getColumn($validItems, 'column_name');
        // 条件でフィルタリング
        $targetRecords = array_filter(self::getFixtureInstance('client_disp')->data(), function ($record) use ($validColumnNames, $dispTypeId) {
            return $record['tenant_id'] == Yii::$app->tenant->id && $record['disp_type_id'] == $dispTypeId && ArrayHelper::isIn($record['column_name'], $validColumnNames);
        });
        ArrayHelper::multisort($targetRecords, 'sort_no');

        $items = ClientDisp::items($dispTypeId);

        verify($items)->notEmpty();
        foreach ($items as $i => $item) {
            verify($item instanceof ClientColumnSet)->true();
            verify($item->column_name)->equals($targetRecords[$i]['column_name']);
        }
    }

    public function testGetClientAttributesWithFormat()
    {
        $dispTypeId = $this->id(1, 'disp_type');
        $items = ClientDisp::items($dispTypeId);

        verify($items)->notEmpty();

        $emptyAttribute = 'client_name';
        // 2つ空にする
        $jobMaster = JobMaster::findOne($this->id(1, 'job_master'));
        $jobMaster->clientMaster->$emptyAttribute = '';
        $attributes = ClientDisp::getClientAttributesWithFormat($jobMaster);
        // 全部の数比較
        verify($attributes)->notEmpty();
        verify($attributes)->count(count($items) - 1);
        // 中身比較
        foreach ($items as $item) {
            /** @var ClientColumnSet $item */
            if ($item->column_name == $emptyAttribute) {
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
        $bothItems = ClientDisp::bothItems($dispTypeId);

        verify(ClientColumnSet::$dispTypeId)->equals($dispTypeId);
        verify($bothItems['clientItems'])->notEmpty();
        verify($bothItems['notClientItems'])->notEmpty();

        // クライアント表示アイテムの検証
        $this->checkListItems($bothItems['clientItems'], $dispTypeId);

        // クライアント非表示アイテムの検証
        foreach ($bothItems['notClientItems'] as $i => $item) {
            /** @var ClientColumnSet $item */
            $this->commonCheck($item);
            // disp_type_idがない
            verify($item->clientDisp)->null();
        }
    }

    /**
     * @param ClientColumnSet[] $clientItems
     * @param integer $dispTypeId
     */
    public function checkListItems($clientItems, $dispTypeId)
    {
        ClientColumnSet::$dispTypeId = $dispTypeId;
        $sort = 0;
        foreach ($clientItems as $i => $item) {
            /** @var ClientColumnSet $item */
            unset($item->clientDisp);
            $this->commonCheck($item);
            // disp_type_idが一致
            verify($item->clientDisp->disp_type_id)->equals($dispTypeId);
            // 並び替え検証
            verify($item->clientDisp->sort_no)->greaterOrEquals($sort);
            $sort = $item->clientDisp->sort_no;
        }
    }

    /**
     * アイテム共通チェック
     * @param ClientColumnSet $item
     */
    public function commonCheck(ClientColumnSet $item)
    {
        // clientItemになれない項目ではない
        verify(ArrayHelper::isIn($item->column_name, ClientColumnSet::NOT_AVAILABLE_CLIENT_DISP_ITEMS))->equals(false);
        // 有効な項目である
        verify($item->valid_chk)->equals(ClientColumnSet::VALID);
    }
}
