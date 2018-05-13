<?php

namespace app\modules\manage\models\forms;

use app\common\Helper\JmUtils;
use app\models\FreeContentElement;
use creocoder\flysystem\Filesystem;
use proseeds\base\Tenant;
use Yii;
use yii\helpers\ArrayHelper;
use yii\web\UploadedFile;

/**
 * 管理画面のフリーコンテンツ編集画面用モデル
 *
 * @property int $displayItem
 * @property int $layout
 * @property bool $hasImg
 */
class FreeContentElementForm extends FreeContentElement
{
    /** $displayItemの選択肢 */
    const DISPLAY_TEXT = 1;
    const DISPLAY_IMG = 2;
    const DISPLAY_BOTH = 3;

    /** $layoutの選択肢 */
    const IMG_IS_LEFT = 1;
    const TEXT_IS_LEFT = 2;

    /** 推奨画像幅 */
    const WIDTH_ONLY_IMG = 1080;
    const WIDTH_BOTH = 340;

    /** 画像があるときの$hasImgの値 */
    const HAS_IMG = true;

    /** @var UploadedFile アップロードしたファイル */
    public $imgFile;
    /** @var string プレビュー表示するためのbase64変換された画像情報 */
    public $base64Img;

    /** @var int 画像とテキストの配置 */
    private $_displayItem;
    /** @var int 画像とテキストの配置 */
    private $_layout;

    /** @var array */
    private static $_existingFileNames;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        // 画像が必要なタイプで引き継ぐ画像名も無い場合、画像ファイルが必須
        $displayText = self::DISPLAY_TEXT;
        $whenClientForImgFile = <<<JS
function(attribute, value) {
  var displayItemId = attribute.id.replace(/-imgfile/g , '-displayitem');
  var fileNameId = attribute.id.replace(/-imgfile/g, "-image_file_name");
  return $('#' + displayItemId).val() != {$displayText} && !$('#' + fileNameId).val();
}
JS;
        // テキストが必要なタイプの場合、テキストが必須
        $displayImg = self::DISPLAY_IMG;
        $whenClientForText = <<<JS
function(attribute, value) {
  var displayItemId = attribute.id.replace(/-text/g , '-displayitem');
  return $('#' + displayItemId).val() != {$displayImg};
}
JS;

