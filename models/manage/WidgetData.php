<?php

namespace app\models\manage;

use app\common\traits\UploadTrait;
use Exception;
use creocoder\flysystem\Filesystem;
use Yii;
use yii\db\ActiveQuery;
use yii\helpers\ArrayHelper;
use proseeds\models\BaseModel;
use yii\web\UploadedFile;

/**
 * This is the model class for table "widget_data".
 *
 * @property integer $id
 * @property integer $tenant_id
 * @property integer $widget_id
 * @property string $title
 * @property string $pict
 * @property string $description
 * @property integer $sort
 * @property integer $disp_start_date
 * @property integer $disp_end_date
 * @property integer $valid_chk
 *
 * @property Widget $widget
 * @property WidgetDataArea[] $widgetDataArea
 * @property string $movieTag
 * @property array $urls
 * @property array $areaIds
 * @property Filesystem $fileSystem
 */
class WidgetData extends BaseModel
{
    use UploadTrait;

    /** 状態 - 有効or無効 */
    const INVALID = 0;
    const VALID = 1;
    /** url登録方法 - 原稿選択orURL入力 */
    const INPUT_TYPE_URL = 0;
    const INPUT_TYPE_JOB = 1;
    /** 画像パス */
    const DIR_PATH = 'data/content';
    /** sortデフォルト値 */
    const DEFAULT_SORT = 1;
    /** 書き込みシナリオ(ajaxValidationとの区別) */
    const SCENARIO_REGISTER = 'register';

    /** @var array エリア別登録URL */
    private $_urls;
    /** @var string 動画タグ */
    private $_movieTag;
    /** @var array 表示エリアID */
    private $_areaIds;

    /**
     * @return string
     */
    public static function tableName()
    {
        return 'widget_data';
    }

