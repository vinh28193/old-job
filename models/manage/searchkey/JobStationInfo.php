<?php

namespace app\models\manage\searchkey;

use proseeds\models\BaseModel;
use Yii;
use yii\db\ActiveQuery;

/**
 * This is the model class for table "job_station_info".
 *
 * @property integer $id
 * @property integer $tenant_id
 * @property integer $job_master_id
 * @property integer $station_id
 * @property integer $transport_type
 * @property integer $transport_time
 *
 * @property Station $station
 * @property string $stationName
 */
class JobStationInfo extends BaseModel
{
    /** 交通手段 - 徒歩 */
    const TRANSPORT_WALK = 0;

    /** 交通手段 - バス */
    const TRANSPORT_BUS = 1;

    /**
     * テーブル名の設定
     * @return string テーブル名
     */
    public static function tableName()
    {
        return 'job_station_info';
    }

    /**
     * ルールの設定。
     * @return array ルール設定
     */
    public function rules()
    {
        return [
            [[
                'station_id',
                'transport_type',
                'transport_time',
            ], 'integer'],
        ];
    }

    /**
     * 要素のラベル名を設定。
     * function_item_setテーブルから名前を取得し、カラムごとに適用しています。
     * @return array ラベル設定
     */
    public function attributeLabels()
    {
        return [
            'transport_time' => Yii::t('app', '駅からの時間'),
        ];
    }

    /**
     * 駅名のゲッター
     * station_idから駅名を検索して返す。
     * @return string
     */
    public function getStationName()
    {
        return $this->station ? $this->station->station_name : null;
    }

    /**
     * @return ActiveQuery
     */
    public function getStation()
    {
        return $this->hasOne(Station::className(), ['station_no' => 'station_id'])->select(['station_no', 'station_name']);
    }

    /**
     * 交通手段リストを取得する
     * @return array
     */
    public static function getTransportList()
    {
        return [
            self::TRANSPORT_WALK => Yii::t('app', '徒歩'),
            self::TRANSPORT_BUS => Yii::t('app', '車・バス')
        ];
    }
}
