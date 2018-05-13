<?php
/**
 * Created by PhpStorm.
 * User: mita33
 * Date: 2016/05/12
 * Time: 19:48
 */

namespace app\models\manage;

use yii;
use proseeds\models\BaseModel;

/**
 * This is the model class for table "corp_column_subset".
 *
 * @property integer $id
 * @property integer $tenant_id
 * @property string $column_name
 * @property string $subset_name
 */
class CorpColumnSubset extends BaseModel
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'corp_column_subset';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['tenant_id', 'column_name', 'subset_name'], 'required'],
            [['tenant_id'], 'integer'],
            [['column_name'], 'string', 'max' => 30],
            [['subset_name'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', '主キーID'),
            'tenant_id' => Yii::t('app', 'テナントID'),
            'column_name' => Yii::t('app', 'corp_masterのカラム名'),
            'subset_name' => Yii::t('app', '選択肢項目名'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSetItem()
    {
        return $this->hasOne(CorpColumnSet::className(), ['column_name' => 'column_name', 'tenant_id' => 'tenant_id']);
    }
}
