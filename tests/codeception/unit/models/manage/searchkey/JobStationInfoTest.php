<?php

namespace models\manage;

use app\models\manage\searchkey\JobStationInfo;
use tests\codeception\fixtures\StationFixture;
use tests\codeception\unit\fixtures\JobStationInfoFixture;
use tests\codeception\unit\JmTestCase;

/**
 * Class JobStationInfoTest
 * @package models\manage
 *
 * @property JobStationInfoFixture $job_station_info
 * @property StationFixture $station
 */
class JobStationInfoTest extends JmTestCase
{
    /**
     * 要素テスト
     */
    public function testAttributeLabels()
    {
        $model = new JobStationInfo();
        verify(is_array($model->attributeLabels()))->true();
    }

    /**
     * ルールテスト
     */
    public function testRules()
    {
        $this->specify('数字チェック', function () {
            $model = new JobStationInfo();
            $model->load([$model->formName() => [
                'station_id' => '文字列',
                'transport_type' => '文字列',
                'transport_time' => '文字列',
            ]]);
            $model->validate();
            verify($model->hasErrors('station_id'))->true();
            verify($model->hasErrors('transport_type'))->true();
            verify($model->hasErrors('transport_time'))->true();
        });

        $this->specify('正しい値', function () {
            $model = new JobStationInfo();
            $model->load([$model->formName() => [
                'station_id' => 1,
                'transport_type' => 1,
                'transport_time' => 1,
            ]]);
            verify($model->validate())->true();
        });
    }

//    public function testRelations()
//    {
//        $model = JobStationInfo::findOne(1);
//        $this->specify('getStation', function () use ($model) {
//            verify($model->station)->notEmpty();
//        });
//    }

    /**
     * 駅名セットゲットテスト
     */
//　todo testがおかしいのであとで直す
//    public function testGetStationName()
//    {
//        $id = $this->id(1, 'job_station_info');
//
//        $jobStationInfo = $this->findRecordById($this->job_station_info, $id);
//
//        $station = isset($jobStationInfo) ? $this->findRecordById($this->station, $jobStationInfo['station_id']) : null;
//
//        $textVerify = !is_null($station) ? $station['station_name'] : '';
//
//        $model = JobStationInfo::findOne($id);
//        verify($model->getStationName())->equals($textVerify);
//
//    }

    public function testTransportList()
    {
        $this->specify('徒歩', function () {
            verify(JobStationInfo::getTransportList()[JobStationInfo::TRANSPORT_WALK])->equals('徒歩');
        });
        $this->specify('車・バス', function () {
            verify(JobStationInfo::getTransportList()[JobStationInfo::TRANSPORT_BUS])->equals('車・バス');
        });
    }
}