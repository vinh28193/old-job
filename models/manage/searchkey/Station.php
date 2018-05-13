<?php

namespace app\models\manage\searchkey;

use proseeds\models\BaseModel;
use Yii;
use yii\helpers\ArrayHelper;

/**
 * Stationモデル
 *
 * @author Yukinori Nakamura
 *
 * @property integer $railroad_company_cd
 * @property string  $railroad_company_name
 * @property integer $route_cd
 * @property string  $route_name
 * @property integer $station_no
 * @property string  $station_name
 * @property string  $station_name_kana
 * @property integer $sort_no
 * @property integer $pref_no
 *
 * @property string  $routeStationNo
 */
class Station extends BaseModel
{

    /**
     * リスト取得の最大件数
     */
    const MAX_VALUE = 100;

    /**
     * テーブル名の設定
     * @return string テーブル名
     */
    public static function tableName()
    {
        return 'station';
    }

    /**
     * ルールの設定。
     * @return array ルール設定
     */
    public function rules()
    {
        return [
            [
                [
                    'railroad_company_cd',
                    'railroad_company_name',
                    'route_cd',
                    'route_name',
                    'station_no',
                    'station_name',
                    'station_name_kana',
                    'sort_no',
                    'pref_no',
                ],
                'required',
            ],
            [['railroad_company_cd', 'route_cd', 'station_no', 'sort_no', 'pref_no'], 'integer'],
            [['railroad_company_name', 'route_name', 'station_name', 'station_name_kana'], 'string', 'max' => 100],
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
            'railroad_company_cd'   => Yii::t('app', '路線会社コード'),
            'railroad_company_name' => Yii::t('app', '路線会社'),
            'route_cd'              => Yii::t('app', '沿線コード'),
            'route_name'            => Yii::t('app', '沿線名'),
            'station_no'            => Yii::t('app', '駅コード'),
            'station_name'          => Yii::t('app', '駅名'),
            'station_name_kana'     => Yii::t('app', '駅名カナ'),
            'sort_no'               => Yii::t('app', 'ソート順'),
            'pref_no'               => Yii::t('app', '都道府県コード'),
            'valid_chk'             => Yii::t('app', '公開状況'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPref()
    {
        // onConditionに関して。on句にtenant_idがサイトから取得したもので絞っている。
        return $this->hasOne(Pref::className(), [
            'pref_no' => 'pref_no'
        ])->onCondition([Pref::tableName() . '.tenant_id' => Yii::$app->tenant->id]);
    }

    /**
     * 検索キーコードのcsvダウンロードで使われる
     * attributeの配列を生成する
     * @return array
     */
    public function searchkeyCsvAttributes()
    {
        return [
            'station_no',
            'pref.pref_name',
            'route_name',
            'station_name'
        ];
    }

    /**
     * 検索結果のある路線・駅を取得する
     * @param array $prefNos 都道府県Noの配列
     * @param array $jobIds 仕事IDの配列
     * @return array 路線、駅の順にネストされた配列を返す
     */
    public static function stationArray($prefNos, $jobIds)
    {
        $numArray = self::find()
            ->innerJoin('job_station_info', '`station`.`station_no` = `job_station_info`.`station_id`')
            ->select([
                'station.station_no',
                'station.route_cd',
            ])->where([
                'station.pref_no' => $prefNos,
                'job_station_info.job_master_id' => $jobIds,
            ])->distinct()->asArray()->all();

        $array = [];
        foreach ((array)$numArray as $numbers) {
            $array[$numbers['route_cd']][] = $numbers['station_no'];
        }

        return $array;
    }
}
