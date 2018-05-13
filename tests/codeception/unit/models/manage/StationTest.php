<?php
namespace models\manage;

use app\components\Area as ComArea;
use app\models\JobMasterDisp;
use app\models\manage\searchkey\JobStationInfo;
use app\models\manage\searchkey\Pref;
use tests\codeception\unit\JmTestCase;
use app\models\manage\searchkey\Station;

class StationTest extends JmTestCase
{
    /**
     * 一応
     */
    public function testTableName()
    {
        $model = new Station();
        verify($model->tableName())->equals('station');
    }

    /**
     * 一応
     */
    public function testAttributeLabels()
    {
        $model = new Station();
        verify($model->attributeLabels())->notEmpty();
    }

    /**
     * rulesのtest
     */
    public function testRules()
    {
        $this->specify('数字チェック', function () {
            $model = new Station();
            $model->load([$model->formName() => [
                'railroad_company_cd' => '文字列',
                'route_cd' => '文字列',
                'station_no' => '文字列',
                'sort_no' => '文字列',
                'pref_no' => '文字列',
            ]]);
            $model->validate();
            verify($model->hasErrors('railroad_company_cd'))->true();
            verify($model->hasErrors('route_cd'))->true();
            verify($model->hasErrors('station_no'))->true();
            verify($model->hasErrors('pref_no'))->true();
            verify($model->hasErrors('sort_no'))->true();
        });

        $this->specify('必須チェック', function () {
            $model = new Station();
            $model->load([$model->formName() => [
                'railroad_company_cd' => null,
                'railroad_company_name' => null,
                'route_cd' => null,
                'route_name' => null,
                'station_no' => null,
                'station_name' => null,
                'station_name_kana' => null,
                'sort_no' => null,
                'pref_no' => null,
            ]]);
            $model->validate();
            verify($model->hasErrors('railroad_company_cd'))->true();
            verify($model->hasErrors('railroad_company_name'))->true();
            verify($model->hasErrors('route_cd'))->true();
            verify($model->hasErrors('route_name'))->true();
            verify($model->hasErrors('station_no'))->true();
            verify($model->hasErrors('station_name'))->true();
            verify($model->hasErrors('station_name_kana'))->true();
            verify($model->hasErrors('sort_no'))->true();
            verify($model->hasErrors('pref_no'))->true();
        });
        $this->specify('正しい値', function () {
            $model = new Station();
            $model->load([$model->formName() => [
                'railroad_company_cd'   => '1',
                'railroad_company_name' => str_repeat('a', 100),
                'route_cd'              => '1',
                'route_name'            => str_repeat('a', 100),
                'station_no'            => '1',
                'station_name'          => str_repeat('a', 100),
                'station_name_kana'     => str_repeat('a', 100),
                'sort_no'               => '1',
                'pref_no'               => '1',
                'valid_chk'             => '1',
            ]]);
            verify($model->validate())->true();
        });

        $this->specify('文字列の最大', function () {
            $model = new Station();
            $model->load([$model->formName() => [
                'railroad_company_name' => str_repeat('a', 101),
                'route_name' => str_repeat('a', 101),
                'station_name' => str_repeat('a', 101),
                'station_name_kana' => str_repeat('a', 101),
            ]]);
            $model->validate();
            verify($model->hasErrors('railroad_company_name'))->true();
            verify($model->hasErrors('route_name'))->true();
            verify($model->hasErrors('station_name'))->true();
            verify($model->hasErrors('station_name_kana'))->true();
        });
    }

    /**
     * 検索結果のある路線・駅を取得するテスト
     */
    public function testStationArray()
    {
        $areas = (new ComArea())->models;
        foreach ((array)$areas as $area) {
            list($prefIds, $prefNos) = Pref::prefIdsPrefNos($area);
            foreach ((array)$prefIds as $index => $prefId) {
                $jobIds = JobMasterDisp::jobIds($prefId);
                foreach ((array)$jobIds as $jobId) {
                    $stationArray = Station::stationArray($prefNos[$index], $jobId);

                    foreach ((array)$stationArray as $route => $stations) {
                        // 路線に存在する駅か
                        // 路線の駅をすべて取得して比較する
                        $stationNosBelong = Station::find()->select([
                            'station_no',
                        ])->where([
                            'route_cd' => $route,
                        ])->column();

                        // 路線に無い駅が存在しなければEmpty
                        verify(array_diff($stations, $stationNosBelong))->isEmpty();

                        // 指定の仕事IDに対する駅か
                        // 仕事IDに紐づく駅をすべて取得して比較する
                        $stationNosForJob = JobStationInfo::find()->select([
                            'station_no',
                        ])->innerJoin(
                            'station',
                            '`job_station_info`.`station_id`=`station`.`station_no`'
                        )->where([
                            'job_master_id' => $jobId,
                            'pref_no' => $prefNos[$index],
                        ])->column();

                        // 仕事IDと紐付かない駅が存在しなければEmpty
                        verify(array_diff($stations, $stationNosForJob))->isEmpty();

                    }
                }
            }
        }
    }
}