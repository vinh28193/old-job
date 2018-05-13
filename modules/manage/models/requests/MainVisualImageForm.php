<?php

namespace app\modules\manage\models\requests;

use app\common\Helper\JmUtils;
use app\models\manage\MainVisualImage;
use app\models\manage\MainVisual;
use app\models\manage\WidgetData;
use app\modules\manage\models\Manager;
use proseeds\base\Tenant;
use proseeds\models\traits\UploadTrait;
use Yii;
use yii\helpers\Url;
use yii\web\BadRequestHttpException;
use yii\web\UploadedFile;

/**
 * Class MainVisualImageForm
 * @package app\modules\manage\models\requests
 *
 * @property MainVisual $mainVisual
 * @property MainVisualImage $mainVisualImage
 */
class MainVisualImageForm extends CommonForm
{
    use UploadTrait;

    // 画像の最大縦横サイズ
    const MAX_SIZE = 1600;

    /**
     * @var UploadedFile
     */
    public $file;
    /**
     * todo 命名が規約違反なのでrename
     * @var UploadedFile
     */
    public $file_sp;

    /**
     * @var int ID
     */
    public $id;
    /**
     * @var string 画像
     */
    public $image;
    /**
     * @var string URL
     */
    public $url;
    /**
     * todo 命名が規約違反なのでrename。その際、rulesやattributeLabelsにも影響があるので注意
     * @var string URL
     */
    public $url_sp;
    /**
     * @var integer ALT設定コンテンツ
     */
    public $content;
    /**
     * @var string 順番
     */
    public $sort;
    /**
     * todo 命名が規約違反なのでrename。その際、rulesやattributeLabelsにも影響があるので注意
     * @var integer 有効フラグ
     */
    public $valid_chk;
    /**
     * @var string メモ
     */
    public $memo;

    /**
     * @var MainVisual
     */
    private $_mainVisual;
    /**
     * @var MainVisualImage
     */
    private $_mainVisualImage;
    /**
     * @var int
     */
    private $_formNameKey;

    /**
     * @var Manager
     */
    private $_editor;

    /**
     * MainVisualImageForm constructor.
     * @param MainVisual $mainVisual
     * @param MainVisualImage|null $mainVisualImage
     * @param string|null $key
     * @param array $config
     */
    public function __construct(
        MainVisual $mainVisual,
        MainVisualImage $mainVisualImage,
        string $key = null,
        array $config = []
    ) {
        parent::__construct($config);
        $this->_mainVisual = $mainVisual;
        $this->_mainVisualImage = $mainVisualImage;
        $this->_formNameKey = $key;

        $this->id = $this->_mainVisualImage->id;
        $this->url = $this->_mainVisualImage->url;
        $this->url_sp = $this->_mainVisualImage->url_sp;
        $this->content = $this->_mainVisualImage->content;
        $this->sort = $this->_mainVisualImage->sort;
        $this->valid_chk = $this->_mainVisualImage->valid_chk;
        $this->memo = $this->_mainVisualImage->memo;

        $this->_editor = Yii::$app->user->identity;

        $this->isPublic = true;
    }

    /**
     * @return string
     */
    public function formName()
    {
        return parent::formName() . ($this->_formNameKey ?? '');
    }

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        $rules = $this->_mainVisualImage->rules();

        if ($this->_mainVisualImage->isNewRecord) {
            // 新規の際は必須を変更する
            foreach ($rules as $i => $rule) {
                if ($rule[1] == 'required') {
                    $rules[$i][1] = 'safe';
                }
            }
        }

