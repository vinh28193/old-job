<?php

namespace app\models\manage;

use Yii;
use proseeds\models\BaseModel;

/**
 * This is the model class for table "widget_layout".
 *
 * @property integer $id
 * @property integer $tenant_id
 * @property integer $area_flg
 * @property integer $widget_layout_no
 *
 * @property Widget[] $widget
 */
class WidgetLayout extends BaseModel
{
    /** エリア - 全国or全国含めた全エリア */
    const AREA_NATIONWIDE = 0;
    const AREA_COMMON = 1;
    /** widgetLayoutに対応する番号 1～6*/
    const WIDGET_LAYOUT_NO_1 = 1; // 1
    const WIDGET_LAYOUT_NO_2 = 2; // 2-1
    const WIDGET_LAYOUT_NO_3 = 3; // 2-2
    const WIDGET_LAYOUT_NO_4 = 4; // 2-3
    const WIDGET_LAYOUT_NO_5 = 5; // 2-4
    const WIDGET_LAYOUT_NO_6 = 6; // 3

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'widget_layout';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['tenant_id'], 'required'],
            [['tenant_id', 'area_flg', 'widget_layout_no'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'tenant_id' => Yii::t('app', 'テナントID'),
            'area_flg' => Yii::t('app', '全国、エリア判別(全国TOP：0、各エリアTOP共通レイアウト:1)'),
            'widget_layout_no' => Yii::t('app', 'ウィジェットレイアウトナンバー(1～6)'),
        ];
    }
    
    /**
     * widgetへのリレーション
     * @return \yii\db\ActiveQuery
     */
    public function getWidget()
    {
        return $this->hasMany(Widget::className(), ['widget_layout_id' => 'id']);
    }
}
