<?php
/**
 * Created by PhpStorm.
 * User: proseeds
 * Date: 2016/09/13
 * Time: 11:28
 */

namespace app\modules\manage\controllers\secure;

use yii;
use app\models\manage\SearchkeyMaster;
use app\modules\manage\controllers\CommonController;
use yii\helpers\ArrayHelper;

class CsvHelperController extends CommonController
{
    const PLAN     = 1;
    const DIST     = 2;
    const STATION  = 3;
    const WAGE     = 4;
    const JOB_TYPE = 5;
    const CATE_1   = 6;
    const CATE_2   = 7;
    const CATE_3   = 8;
    const CATE_4   = 9;
    const CATE_5   = 10;
    const CATE_6   = 11;
    const CATE_7   = 12;
    const CATE_8   = 13;
    const CATE_9   = 14;
    const CATE_10  = 15;
    const ITEM_11  = 16;
    const ITEM_12  = 17;
    const ITEM_13  = 18;
    const ITEM_14  = 19;
    const ITEM_15  = 20;
    const ITEM_16  = 21;
    const ITEM_17  = 22;
    const ITEM_18  = 23;
    const ITEM_19  = 24;
    const ITEM_20  = 25;

    /**
     * TODO:要修正
     * helperType + TO_NO = searchkey_no
     */
    const TO_NO = 1;

    const HELPS = [
        self::PLAN     => 'plan',
        self::DIST     => 'pref',
        self::STATION  => 'station',
        self::WAGE     => 'wage_category',
        self::JOB_TYPE => 'job_type_category',
        self::CATE_1   => 'searchkey_category1',
        self::CATE_2   => 'searchkey_category2',
        self::CATE_3   => 'searchkey_category3',
        self::CATE_4   => 'searchkey_category4',
        self::CATE_5   => 'searchkey_category5',
        self::CATE_6   => 'searchkey_category6',
        self::CATE_7   => 'searchkey_category7',
        self::CATE_8   => 'searchkey_category8',
        self::CATE_9   => 'searchkey_category9',
        self::CATE_10  => 'searchkey_category10',
        self::ITEM_11  => 'searchkey_item11',
        self::ITEM_12  => 'searchkey_item12',
        self::ITEM_13  => 'searchkey_item13',
        self::ITEM_14  => 'searchkey_item14',
        self::ITEM_15  => 'searchkey_item15',
        self::ITEM_16  => 'searchkey_item16',
        self::ITEM_17  => 'searchkey_item17',
        self::ITEM_18  => 'searchkey_item18',
        self::ITEM_19  => 'searchkey_item19',
        self::ITEM_20  => 'searchkey_item20',
    ];

    /**
     * @param int|null $helperType
     * @return string
     * @throws yii\web\NotFoundHttpException
     */
    public function actionJob($helperType = null)
    {
        $searchkeyMaster = $this->getModel($helperType);

        $searchKeys = Yii::$app->searchKey->searchKeys;
        $pullDown = array_filter(array_map(function ($key) use ($searchKeys) {
            if ($key == 'plan') {
                return Yii::t('app', '料金プラン');
            } else {
                /** @var SearchkeyMaster|null $model */
                $model = ArrayHelper::getValue($searchKeys, $key);
                return ($model) ? $model->searchkey_name : null;
            }
        }, self::HELPS));

        return $this->render('job', [
            'searchkeyMaster' => $searchkeyMaster,
            'helperType' => $helperType,
            'pullDown' => $pullDown,
        ]);
    }

    /**
     * SearchkeyMasterモデルを返す。
     * 検索キーコードに紐づいたモデルを取得するため(viewでその処理を行っている）
     * @param integer $helperType
     * @return SearchkeyMaster|Null
     * @throws yii\web\NotFoundHttpException
     */
    private function getModel($helperType)
    {
        if (isset($helperType) && in_array($helperType, array_keys(self::HELPS))) {
            $searchkeyMaster = ArrayHelper::getValue(Yii::$app->searchKey->searchKeys, self::HELPS[$helperType]);
        } else {
            // 値がセットされていない、またはgetパラメータの値が指定のもの以外の場合、404エラーを返す(暫定)
            throw new yii\web\NotFoundHttpException();
        }
        return $searchkeyMaster;
    }

    /**
     * 検索キーNoから、HelperTypeのIDを返す
     * @param integer $keyNo
     * @return integer
     */
    public static function toHelperNo($keyNo)
    {
        return $keyNo - self::TO_NO;
    }
}