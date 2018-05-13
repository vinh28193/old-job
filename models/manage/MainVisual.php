<?php

namespace app\models\manage;

use app\models\manage\searchkey\Area;
use proseeds\models\BaseModel;
use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "main_visual".
 *
 * @property integer $id
 * @property string $tenant_id
 * @property integer $area_id
 * @property string $type
 * @property integer $valid_chk
 * @property string $memo
 * @property integer $created_at
 * @property integer $updated_at
 *
 * @property Area $area
 * @property MainVisualImage[] $images
 */
class MainVisual extends BaseModel
{
    const TYPE_SLIDE = 'slide';
    const TYPE_BANNER = 'banner';

    /**
     * @return array
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
        ];
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'main_visual';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['tenant_id', 'type', 'valid_chk'], 'required'],
            [['area_id', 'valid_chk'], 'integer'],
            [['memo'], 'string', 'max' => 2000],
            [['type'], 'string', 'max' => 16],
            [
                ['area_id'],
                'exist',
                'skipOnError' => true,
                'targetClass' => Area::className(),
                'targetAttribute' => ['area_id' => 'id']
            ],
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
            'type' => Yii::t('app', '表示形式'),
            'valid_chk' => Yii::t('app', '公開状態'),
            'memo' => Yii::t('app', '管理用メモ'),
            'created_at' => Yii::t('app', '作成日時'),
            'updated_at' => Yii::t('app', '更新日時'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getArea()
    {
        return $this->hasOne(Area::className(), ['id' => 'area_id']);
    }

    /**
     * @param null $className
     * @return \yii\db\ActiveQuery
     */
    public function getImages($className = null)
    {
        return $this->hasMany($className ?? MainVisualImage::className(), ['main_visual_id' => 'id'])
            ->orderBy(['sort' => SORT_ASC]);
    }

    /**
     * ビジュアルパターン
     *
     * @return array
     */
    public static function types()
    {
        return [
            self::TYPE_SLIDE => Yii::t('app', 'スライドショー'),
            self::TYPE_BANNER => Yii::t('app', 'バナー'),
        ];
    }
}
