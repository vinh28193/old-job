<?php

namespace app\models\manage\searchkey;

use Yii;
use yii\helpers\ArrayHelper;
use yii\data\ActiveDataProvider;

/**
 * This is the model class for table "searchkey_category9".
 *
 * @property integer $id
 * @property integer $tenant_id
 * @property string $searchkey_category_name
 * @property integer $sort
 * @property integer $valid_chk
 * @property integer $select_type
 * @property integer $search_type
 */
class SearchkeyCategory9 extends SearchkeyCategory
{
    public $searchKeyCategoryNo = 9;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'searchkey_category9';
    }

    /**
     * itemとのリレーション
     * @return \yii\db\ActiveQuery
     */
    public function getsearchkeyItem()
    {
        return $this->hasMany(SearchkeyItem9::className(),['searchkey_category_id' => 'id'])->orderBy(['sort' => SORT_ASC]);
    }
}
