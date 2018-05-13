<?php

namespace app\models\manage;

use proseeds\models\BaseModel;
use Yii;

/**
 * This is the model class for table "main_visual_image".
 *
 * @property integer $id
 * @property integer $tenant_id
 * @property integer $main_visual_id
 * @property string $file_name
 * @property string $file_name_sp
 * @property string $url
 * @property string $url_sp
 * @property string $content
 * @property integer $sort
 * @property integer $valid_chk
 * @property string $memo
 * @property integer $created_at
 * @property integer $updated_at
 *
 * @property string $filePath
 * @property string $filePathSp
 *
 * @property MainVisual $mainVisual
 */
class MainVisualImage extends BaseModel
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'main_visual_image';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['tenant_id', 'main_visual_id', 'valid_chk'], 'required'],
            [['sort'], 'required'],
            [['main_visual_id', 'valid_chk'], 'integer'],
            [['memo'], 'string'],
            [['url', 'url_sp'], 'url'],
            [['url', 'url_sp', 'file_name', 'file_name_sp'], 'string', 'max' => 256],
            [['content'], 'string', 'max' => 64],
            [
                ['main_visual_id'],
                'exist', 'skipOnError' => true,
                'targetClass' => MainVisual::className(),
                'targetAttribute' => ['main_visual_id' => 'id']
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
            'main_visual_id' => Yii::t('app', 'メインビジュアルID'),
            'file_name' => Yii::t('app', 'ファイル名'),
            'file_name_sp' => Yii::t('app', 'SP向け画像ファイル名'),
            'url' => Yii::t('app', 'PCリンク先URL'),
            'url_sp' => Yii::t('app', 'スマホリンク先URL'),
            'content' => Yii::t('app', 'altテキスト'),
            'sort' => Yii::t('app', '並び順'),
            'valid_chk' => Yii::t('app', '公開状態'),
            'memo' => Yii::t('app', '管理用メモ'),
            'created_at' => Yii::t('app', '作成日時'),
            'updated_at' => Yii::t('app', '更新日時'),
        ];
    }

    /**
     * @return string
     */
    public function getFilePath()
    {
        return WidgetData::DIR_PATH . '/' . $this->file_name;
    }

    /**
     * @return string
     */
    public function getFilePathSp()
    {
        return WidgetData::DIR_PATH . '/' . $this->file_name_sp;
    }

    /**
     * @param $extension
     * @return string
     */
    public static function generateFileName($extension)
    {
        return 'main_visual' . '_' . md5(uniqid()) . '.' . $extension;
    }

    /**
     * @param null $className
     * @return \yii\db\ActiveQuery
     */
    public function getMainVisual($className = null)
    {
        return $this->hasOne($className ?? MainVisual::className(), ['id' => 'main_visual_id']);
    }
}
