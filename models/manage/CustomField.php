<?php

namespace app\models\manage;

use app\common\Helper\Html;
use app\common\traits\UploadTrait;
use Yii;
use yii\web\UploadedFile;
use proseeds\models\BaseModel;

/**
 * This is the model class for table "custom_field".
 *
 * @property integer $id
 * @property integer $tenant_id
 * @property integer $custom_no
 * @property string $detail
 * @property string $url
 * @property string $pict
 * @property boolean $valid_chk
 * @property integer $created_at
 * @property integer $updated_at
 * @property CustomField[] $models
 */
class CustomField extends BaseModel
{
    use UploadTrait{
        deleteOldFile as protected traitDeleteOldFile;
        load as protected traitLoad;
    }

    /** 公開状況 - 無効 */
    const INVALID = 0;
    /** 公開状況 - 有効 */
    const VALID = 1;
    /** 画像保存パス */
    const DIR_PATH = 'data/custom';
    /** 最大サイズ */
    const MAX_SIZE = 1024 * 1024 * 3;
    /** 画像拡張子 */
    const FILE_EXTENSIONS = ['jpg', 'jpeg', 'gif', 'png'];
    /** csvシナリオ */
    const SCENARIO_CSV = 'csv';

    /** @var  UploadedFile */
    public $pictFile;
    /** @var  string */
    public $oldPictFile;

    /**
     * テーブル名
     * @return string テーブル名
     */
    public static function tableName()
    {
        return 'custom_field';
    }

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        $this->uploadInit(self::DIR_PATH, true);
        $this->fileAttributes = [
            'file' => 'pict',
            'name' => 'pict',
        ];
    }

    /**
     * ルール設定
     * @return array ルールの構成
     */
    public function rules()
    {
        return [
            [['custom_no', 'detail', 'url', 'valid_chk'], 'required'],
            [['tenant_id', 'custom_no', 'created_at', 'updated_at'], 'integer'],
            [['custom_no', 'url'], 'unique'],
            ['detail', 'string', 'max' => 500],
            ['url', 'string', 'max' => 2000],
            ['url', 'match', 'pattern' => '/^(?::\d{1,5})?(?:$|[?\/#])/i'], // URLバリデーションから一部抜粋したパターン
            ['pict', 'image', 'maxSize' => self::MAX_SIZE, 'extensions' => implode(',', self::FILE_EXTENSIONS)],
            ['valid_chk', 'boolean'],
            ['pict', 'string', 'max' => 255, 'on' => self::SCENARIO_CSV],
        ];
    }

    /**
     * 要素の名前設定
     * @return array 要素のラベル設定
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'tenant_id' => Yii::t('app', 'テナントID'),
            'custom_no' => Yii::t('app', 'No.'),
            'detail' => Yii::t('app', '表示内容'),
            'url' => Yii::t('app', 'URL'),
            'pict' => Yii::t('app', '画像'),
            'valid_chk' => Yii::t('app', '公開状況'),
        ];
    }

    /**
     * 公開情報の値の名前設定
     * @return array 公開情報の値のラベル設定
     */
    public static function validChkLabel():array
    {
        return [
            self::INVALID => Yii::t('app', '非公開'),
            self::VALID => Yii::t('app', '公開'),
        ];
    }

    /**
     * バリデーション前処理
     * @return bool
     */
    public function beforeValidate()
    {
        if (!parent::beforeValidate()) {
            return false;
        }

        if ($this->isNewRecord && !$this->custom_no) {
            $this->custom_no = self::find()->max('custom_no') + 1;
        }
        return true;
    }

    /**
     * csvの時はloadFileInfoの影響を排除
     * @param array $data
     * @param null $formName
     * @return bool
     */
    public function load($data, $formName = null)
    {
        if ($this->scenario == self::SCENARIO_CSV) {
            return parent::load($data, $formName);
        }
        return $this->traitLoad($data, $formName = null);
    }

    /**
     * isUsedPictの判定を足すためにオーバーライドしている
     * @return bool
     */
    public function deleteOldFile():bool
    {
        if (!static::isUsedPict($this->oldFileName)) {
            return $this->traitDeleteOldFile();
        }
        return false;
    }

    /**
     * pictファイルが他で使われているかどうか返す
     * レコード削除後に実行しないと正常に動作しない
     * todo query発行回数やばいのでファイル名をキャッシュする方法を考える
     * @param null|string $pict チェックしたいファイル名
     * @return bool 使われているときtrueを返す
     */
    public static function isUsedPict($pict):bool
    {
        $count = self::find()->where(['pict' => $pict])->count('id');
        return $count? true: false;
    }

    /**
     * URLが一致するデータに応じた、カスタムフィールドを返す
     * @param string $url リクエストされたURL
     * @return string HTML
     */
    public static function customFieldHtml($url):string
    {
        /** @var self|null $custom */
        $custom = self::find()
            ->where(['valid_chk' => self::VALID])
            ->andWhere(['url' => $url])
            ->one();

        if (isset($custom)) {
            $image = $custom->srcUrl() ? Html::tag('div', Html::img($custom->srcUrl()), ['class' => 'resultCustomField__image']) : '';
            $detail = Html::tag('p', Html::encode($custom->detail), ['class' => 'resultCustomField__text']);
            $html = <<<HTML
<div class="resultCustomField clearfix">$image$detail</div>
HTML;
            return $html;
        } else {
            return '';
        }
    }

    /**
     * 有効なURLを全て取得する
     * @return array
     */
    public static function allUrls()
    {
        return self::find()->where([
            'valid_chk' => self::VALID,
        ])->select([
            'url',
        ])->column();
    }
}
