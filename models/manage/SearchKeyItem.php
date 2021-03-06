<?php

namespace app\models\manage;

use Yii;

/**
 * This is the model class for table "searchkey_item1".
 *
 * @property integer $id
 * @property integer $tenant_id
 * @property integer $searchkey_category1_id
 * @property string  $searchkey_item_name
 * @property integer $sort
 * @property integer $valid_chk
 */
class SearchKeyItem extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'searchkey_item1';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['tenant_id', 'searchkey_category1_id', 'sort', 'valid_chk'], 'integer'],
            [['searchkey_item_name'], 'required'],
            [['searchkey_item_name'], 'string', 'max' => 50],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id'                     => Yii::t('app', 'ID'),
            'tenant_id'              => Yii::t('app', 'テナントID'),
            'searchkey_category1_id' => Yii::t('app', '外部キー'),
            'searchkey_item_name'    => Yii::t('app', '項目名'),
            'sort'                   => Yii::t('app', '表示順'),
            'valid_chk'              => Yii::t('app', '公開状況'),
        ];
    }
}
