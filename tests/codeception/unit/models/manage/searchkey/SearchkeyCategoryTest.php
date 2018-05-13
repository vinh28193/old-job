<?php

namespace models\manage\searchkey;

use app\components\Area as ComArea;
use app\models\JobMasterDisp;
use app\models\manage\searchkey\Pref;
use app\models\manage\searchkey\SearchkeyCategory;
use app\models\manage\searchkey\SearchkeyCategory1;
use app\models\manage\searchkey\SearchkeyItem;
use app\models\manage\SearchkeyMaster;
use tests\codeception\unit\JmTestCase;
use yii\base\Exception;
use yii\helpers\ArrayHelper;

use yii;
use yii\helpers\Inflector;

class SearchkeyCategoryTest extends JmTestCase
{
    /**
     * @param $flg integer
     * @return null|\app\models\manage\searchkey\SearchkeyCategory
     * @throws Exception
     * @throws yii\base\InvalidConfigException
     */
    function createModel($flg)
    {
        $tmpModel = null;

        $tmpModel = Yii::createObject('app\models\manage\searchkey\SearchkeyCategory'.$flg);

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
        for($i=1; $i<=10; $i++) {
            $model = $this->createModel($i);
            verify($model->attributeLabels())->notEmpty();
        }
    }

    public function testRules()
    {
        for($i=1; $i<=10; $i++){
            $this->specify('カテゴリ名空時の検証', function() use ($i) {
                $model = $this->createModel($i);
                $model->load(['SearchkeyCategory'.$i => [
                    'searchkey_category_name' => '',
                ]]);
                $model->validate();
                verify($model->hasErrors('searchkey_category_name'))->true();
            });
            $this->specify('カテゴリ名最大文字数の検証', function() use ($i) {
                $model = $this->createModel($i);
                $model->load(['SearchkeyCategory'.$i => [
                    'searchkey_category_name' => str_repeat('1',51),
                ]]);
                $model->validate();
                verify($model->hasErrors('searchkey_category_name'))->true();
            });
            $this->specify('表示順空時の検証', function() use ($i) {
                $model = $this->createModel($i);
                $model->load(['SearchkeyCategory'.$i => [
                    'sort' => '',
                ]]);
                $model->validate();
                verify($model->hasErrors('sort'))->true();
            });
            $this->specify('公開状況空時の検証', function() use ($i) {
                $model = $this->createModel($i);
                $model->load(['SearchkeyCategory'.$i => [
                    'valid_chk' => null,
                ]]);
                $model->validate();
                verify($model->hasErrors('valid_chk'))->true();
            });
            $this->specify('公開状況数字外の検証', function() use ($i) {
                $model = $this->createModel($i);
                $model->load(['SearchkeyCategory'.$i => [
                    'valid_chk' => 'aaa',
                ]]);
                $model->validate();
                verify($model->hasErrors('valid_chk'))->true();
            });
            $this->specify('公開状況数字外の検証', function() use ($i) {
                $model = $this->createModel($i);
                $model->load(['SearchkeyCategory'.$i => [
                    'valid_chk' => 'aaa',
                ]]);
                $model->validate();
                verify($model->hasErrors('valid_chk'))->true();
            });
            $this->specify('正しい値', function() use ($i) {
                $model = $this->createModel($i);
                $model->load(['SearchkeyCategory'.$i => [
                    'searchkey_category_name' => '文字列',
                    'sort' => 1,
                    'valid_chk' => 1,
                ]]);
                verify($model->validate())->true();
            });
        }
    }

    public function testItemModelName()
    {
        for($i=1; $i<=10; $i++){
            $model = $this->createModel($i);
            verify($model->itemModelName)->equals('app\models\manage\searchkey\SearchkeyItem'.$i);
        }
    }

    public function testSearchkeyCategoryList()
    {
        //カテゴリ1のみテストを行う
        $SearchCategoryList = SearchkeyCategory1::getSearchkeyCategoryList(1);
        foreach ($SearchCategoryList as $id => $categoryName) {
            $target = 
                ArrayHelper::getValue(ArrayHelper::index(
                    self::getFixtureInstance('searchkey_category1')->data(),
                    'id'
                ),
                    $id
                );
            //fixtureのカテゴリ名とfunctionから取得したカテゴリ名を比較
            verify($target['searchkey_category_name'])->equals($categoryName);
        }
    }

    /**
     * 検索結果のあるサーチキーを取得するテスト
     */
    public function testCategoryArray()
    {
        $areas = (new ComArea())->models;
        foreach ((array)$areas as $area) {
            list($prefIds, $prefNos) = Pref::prefIdsPrefNos($area);
            foreach ((array)$prefIds as $index => $prefId) {
                $jobIds = JobMasterDisp::jobIds($prefId);
                foreach ((array)$jobIds as $jobId) {
                    foreach ((array)SearchkeyMaster::findSearchKeys() as $searchkeyMaster) {
                        /** @var SearchkeyMaster $searchkeyMaster */
                        if (preg_match('/searchkey_category/', $searchkeyMaster->table_name)) {
                            /** @var SearchkeyCategory $categoryModelName */
                            /** @var SearchkeyItem $itemModelName */
                            $categoryModelName = $searchkeyMaster->modelFullName;
                            $categoryTableName = $categoryModelName::tableName();
                            $itemTableName = str_replace('category', 'item', $categoryTableName);
                            $itemModelName = SearchkeyMaster::MODEL_BASE_PATH . Inflector::camelize($itemTableName);
                            $jobRelationTableName = $searchkeyMaster->job_relation_table;
                            $categoryArray = SearchkeyCategory::categoryArray($jobId, $searchkeyMaster);

                            foreach ((array)$categoryArray as $category => $items) {
                                // Item配列がCategoryのものかチェック
                                $resCategory = $itemModelName::find()->select([
                                    'searchkey_category_no',
                                ])->innerJoin(
                                    $categoryTableName,
                                    '`' . $itemTableName . '`.`searchkey_category_id`=`' . $categoryTableName . '`.`id`'
                                )->where([
                                    'searchkey_item_no' => $items,
                                ])->distinct()->column();

                                verify(count($resCategory))->equals(1);
                                verify($resCategory[0])->equals($category);

                                // Categoryが有効なものかチェック
                                $countValidCategory = $categoryModelName::find()->where([
                                    'searchkey_category_no' => $category,
                                    'valid_chk' => SearchkeyCategory::FLAG_VALID,
                                ])->count();

                                verify($countValidCategory)->equals(1);

                                // Itemが有効なものかチェック
                                $countValidItem = $itemModelName::find()->where([
                                    'searchkey_item_no' => $items,
                                    'valid_chk' => SearchkeyItem::FLAG_VALID,
                                ])->count();

                                verify($countValidItem)->equals(count($items));

                                // 指定された仕事IDに対するItemかチェック
                                // categoryのvalid_chkが0の場合を除外していないので、$resItemsの方が多いことがある
                                $resItems = $itemModelName::find()->select([
                                    'searchkey_item_no',
                                ])->innerJoin(
                                    $jobRelationTableName,
                                    '`' . $itemTableName . '`.`id`=`' . $jobRelationTableName . '`.`searchkey_item_id`'
                                )->where([
                                    $jobRelationTableName . '.job_master_id' => $jobId,
                                    $itemTableName . '.valid_chk' => SearchkeyItem::FLAG_VALID,
                                ])->column();

                                verify(array_diff($items, $resItems))->isEmpty();
                            }
                        }
                    }
                }
            }
        }
    }
}