        return array_merge($rules, [
            [
                ['image'],
                'image',
                'skipOnEmpty' => true,
                'extensions' => 'gif, png, jpg',
                'maxFiles' => 1,
                'maxWidth' => self::MAX_SIZE,
                'maxHeight' => self::MAX_SIZE,
            ],
        ]);
    }

    /**
     * @return array
     */
    public function attributeLabels()
    {
        return array_merge($this->_mainVisualImage->attributeLabels(), [
            'file' => Yii::t('app', 'PC画像'),
            'file_sp' => Yii::t('app', 'スマホ画像'),
        ]);
    }

    /**
     * 更新を反映
     * @return bool
     * @throws BadRequestHttpException
     */
    public function save()
    {
        $originFileName = $this->_mainVisualImage->file_name ?? null;
        $originFileSpName = $this->_mainVisualImage->file_name_sp ?? null;

        try {
            if ($this->_mainVisualImage->isNewRecord) {
                if (!$this->file && !$this->file_sp) {
                    $this->addErrors([
                        'file' => Yii::t('app', 'メインビジュアル設定に画像は必須です。'),
                        'file_sp' => Yii::t('app', 'メインビジュアル設定に画像は必須です。'),
                    ]);
                    throw new BadRequestHttpException(Yii::t('app', 'メインビジュアル画像の保存に失敗しました'));
                }
            }

            // 画像の保存
            if ($this->file) {
                $this->_mainVisualImage->file_name = $this->getRandomFileName($this->file->extension);
            }
            if ($this->file_sp) {
                $this->_mainVisualImage->file_name_sp = $this->getRandomFileName($this->file_sp->extension);
            }

            $changeFile = $originFileName != $this->_mainVisualImage->file_name;
            $changeSpFile = $originFileSpName != $this->_mainVisualImage->file_name_sp;
            $this->_mainVisualImage->tenant_id = $this->_editor->tenant_id;
            $this->_mainVisualImage->main_visual_id = $this->_mainVisual->id;
            $this->_mainVisualImage->url = $this->url;
            $this->_mainVisualImage->url_sp = $this->url_sp;
            $this->_mainVisualImage->content = $this->content;
            $this->_mainVisualImage->sort = $this->sort;
            $this->_mainVisualImage->valid_chk = $this->valid_chk;

            if (!$this->_mainVisualImage->save()) {
                $this->addErrors($this->_mainVisualImage->errors);
                throw new BadRequestHttpException(Yii::t('app', 'メインビジュアル画像情報の保存に失敗しました'));
            }

            // 画像が変更されていれば転送
            /** @var Tenant $tenant */
            $tenant = Yii::$app->tenant;
            $directoryPath = $tenant->tenantCode . '/' . WidgetData::DIR_PATH;
            if ($changeFile) {
                $this->uploadFile('file', $directoryPath, $this->_mainVisualImage->file_name);
                $originPath = $directoryPath . '/' . $originFileName;
                if ($originFileName && $this->fileSystem->has($originPath)) {
                    $this->fileSystem->delete($originPath);
                }
            }
            if ($changeSpFile) {
                $this->uploadFile('file_sp', $directoryPath, $this->_mainVisualImage->file_name_sp);
                $originSpPath = $directoryPath . '/' . $originFileSpName;
                if ($originFileSpName && $this->fileSystem->has($originSpPath)) {
                    $this->fileSystem->delete($originSpPath);
                }
            }
            return true;
        } catch (BadRequestHttpException $e) {
            Yii::error($this->errors);
            throw new BadRequestHttpException($e->getMessage());
        }
    }

    /**
     * @return MainVisual
     */
    public function getMainVisual(): MainVisual
    {
        return $this->_mainVisual;
    }

    /**
     * @return MainVisualImage
     */
    public function getMainVisualImage(): MainVisualImage
    {
        return $this->_mainVisualImage;
    }

    /**
     * モデルに紐づいたファイルにアクセスできるurlを返す
     * 主にsrc属性にセットされる
     * @return string
     */
    public function srcUrl():string
    {
        return Url::to([JmUtils::fileUrl($this->mainVisualImage->filePath), 'public' => $this->isPublic]);
    }

    /**
     * モデルに紐づいたファイルにアクセスできるurlを返す
     * 主にsrc属性にセットされる
     * @return string
     */
    public function srcSpUrl():string
    {
        return Url::to([JmUtils::fileUrl($this->mainVisualImage->filePathSp), 'public' => $this->isPublic]);
    }
}
