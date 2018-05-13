<?php

namespace app\models\manage;

use app\common\traits\UploadTrait;
use app\models\bases\BaseMediaUpload;
use creocoder\flysystem\Filesystem;
use yii;
use yii\web\UploadedFile;
use app\modules\manage\models\Manager;

/**
 * This is the model class for table "media_upload".
 *
 * @property bool $isJobPicRegister
 * @property string $fileSaveError
 * @property Filesystem $fileSystem
 */
class MediaUpload extends BaseMediaUpload
{
    use UploadTrait;

    /** @var UploadedFile */
    public $imageFile;

    /** @var string */
    public $adminName;

    /** @var string */
    public $fileSaveError = '';

    /**
     * file uploadで使うpropertyを初期化
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        $this->uploadInit(self::DIR_PATH, true);
        $this->fileAttributes = [
            'file' => 'imageFile',
            'name' => 'save_file_name',
        ];
    }

    /**
     * admin_master_idとclient_master_idに関してはシナリオから外して
     * postからのloadを許可せず、権限や元のDBの値を元に代入する
     * @inheritdoc
     */
    public function rules()
    {
        // 掲載企業用画像の場合は別のclientの画像は許容する
        if ($this->client_master_id !== null) {
            $filter = [
                'or',
                ['client_master_id' => null],
                ['client_master_id' => $this->client_master_id],
            ];
        }

        $rules = [
            [
                'imageFile',
                'file',
                'maxSize' => self::MAX_SIZE,
                'extensions' => implode(', ', self::FILE_EXTENSIONS),
            ],
            ['disp_file_name', 'string', 'max' => 200],
            [
                'disp_file_name',
                'unique',
                'filter' => $filter ?? [],
                'message' => Yii::t('app', '同じファイル名が保存されています。'),
            ],
            ['tag', 'string', 'max' => 50],
            [
                'tag',
                'compare',
                'compareValue' => '0',
                'operator' => '!==',
                'type' => 'string',
                'message' => Yii::t('app', '"{compareValueOrAttribute}"という{attribute}を設定することはできません'),
            ],
        ];

        if ($this->isNewRecord) {
            // 新規作成の場合はファイルが必須
            $rules = array_merge($rules, [
                ['imageFile', 'required'],
            ]);
        }

        return $rules;
    }

    /**
     * traitのmethodをオーバーライド
     * postされたファイルを元にattributeに値をloadする
     * ファイルが存在すればアップロード者IDを上書きする
     * タグのみの更新の時はアップロード者IDは更新しない
     * 新規作成の時のみ、ファイル名とアップロード者に応じた掲載企業IDを代入する
     * @return bool
     */
    public function loadFileInfo():bool
    {
        if ($file = UploadedFile::getInstance($this, 'imageFile')) {
            // ファイルがあればimageFileにtrueを代入して必須チェック回避
            $this->imageFile = true;
            // ファイルがあればアップロード者IDとファイルサイズと各ファイル名を更新
            $this->admin_master_id = Yii::$app->user->id;
            $this->file_size = $file->size;
            $this->save_file_name = $this->getRandomFileName($file->extension);
            $this->disp_file_name = $file->name;
            if ($this->isNewRecord) {
                // さらに新規登録なら適切な掲載企業IDを代入
                return $this->loadAuthParam();
            }
        }
        return true;
    }

    /**
     * 運営元と代理店の場合は、掲載企業IDをそのままスルーする。
     * 求人原稿登録画面からのアップロードの場合はその画面で選択されている掲載企業が入り、
     * ギャラリーの更新の場合は（post操作しない限りは）元々の値が入り、
     * ギャラリーの新規登録の場合は（post操作しない限りは）nullが入ることになる。
     * 掲載企業の場合は自分のIDで上書きする。
     * todo 保守：運営元と代理店のpost操作に関しては検討の後、validateClientIdで対策すること
     * @return bool
     */
    private function loadAuthParam()
    {
        /** @var Manager $identity */
        $identity = Yii::$app->user->identity;
        switch ($identity->myRole) {
            case Manager::OWNER_ADMIN:
            case Manager::CORP_ADMIN:
                return true;
                break;
            case Manager::CLIENT_ADMIN:
                $this->client_master_id = $identity->client_master_id;
                return true;
                break;
            default :
                return false;
                break;
        }
    }

    /**
     * 全てのエラーメッセージを1次元配列で返す
     * @return array
     */
    public function errorMessages(): array
    {
        $errorMessages = [];
        foreach ($this->errors as $error) {
            $errorMessages = array_merge($errorMessages, $error);
        }
        return $errorMessages;
    }
}
