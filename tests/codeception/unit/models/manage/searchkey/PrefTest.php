<?php

namespace models\manage\searchkey;

use app\components\Area as ComArea;
use app\models\JobMasterDisp;
use app\models\manage\searchkey\Area;
use app\models\manage\searchkey\Dist;
use app\models\manage\searchkey\Pref;
use app\models\manage\searchkey\PrefDistMaster;
use tests\codeception\unit\JmTestCase;

class PrefTest extends JmTestCase
{
    /**
     * attribute labels test
     */
    public function testAttributeLabels()
    {
        $model = new Pref();
        verify($model->attributeLabels())->notEmpty();
    }

    public function testRules()
    {
        $this->specify('都道府県名空時の検証', function () {
            $model = new Pref();
            $model->load(['Pref' => [
                'pref_name' => '',
            ]]);
            $model->validate();
            verify($model->hasErrors('pref_name'))->true();
        });
        $this->specify('都道府県名最大文字数の検証', function () {
            $model = new Pref();
            $model->load(['Pref' => [
                'pref_name' => str_repeat('1', 51),
            ]]);
            $model->validate();
            verify($model->hasErrors('pref_name'))->true();
        });
        $this->specify('表示順空時の検証', function () {
            $model = new Pref();
            $model->load(['Pref' => [
                'sort' => '',
            ]]);
            $model->validate();
            verify($model->hasErrors('sort'))->true();
        });
        $this->specify('正しい値', function () {
            $model = new Pref();
            $model->load(['Pref' => [
                'pref_name' => '文字列',
                'sort' => 1,
                'valid_chk' => 1,
                'pref_no' => 1,
            ]]);
            verify($model->validate())->true();
        });
    }

    public function testPrefList()
    {
        $this->specify('全部返す場合', function () {
            $prefList = Pref::getPrefList();
            // テナント内の都道府県がすべて含まれていることを検証
            $count = Pref::find()->count();
            verify($prefList)->count((int)$count);
            // id順であることを検証
            $lastId = 0;
            foreach ($prefList as $id => $prefName) {
                verify($id)->greaterThan($lastId);
                $lastId = $id;
            }
        });

        $this->specify('有効なエリアのものだけ返す場合', function () {
            $prefList = Pref::getPrefList(true);
            $lastId = 0;
            foreach ($prefList as $id => $prefName) {
                // id順であることを検証
                verify($id)->greaterThan($lastId);
                $lastId = $id;
                // 有効なareaに紐づいていることを検証
                $pref = Pref::findOne($id);
                verify($pref->area->valid_chk)->equals(Area::FLAG_VALID);
            }
        });
    }

    /**
     * 指定エリア内prefのid、pref_noを取得するテスト
     */
    public function testPrefIdsPrefNos()
    {
        $areas = (new ComArea())->models;
        foreach ((array)$areas as $area) {
            /** @var Area $area */
            list($ids, $nos) = Pref::prefIdsPrefNos($area);
            $prefList = Pref::find()->where([
                Pref::tableName() . '.area_id' => $area->id
            ])->all();
            $count = count($prefList);
            verify($ids)->count((int)$count);
            verify($nos)->count((int)$count);
            foreach ((array)$prefList as $index => $pref) {
                /** @var Pref $pref */
                verify($ids[$index])->equals($pref->id);
                verify($nos[$index])->equals($pref->pref_no);
            }
        }
    }

    /**
     * 検索結果のある勤務地を取得するテスト
     */
    public function testPrefArray()
    {
        $areas = (new ComArea())->models;
        foreach ((array)$areas as $area) {
            list($prefIds, $prefNos) = Pref::prefIdsPrefNos($area);
            foreach ((array)$prefIds as $index => $prefId) {
                $jobIds = JobMasterDisp::jobIds($prefId);
                foreach ((array)$jobIds as $jobId) {
                    $prefArray = Pref::prefArray($area, [$jobId]);

                    // 仕事IDに紐づく県かどうかのチェック
                    // 仕事IDに紐づく県をすべて取得して比較する
                    $prefByJobId = Pref::find()->select([
                        'pref_no',
                    ])->innerJoin(
                        'job_pref',
                        '`pref`.`id`=`job_pref`.`pref_id`'
                    )->where([
                        'job_pref.job_master_id' => $jobId,
                    ])->column();

                    $prefList = array_keys($prefArray);
                    verify(array_diff($prefList, $prefByJobId))->isEmpty();

                    $distTotal = [];
                    foreach ((array)$prefArray as $prefNo => $prefDistMasterArray) {

                        // 県に属する地域かどうかチェック
                        // 県に属するすべての地域を取得して比較する
                        $regions = array_keys($prefDistMasterArray);
                        $prefDistMasters = PrefDistMaster::find()->select([
                            'pref_dist_master_no',
                        ])->innerJoin(
                            'pref',
                            '`pref_dist_master`.`pref_id`=`pref`.`id`'
                        )->where([
                            'pref.pref_no' => $prefNo,
                        ])->column();

                        // 県に属さない地域が存在しなければEmpty
                        verify(array_diff($regions, $prefDistMasters))->isEmpty();

                        $distSubTotal = [];
                        foreach ((array)$prefDistMasterArray as $prefDistMasterNo => $distArray) {
                            // 地域に属する市区町村かどうかチェック
                            // 地域に属する市区町村をすべて取得して比較する
                            $distInRegion = Dist::find()->select([
                                'dist_cd',
                            ])->innerJoin(
                                'pref_dist',
                                '`dist`.`dist_cd`=`pref_dist`.`dist_id`'
                            )->innerJoin(
                                'pref_dist_master',
                                '`pref_dist`.`pref_dist_master_id`=~`pref_dist_master`.`id`'
                            )->where([
                                'pref_dist_master.pref_dist_master_no' => $prefDistMasterNo,
                            ])->column();

                            // 地域に属さない市区町村が存在しなければEmpty
                            verify(array_diff($distArray, $distInRegion))->isEmpty();

                            $distSubTotal = array_merge($distSubTotal, $distArray);
                        }

                        // 県に属する市区町村か
                        // 県に属する市区町村をすべて取得して比較する
                        $distInPref = Dist::find()->select([
                            'dist_cd',
                        ])->where([
                            'pref_no' => $prefNo,
                        ])->column();

                        // 県に属さない市区町村が存在しなければEmpty
                        verify(array_diff($distTotal, $distInPref))->isEmpty();

                        $distTotal = array_merge($distTotal, $distSubTotal);
                    }

                    // 仕事IDに紐づく市区町村か
                    // 仕事IDに紐づかない市区町村が存在しなければEmpty
                    $distByJobId = Dist::find()->select([
                        'dist_cd',
                    ])->innerJoin(
                        'job_dist',
                        '`dist`.`id`=`job_dist`.`dist_id`'
                    )->where([
                        'job_dist.job_master_id' => $jobId,
                    ])->column();
                    verify(array_diff($distTotal, $distByJobId))->isEmpty();
                }
            }
        }
    }
}