        return [
            [['displayItem', 'layout', '!type', '!sort'], 'required'],
            [
                'displayItem',
                'in',
                'range' => array_keys(static::displayItemLabels()),
                'message' => Yii::t('app', '{attribute}が不正です'),
            ],
            [
                'layout',
                'in',
                'range' => array_keys(static::layoutLabels()),
                'message' => Yii::t('app', '{attribute}が不正です'),
            ],
            [['base64Img', 'text'], 'string'],
            [['image_file_name'], 'string', 'max' => 255, 'message' => Yii::t('app', '不正な値が送信されました')],
            [
                'image_file_name',
                function ($attribute, $params, $validator) {
                /** @see FreeContentElementFromTest */
                    if ($this->isRequiredImage() && ( // 画像が必要なタイプの時
                            ($this->imgFile && $this->isNotExistingFileName()) || // post fileがあって新規ファイル名が入っている、もしくは
                            (!$this->imgFile && $this->isExistingFileName()) // post fileが無くて既存のファイル名が入っている
                        )) {
                        // ならばOK
                        return;
                    }
                    // 画像が必要なタイプでない時はimage_file_nameとpost画僧は空
                    if (!$this->isRequiredImage() && JmUtils::isEmpty($this->image_file_name) && !$this->imgFile) {
                        // ならばOK
                        return;
                    }
                    // それ以外はエラー
                    $this->addError($attribute, Yii::t('app', '不正な値が送信されました'));
                },
                'skipOnEmpty' => false,
            ],
            [
                'text',
                'string',
                'max' => 5000,
                'when' => function () {
                    return $this->isRequiredText();
                },
                'whenClient' => $whenClientForText,
            ],
            [
                'text',
                'required',
                'when' => function () {
                    return $this->isRequiredText();
                },
                'whenClient' => $whenClientForText,
            ],
            [
                'imgFile',
                'required',
                'when' => function () {
                    return $this->isRequiredImage() && !$this->image_file_name;
                },
                'whenClient' => $whenClientForImgFile,
            ],
            [
                'imgFile',
                'image',
                'maxSize' => self::MAX_SIZE,
                'extensions' => self::FILE_EXTENSIONS,
                'when' => function () {
                    return $this->isRequiredImage();
                },
                'whenClient' => $whenClientForImgFile,
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'displayItem' => Yii::t('app', '表示'),
            'layout' => Yii::t('app', '配置'),
            'imgFile' => Yii::t('app', '画像'),
        ]);
    }

    /**
     * typeとtexを入力値に従って代入するようオーバーライド
     * @inheritdoc
     */
    public function load($data, $formName = null)
    {
        if (!parent::load($data, $formName)) {
            return false;
        }
        $this->type = $this->typeFromInput();
        if (!$this->isRequiredText()) {
            $this->text = '';
        }
        return true;
    }

    /**
     * sortとpostを元にloadしてsave用のモデルを作る
     * @param $sort
     * @param $data
     * @param null $formName
     * @return bool
     */
    public function loadAll($sort, $data, $formName = null)
    {
        if (!$this->load($data, $formName)) {
            return false;
        }
        // sortをセット
        $this->sort = $sort;
        // 画像情報読み込み
        $this->loadFileInfo();
        return true;
    }

    /**
     * 表示別の推奨画像サイズを返す
     * @return int
     */
    public function recommendedWidth():int
    {
        switch ($this->displayItem) {
            case self::DISPLAY_IMG:
                return self::WIDTH_ONLY_IMG;
                break;
            case self::DISPLAY_BOTH:
                return self::WIDTH_BOTH;
                break;
            default:
                return 0;
                break;
        }
    }

    /**
     * 現時点で存在するファイル名の一覧を取得し、
     * 静的変数にキャッシュする
     */
    public static function cacheExistingFileNames()
    {
        static::$_existingFileNames = FreeContentElement::find()->imageFileNames();
    }

    /**
     * キャッシュしたファイル名の配列を返す。
     * キャッシュするタイミングが重要なので、
     * 明示的にキャッシュしないと例外を返すようにしている。
     * @return array
     * @throws \Exception
     */
    private static function existingFileNames():array
    {
        if (static::$_existingFileNames === null) {
            throw new \Exception('please cache names of existing files expressly.');
        }
        return static::$_existingFileNames;
    }

    /**
     * 存在するファイル名が入っている場合trueを返す。
     * 入力無しと存在しないファイル名が入っている場合はfalse
     * @return bool
     */
    private function isExistingFileName():bool
    {
        return in_array($this->image_file_name, static::existingFileNames());
    }

    /**
     * 存在しないファイル名が入っている場合trueを返す。
     * 入力無しと存在するファイル名が入っている場合はfalse
     * @return bool
     */
    private function isNotExistingFileName():bool
    {
        return !JmUtils::isEmpty($this->image_file_name) && !$this->isExistingFileName();
    }

    /**
     * @return bool
     */
    private function isRequiredText():bool
    {
        return $this->displayItem != self::DISPLAY_IMG;
    }

    /**
     * @return bool
     */
    private function isRequiredImage():bool
    {
        return $this->displayItem != self::DISPLAY_TEXT;
    }

    /**
     * formで実際に画像がpostされるattribute名を返す
     * @return string
     */
    private function imgFormAttribute():string
    {
        $i = $this->sort - 1;
        return "[{$i}]imgFile";
    }

    // 選択肢の設定メソッド群 //////////////////////////////////////////////////////////////////////////////////////////

    /**
     * 表示の選択肢のラベルを返す
     * @return array
     */
    public static function displayItemLabels():array
    {
        return [
            self::DISPLAY_TEXT => Yii::t('app', 'テキスト'),
            self::DISPLAY_IMG => Yii::t('app', '画像'),
            self::DISPLAY_BOTH => Yii::t('app', '画像とテキスト'),
        ];
    }

    /**
     * 配置の選択肢のラベルを返す
     * @return array
     */
    public static function layoutLabels():array
    {
        return [
            self::IMG_IS_LEFT => Yii::t('app', '画像が左'),
            self::TEXT_IS_LEFT => Yii::t('app', 'テキストが左'),
        ];
    }

    // viewの表示補助メソッド //////////////////////////////////////////////////////////////////////////////////////////

    /**
     * FileInputに渡すプレビュー表示用option
     * @return array
     */
    public function pluginOptionsForInit():array
    {
        if (!$this->isNewRecord && $this->isRequiredImage()) {
            return [
                'initialPreview' => [$this->srcUrl()],
                'initialPreviewAsData' => true,
            ];
        }
        return [];
    }

    // UploadTraitと重複してしまっているメソッド ///////////////////////////////////////////////////////////////////////

    /**
     * propertyや画像のpost状況を元にimageをセットする
     * 正しいsortを代入してからこのメソッドを使わないと画像順がズレるので注意
     */
    public function loadFileInfo()
    {
        // 画像不要なタイプなら画像は削除
        if (!$this->isRequiredImage()) {
            $this->image_file_name = '';
            return;
        }

        // 画像が必要なタイプで画像があれば新規画像名を作成
        if ($this->imgFile = UploadedFile::getInstance($this, $this->imgFormAttribute())) {
            $this->image_file_name = $this->getRandomFileName($this->imgFile->extension);
            return;
        }

        // 画像が無くて更新なら旧ファイル名を保持
        if (!$this->isNewRecord) {
            $this->image_file_name = $this->getOldAttribute('image_file_name');
        }
    }

    /**
     * ファイルをアップロードする
     * @return bool
     */
    public function saveFile():bool
    {
        // 画像アップロードの必要が無い時はtrue
        if (!$this->isRequiredImage()) {
            return true;
        }
        // ファイルがある時はアプロード結果を返す
        if ($file = UploadedFile::getInstance($this, $this->imgFormAttribute())) {
            /** @var Filesystem $fileSystem */
            $fileSystem = Yii::$app->publicFs;

            $stream = fopen($file->tempName, 'r+');
            $result = $fileSystem->writeStream(static::dirPath() . '/' . $this->image_file_name, $stream);
            @fclose($stream);
            return $result;
        }
        // 画像が必要なタイプでファイルが無い時、image_file_nameがあるならtrue,ないならfalseを返す
        return JmUtils::isEmpty($this->image_file_name) ? false : true;
    }

    /**
     * fileのurl（ドメインからの相対）を返す
     * preview用にbase64Imgを返せるように拡張
     * @return string
     */
    public function srcUrl():string
    {
        if (!JmUtils::isEmpty($this->base64Img)) {
            return $this->base64Img;
        }

        return parent::srcUrl();
    }

    /**
     * 日付とランダムな文字列を連結したファイル名を返す
     * @param string $extension ファイルの拡張子
     * @return string
     */
    protected function getRandomFileName($extension = ''):string
    {
        $datetime = new \DateTime('NOW');
        return $datetime->format('Y-m-d') . '_' . md5(uniqid()) . '.' . $extension;
    }

    /**
     * オブジェクトストレージのディレクトリパスを返す
     * @return string
     */
    protected static function dirPath():string
    {
        /** @var Tenant $tenant */
        $tenant = Yii::$app->tenant;
        return $tenant->tenantCode . '/' . self::DIR_PATH;
    }

    // getter setterと関連メソッド /////////////////////////////////////////////////////////////////////////////////////

    /**
     * displayItemのgetter
     * @return int|null
     */
    public function getDisplayItem()
    {
        if (!$this->_displayItem && $this->type) {
            $map = [
                self::TYPE_ONLY_TEXT => self::DISPLAY_TEXT,
                self::TYPE_ONLY_IMG => self::DISPLAY_IMG,
                self::TYPE_LEFT_TEXT => self::DISPLAY_BOTH,
                self::TYPE_LEFT_IMG => self::DISPLAY_BOTH,
            ];
            $this->_displayItem = $map[$this->type] ?? null;
        }
        return $this->_displayItem;
    }

    /**
     * displayItemのsetter
     * @param $v
     */
    public function setDisplayItem($v)
    {
        $this->_displayItem = $v;
    }

    /**
     * layoutのgetter
     * @return int|null
     */
    public function getLayout()
    {
        if (!$this->_layout && $this->type) {
            $map = [
                self::TYPE_ONLY_TEXT => null,
                self::TYPE_ONLY_IMG => null,
                self::TYPE_LEFT_TEXT => self::TEXT_IS_LEFT,
                self::TYPE_LEFT_IMG => self::IMG_IS_LEFT,
            ];
            $this->_layout = $map[$this->type] ?? null;
        }
        return $this->_layout;
    }

    /**
     * layoutのsetter
     * @param $v
     */
    public function setLayout($v)
    {
        $this->_layout = $v;
    }

    /**
     * displayItemとlayoutを元にtypeを返す
     * @return int|null
     */
    private function typeFromInput()
    {
        switch ($this->displayItem) {
            case self::DISPLAY_TEXT:
                return self::TYPE_ONLY_TEXT;
                break;
            case self::DISPLAY_IMG:
                return self::TYPE_ONLY_IMG;
                break;
            case self::DISPLAY_BOTH:
                if ($this->layout == self::IMG_IS_LEFT) {
                    return self::TYPE_LEFT_IMG;
                } elseif ($this->layout == self::TEXT_IS_LEFT) {
                    return self::TYPE_LEFT_TEXT;
                }
                break;
            default:
                break;
        }
        return null;
    }

    // まとめてloadやsaveをする静的メソッド群 //////////////////////////////////////////////////////////////////////////

    /**
     * postデータを元にsortでindexされたmodelの配列を返す
     * delete insertするため、全て新規のモデルとして返すことに注意
     * @param array $data
     * @return static[]
     */
    public static function loadMultipleAndIndex($data):array
    {
        $sort = 1;
        foreach ($data as $elementData) {
            $model = new FreeContentElementForm();

            if (!$model->loadAll($sort, $elementData, '')) {
                return [];
            }
            // その方が扱いやすいのでsortでインデックスしておく
            $models[$sort] = $model;

            $sort++;
        }
        return $models ?? [];
    }

    /**
     * $freeContentIdに紐づいた$modelsをdelete&insertする
     * @param static[] $models
     * @param FreeContentForm $freeContent
     * @return bool
     */
    public static function saveMultiple(array $models, FreeContentForm $freeContent):bool
    {
        // delete&insert前の使用ファイル名をキャッシュ
        static::cacheExistingFileNames();
        $freeContent->unlinkAll('elements', true);
        foreach ($models as $sort => $element) {
            // validateしたいのでlinkを使わずにinsertする
            $element->free_content_id = $freeContent->id;
            // レコードをsaveして、画像実体もアップロードする
            if (!$element->save() || !$element->saveFile()) {
                return false;
            }
        }
        return true;
    }

    /**
     * 渡されたモデル群と現在登録されているレコードの差分の画像ファイルを削除する
     * @param static[] $elements
     */
    public static function deleteUnusedFiles(array $elements)
    {
        static::deleteUnusedFilesByName(ArrayHelper::getColumn($elements, 'image_file_name'));
    }

    /**
     * 渡されたファイル名のファイルのうち現在登録されていない画像ファイルを削除する
     * @param array $fileNames
     */
    public static function deleteUnusedFilesByName($fileNames)
    {
        // 登録前にキャッシュした内容を更新する
        static::cacheExistingFileNames();
        $deleteFiles = array_diff($fileNames, static::existingFileNames());

        /** @var Filesystem $fileSystem */
        $fileSystem = Yii::$app->publicFs;

        foreach ($deleteFiles as $deleteFile) {
            $filePath = static::dirPath() . '/' . $deleteFile;
            if ($deleteFile && $fileSystem->has($filePath)) {
                $fileSystem->delete($filePath);
            }
        }
    }
}
