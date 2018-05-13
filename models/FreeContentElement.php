<?php

namespace app\models;

use app\common\Helper\JmUtils;
use app\models\queries\FreeContentElementQuery;
use proseeds\models\BaseModel;
use Yii;
use yii\helpers\Url;

/**
 * This is the model class for table "free_content_element".
 *
 * @property integer $id
 * @property integer $tenant_id
 * @property integer $free_content_id
 * @property integer $type
 * @property string $image_file_name
 * @property string $text
 * @property integer $sort
 */
class FreeContentElement extends BaseModel
{
    /** 画像保存ディレクトリ */
    const DIR_PATH = 'free-content';

    /** typeの選択肢 */
    const TYPE_ONLY_TEXT = 1;
    const TYPE_ONLY_IMG = 2;
    const TYPE_LEFT_IMG = 3;
    const TYPE_LEFT_TEXT = 4;
    const TYPES = [
        self::TYPE_ONLY_TEXT,
        self::TYPE_ONLY_IMG,
        self::TYPE_LEFT_IMG,
        self::TYPE_LEFT_TEXT,
    ];

    /** 最大ファイルサイズ(3MiB) */
    const MAX_SIZE = 1024 * 1024 * 3;

    /** 入力を許可するファイルの拡張子 */
    const FILE_EXTENSIONS = [
        'jpg',
        'jpeg',
        'gif',
        'png',
    ];

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'free_content_element';
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', '主キーID'),
            'tenant_id' => Yii::t('app', 'テナントID'),
            'free_content_id' => Yii::t('app', 'free_contentテーブルのID'),
            'type' => Yii::t('app', '要素タイプ'),
            'image' => Yii::t('app', '画像ファイル名'),
            'text' => Yii::t('app', 'テキスト'),
            'sort' => Yii::t('app', '並び順'),
        ];
    }

    /**
     * fileのurl（ドメインからの相対）を返す
     * @return string
     */
    public function srcUrl():string
    {
        return Url::to([JmUtils::fileUrl(self::DIR_PATH . '/' . $this->image_file_name), 'public' => true]);
    }

    /**
     * @inheritdoc
     * @return FreeContentElementQuery
     */
    public static function find():FreeContentElementQuery
    {
        return new FreeContentElementQuery(get_called_class());
    }
}
