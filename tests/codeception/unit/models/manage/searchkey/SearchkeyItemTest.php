<?php

namespace models\manage\searchkey;

use app\components\Area as ComArea;
use app\models\JobMasterDisp;
use app\models\manage\searchkey\Pref;
use app\models\manage\searchkey\SearchkeyItem;
use app\models\manage\SearchkeyMaster;
use tests\codeception\unit\JmTestCase;
use yii\base\Exception;

use yii;

class SearchkeyItemTest extends JmTestCase
{
    // model
    function createModel($flg)
    {
        $tmpModel = null;

        $tmpModel = Yii::createObject('app\models\manage\searchkey\SearchkeyItem'.$flg);

        if ($tmpModel !== null) {
            return $tmpModel;
        } else {
            throw new Exception('モデル取得失敗.');
        }
    }

    /**
     * attribute labels test
     */
    public function testAttributeLabels()
    {
        for($i=1; $i<=20; $i++) {
            $model = $this->createModel($i);
            verify($model->attributeLabels())->notEmpty();
        }
    }

    public function testRules()
    {
        for($i=1; $i<=10; $i++){
            $this->specify('カテゴリ名空時の検証', function() use ($i) {
                $model = $this->createModel($i);
                $model->load(['SearchkeyItem'.$i => [
                    'searchkey_item_name' => '',
                ]]);
                $model->validate();
                verify($model->hasErrors('searchkey_item_name'))->true();
            });
            $this->specify('カテゴリ名最大文字数の検証', function() use ($i) {
                $model = $this->createModel($i);
                $model->load(['SearchkeyItem'.$i => [
                    'searchkey_item_name' => str_repeat('1',51),
                ]]);
                $model->validate();
                verify($model->hasErrors('searchkey_item_name'))->true();
            });
            $this->specify('表示順空時の検証', function() use ($i) {
                $model = $this->createModel($i);
                $model->load(['SearchkeyItem'.$i => [
                    'sort' => '',
                ]]);
                $model->validate();
                verify($model->hasErrors('sort'))->true();
            });
            $this->specify('公開状況空時の検証', function() use ($i) {
                $model = $this->createModel($i);
                $model->load(['SearchkeyItem'.$i => [
                    'valid_chk' => null,
                ]]);
                $model->validate();
                verify($model->hasErrors('valid_chk'))->true();
            });
            $this->specify('公開状況数字外の検証', function() use ($i) {
                $model = $this->createModel($i);
                $model->load(['SearchkeyItem'.$i => [
                    'valid_chk' => 'aaa',
                ]]);
                $model->validate();
                verify($model->hasErrors('valid_chk'))->true();
            });
            $this->specify('正しい値', function() use ($i) {
                $model = $this->createModel($i);
                $model->load(['SearchkeyItem'.$i => [
                    'searchkey_item_name' => '文字列',
                    'sort' => 1,
                    'valid_chk' => 1,
                    'searchkey_category_id' => 1,
                ]]);
                verify($model->validate())->true();
            });
        }
    }

    /**
     * 検索結果のあるサーチキーを取得するテスト
     */
    public function testItemArray()
    {
        $areas = (new ComArea())->models;
        foreach ((array)$areas as $area) {
            list($prefIds, $prefNos) = Pref::prefIdsPrefNos($area);
            foreach ((array)$prefIds as $index => $prefId) {
                $jobIds = JobMasterDisp::jobIds($prefId);
                foreach ((array)$jobIds as $jobId) {
                    foreach ((array)SearchkeyMaster::findSearchKeys() as $searchkeyMaster) {
                        /** @var SearchkeyMaster $searchkeyMaster */
                        if (preg_match('/searchkey_item/', $searchkeyMaster->table_name)) {

                            /** @var SearchkeyItem $itemModelName */
                            $itemModelName = $searchkeyMaster->modelFullName;
                            $itemTableName = $itemModelName::tableName();
                            $jobRelationTableName = $searchkeyMaster->job_relation_table;

                            $itemArray = SearchkeyItem::itemArray($jobId, $searchkeyMaster);

                            // Itemが有効なものかチェック
                            $countValidItem = $itemModelName::find()->where([
                                'searchkey_item_no' => $itemArray,
                                'valid_chk' => SearchkeyItem::FLAG_VALID,
                            ])->count();

                            verify($countValidItem)->equals(count($itemArray));

                            // 指定された仕事IDに対するItemかチェック
                            $resItems = $itemModelName::find()->select([
                                'searchkey_item_no',
                            ])->innerJoin(
                                $jobRelationTableName,
                                '`' . $itemTableName . '`.`id`=`' . $jobRelationTableName . '`.`searchkey_item_id`'
                            )->where([
                                $jobRelationTableName . '.job_master_id' => $jobId,
                                $itemTableName . '.valid_chk' => SearchkeyItem::FLAG_VALID,
                            ])->column();

                            verify(array_diff($itemArray, $resItems))->isEmpty();
                        }
                    }
                }
            }
        }
    }
}