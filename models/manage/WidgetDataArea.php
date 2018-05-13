<?php

namespace app\models\manage;

use proseeds\models\BaseModel;
use Yii;

/**
 * This is the model class for table "widget_data_area".
 *
 * @property integer $id
 * @property integer $widget_data_id
 * @property integer $area_id
 * @property string $url
 * @property string $movie_tag
 *
 * @property WidgetData $widgetData
 */
class WidgetDataArea extends BaseModel
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'widget_data_area';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['tenant_id', 'widget_data_id', 'area_id'], 'integer'],
            ['movie_tag', 'string', 'max' => 255],
            ['url', 'string', 'max' => 2000],
            ['url', 'url'],
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
            'widget_data_id' => Yii::t('app', 'widget_dataのID'),
            'area_id' => Yii::t('app', 'areaのID'),
            'url' => Yii::t('app', 'URL'),
            'movie_tag' => Yii::t('app', '動画タグ'),
        ];
    }
    
    /**
     * ウィジェットデータリレーション
     * @return \yii\db\ActiveQuery
     */
    public function getWidgetData()
    {
        return $this->hasOne(WidgetData::className(), ['id' => 'widget_data_id']);
    }

}
