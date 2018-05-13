<?php

namespace models\manage\searchkey;

use app\components\Area as ComArea;
use app\models\JobMasterDisp;
use app\models\manage\searchkey\Pref;
use app\models\manage\searchkey\WageCategory;
use app\models\manage\searchkey\WageItem;
use tests\codeception\unit\JmTestCase;

class WageItemTest extends JmTestCase
{
    /**
     * attribute labels test
     */
    public function testAttributeLabels()
    {
        $model = new WageItem();
        verify($model->attributeLabels())->notEmpty();
    }

    public function testRules()
    {
        $this->specify('金額空時の検証', function() {
            $model = new WageItem();
            $model->load(['WageItem' => [
                'wage_item_name' => '',
            ]]);
            $model->validate();
            verify($model->hasErrors('wage_item_name'))->true();
        });
        $this->specify('金額数字外の検証', function() {
            $model = new WageItem();
            $model->load(['WageItem' => [
                'wage_item_name' => 'aaa',
            ]]);
            $model->validate();
            verify($model->hasErrors('valid_chk'))->true();
        });
        $this->specify('公開状況空時の検証', function() {
            $model = new WageItem();
            $model->load(['WageItem' => [
                'valid_chk' => null,
            ]]);
            $model->validate();
            verify($model->hasErrors('valid_chk'))->true();
        });
        $this->specify('公開状況数字外の検証', function() {
            $model = new WageItem();
            $model->load(['WageItem' => [
                'valid_chk' => 'aaa',
            ]]);
            $model->validate();
            verify($model->hasErrors('valid_chk'))->true();
        });
        $this->specify('表示金額空時の検証', function() {
            $model = new WageItem();
            $model->load(['WageItem' => [
                'disp_price' => '',
            ]]);
            $model->validate();
            verify($model->hasErrors('disp_price'))->true();
        });
        $this->specify('表示金額最大文字数の検証', function() {
            $model = new WageItem();
            $model->load(['WageItem' => [
                'disp_price' => str_repeat('1',21),
            ]]);
            $model->validate();
            verify($model->hasErrors('disp_price'))->true();
        });
        $this->specify('金額最大値の検証', function() {
            $model = new WageItem();
            $model->load(['WageItem' => [
                'wage_item_name' => 1000000001,
            ]]);
            $model->validate();
            verify($model->hasErrors('wage_item_name'))->true();
        });
        $this->specify('正しい値', function() {
            $model = new WageItem();
            $model->load(['WageItem' => [
                'wage_item_name' => 1000000000,
                'valid_chk' => 1,
                'wage_category_id' => 1,
                'disp_price' => '1',
            ]]);
            verify($model->validate())->true();
        });
    }

    /**
     * 検索結果のある給与を取得するテスト
     */
    public function testWageArray()
    {
        $areas = (new ComArea())->models;
        foreach ((array)$areas as $area) {
            list($prefIds, $prefNos) = Pref::prefIdsPrefNos($area);
            foreach ((array)$prefIds as $index => $prefId) {
                $jobIds = JobMasterDisp::jobIds($prefId);
                foreach ((array)$jobIds as $jobId) {
                    $wageArray = WageItem::wageArray($jobId);
                    foreach ((array)$wageArray as $wageCategory => $wageItems) {
                        // Item配列がCategoryのものかチェック
                        $resCategory = WageItem::find()->select([
                            'wage_category_no',
                        ])->innerJoin(
                            'wage_category',
                            '`wage_item`.`wage_category_id`=`wage_category`.`id`'
                        )->where([
                            'wage_item_no' => $wageItems,
                        ])->distinct()->column();

                        verify(count($resCategory))->equals(1);
                        verify($resCategory[0])->equals($wageCategory);

                        // Categoryが有効なものかチェック
                        $countValidCategory = WageCategory::find()->where([
                            'wage_category_no' => $wageCategory,
                            'valid_chk' => WageCategory::FLAG_VALID,
                        ])->count();

                        verify($countValidCategory)->equals(1);

                        // Itemが有効なものかチェック
                        $countValidItem = WageItem::find()->where([
                            'wage_item_no' => $wageItems,
                            'valid_chk' => WageItem::FLAG_VALID,
                        ])->count();

                        verify($countValidItem)->equals(count($wageItems));

                        // 指定された仕事IDに対するItemかチェック
                        // categoryのvalid_chkを調べていないので、$resWageItemsの方が多く抽出される
                        $resWageItems = WageItem::find()->select([
                            'wage_item_no',
                        ])->innerJoin(
                            'job_wage',
                            '`wage_item`.`id`=`job_wage`.`wage_item_id`'
                        )->where([
                            'job_wage.job_master_id' => $jobId,
                            'wage_item.valid_chk' => WageItem::FLAG_VALID,
                        ])->column();

                        verify(array_diff($wageItems, $resWageItems))->isEmpty();
                    }
                }
            }
        }
    }
}