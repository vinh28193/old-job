<?php

namespace app\models;

use app\models\queries\FreeContentQuery;
use proseeds\models\BaseModel;
use Yii;
use yii\helpers\Url;

/**
 * This is the model class for table "free_content".
 *
 * @property integer $id
 * @property integer $tenant_id
 * @property string $title
 * @property string $keyword
 * @property string $description
 * @property string $url_directory
 * @property integer $valid_chk
 * @property FreeContentElement[]|null $elements
 * @property string $url
 */
class FreeContent extends BaseModel
{
    /** 有効or無効 */
    const VALID = 1;
    const INVALID = 0;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'free_content';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['valid_chk', 'title', 'url_directory'], 'required'],
            [['valid_chk'], 'boolean'],
            [['title', 'keyword', 'description'], 'string', 'max' => 255],
            [['url_directory'], 'string', 'max' => 30],
            [['url_directory'], 'unique'],
            [['url_directory'], 'match', 'pattern' => '/^[\w-]+$/i', 'message' => Yii::t('app', 'URLに使えない文字が含まれています')],
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
            'url_directory' => Yii::t('app', 'コンテンツURL'),
            'title' => Yii::t('app', 'ページタイトル'),
            'keyword' => Yii::t('app', 'キーワード'),
            'description' => Yii::t('app', 'ディスクリプション'),
            'valid_chk' => Yii::t('app', '公開状況'),
            'url' => Yii::t('app', 'URL'),
        ];
    }

    /**
     * @inheritdoc
     * @return FreeContentQuery
     */
    public static function find()
    {
        return new FreeContentQuery(get_called_class());
    }

    /**
     * valid_checkの選択肢のラベルを返す
     * @return array
     */
    public static function validArray()
    {
        return [
            static::VALID => Yii::t('app', '公開'),
            static::INVALID => Yii::t('app', '非公開'),
        ];
    }

    /**
     * ElementFormのrelation
     * @return \yii\db\ActiveQuery
     */
    public function getElements()
    {
        return $this->hasMany(FreeContentElement::className(), ['free_content_id' => 'id'])->orderBy('sort');
    }

    /**
     * urlを表示する
     * @return string
     */
    public function getUrl()
    {
        return Url::toRoute(['/contents/' . $this->url_directory], true);
    }
}
