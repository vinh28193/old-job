<?php

namespace app\models\manage;

use Yii;

/**
 * This is the model class for table "searchkey_category1".
 *
 * @property integer $id
 * @property integer $tenant_id
 * @property string  $searchkey_category_name
 * @property integer $sort
 * @property integer $valid_chk
 */
class SearchKeyCategory extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'searchkey_category1';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['tenant_id', 'sort', 'valid_chk'], 'integer'],
            [['searchkey_category_name'], 'required'],
            [['searchkey_category_name'], 'string', 'max' => 50],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id'                      => Yii::t('app', 'ID'),
            'tenant_id'               => Yii::t('app', 'テナントID'),
            'searchkey_category_name' => Yii::t('app', 'カテゴリ名'),
            'sort'                    => Yii::t('app', '表示順'),
            'valid_chk'               => Yii::t('app', '公開状況'),
        ];
    }
}
