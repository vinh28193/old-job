<?php

namespace app\models\manage\searchkey;

use Yii;
use yii\data\ActiveDataProvider;

/**
 * This is the model class for table "searchkey_item2".
 *
 * @property integer $id
 * @property integer $tenant_id
 * @property integer $searchkey_category2_id
 * @property string $searchkey_item_name
 * @property string $parameter_name
 * @property integer $sort
 * @property integer $valid_chk
 */
class SearchkeyItem2 extends SearchkeyItem
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'searchkey_item2';
    }
}