    /**
     * file uploadで使うpropertyを初期化
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
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'tenant_id' => Yii::t('app', 'テナントID'),
            'widget_id' => Yii::t('app', 'ウィジェット名'),
            'title' => Yii::t('app', 'タイトル'),
            'pict' => Yii::t('app', '画像'),
            'description' => Yii::t('app', 'ディスクリプション'),
            'sort' => Yii::t('app', '表示順'),
            'disp_start_date' => Yii::t('app', '公開開始日'),
            'disp_end_date' => Yii::t('app', '公開終了日'),
            'valid_chk' => Yii::t('app', '状態'),

            'areaIds' => Yii::t('app', '表示エリア'),
            'urls' => Yii::t('app', 'リンク先URL'),
            'movieTag' => Yii::t('app', '動画タグ'),
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        $rules = [
            [['tenant_id', 'widget_id'], 'integer'],
            ['sort', 'integer', 'min' => 1, 'max' => 255],
            ['valid_chk', 'boolean'],
            ['description', 'string', 'max' => 200],
            ['title', 'string', 'max' => 100],
            ['movieTag', 'string', 'max' => 255],
            ['pict', 'image', 'maxSize' => 1024 * 1024 * 3/*3MiB*/],
            ['disp_start_date', 'date', 'timestampAttribute' => 'disp_start_date'],
            ['disp_end_date', 'date', 'timestampAttribute' => 'disp_end_date'],
            [
                'disp_end_date',
                'compare',
                'compareAttribute' => 'disp_start_date',
                'operator' => '>=',
                'message' => Yii::t('app', '{attribute}は{compareAttribute}より後の日付にしてください.'),
            ],
            [['widget_id', 'disp_start_date', 'valid_chk', 'sort', 'areaIds'], 'required'],
            // urlsはサーバーサイドではvalidationしない
            [
                'urls',
                'string',
                'max' => 1999,
                'except' => self::SCENARIO_REGISTER,
                'tooLong' => Yii::t('app', '{attribute}は2000文字未満で入力してください.'),
            ],
            ['urls', 'url', 'except' => self::SCENARIO_REGISTER],
            [
                ['title', 'description', 'movieTag'],
                'required',
                'when' => function (self $model, $attribute) {
                    return $model->isRequiredElement($attribute);
                },
            ],
        ];

        // 新規作成の時は画像の必須validationを追加
        if ($this->isNewRecord) {
            $rules = array_merge($rules, [
                [
                    'pict',
                    'required',
                    'when' => function (self $model, $attribute) {
                        return $model->isRequiredElement($attribute);
                    },
                ],
            ]);
        }
        return $rules;
    }

    /**
     * 必須項目かどうかを判定する
     * @param $elementName
     * @return bool
     * @throws Exception
     */
    private function isRequiredElement($elementName)
    {
        if (!$this->widget) {
            throw new Exception('no Widget related');
        }
        return ArrayHelper::isIn(Widget::INPUT_ELEMENTS[$elementName], $this->widget->elements);
    }

    /**
     * 有効無効配列
     * @return array
     */
    public static function validChkArray()
    {
        return [
            self::VALID => Yii::t('app', '有効'),
            self::INVALID => Yii::t('app', '無効'),
        ];
    }

    /**
     * traitのmethodをオーバーライド
     * isRequiredElementの判定を挟むため
     * @return bool
     */
    public function loadFileInfo():bool
    {
        if ($file = $this->fileInstance()) {
            // ファイルがあればファイル名を生成してload
            $this->pict = $this->getRandomFileName($file->extension);
        } elseif (!$this->isNewRecord) {
            // 無くてupdateの場合は旧ファイル名を保持
            $this->pict = $this->getOldAttribute('pict');
        }
        return true;
    }

    /**
     * UploadedFileのインスタンスを返す。
     * ファイルが不要なwidgetだったりpostされたファイルが無かったりする場合はnullを返す
     * @return null|UploadedFile
     */
    public function fileInstance()
    {
        if ($this->isRequiredElement('pict')) {
            return UploadedFile::getInstance($this, 'pict');
        }
        return null;
    }

    /**
     * WidgetDataAreaの更新
     * エリアは大量になることは無いのでbatch insertしていない
     * @return bool
     */
    public function updateRelations():bool
    {
        // エリアIDが無ければfalse
        if (!$this->_areaIds) {
            return false;
        }
        $this->unlinkAll('widgetDataArea', true);
        return $this->saveRelations();
    }

    /**
     * WidgetDataAreaのsave
     * @return bool
     */
    public function saveRelations():bool
    {
        foreach ($this->_areaIds as $areaId) {
            $widgetDataArea = new WidgetDataArea();
            $widgetDataArea->area_id = $areaId;

            $widgetDataArea->url = $this->urls[$areaId] ?? null;
            if ($this->isRequiredElement('movieTag')) {
                $widgetDataArea->movie_tag = $this->movieTag;
            }
            // validationしてOKなら登録
            if (!$widgetDataArea->validate()) {
                return false;
            }
            $this->link('widgetDataArea', $widgetDataArea);
        }
        return true;
    }

    /**
     * @return array
     */
    public function getUrls()
    {
        if (!$this->_urls) {
            $this->_urls = $this->widgetDataArea ? ArrayHelper::map((array)$this->widgetDataArea, 'area_id', 'url') : [];
        }
        return $this->_urls;
    }

    /**
     * @param array $urls
     */
    public function setUrls($urls)
    {
        $this->_urls = $urls;
    }

    /**
     * @return string
     */
    public function getMovieTag()
    {
        if (!$this->_movieTag) {
            $this->_movieTag = ArrayHelper::getValue($this, 'widgetDataArea.0.movie_tag');
        }
        return $this->_movieTag;
    }

    /**
     * @param $v
     */
    public function setMovieTag($v)
    {
        $this->_movieTag = $v;
    }

    /**
     * @return array
     */
    public function getAreaIds()
    {
        if (!$this->_areaIds) {
            $this->_areaIds = ArrayHelper::getColumn($this->widgetDataArea, 'area_id');
        }
        return $this->_areaIds;
    }

    /**
     * @param $v
     */
    public function setAreaIds($v)
    {
        $this->_areaIds = $v;
    }

    /**
     * @return ActiveQuery
     */
    public function getWidgetDataArea()
    {
        return $this->hasMany(WidgetDataArea::className(), ['widget_data_id' => 'id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getWidget()
    {
        return $this->hasOne(Widget::className(), ['id' => 'widget_id']);
    }
}
