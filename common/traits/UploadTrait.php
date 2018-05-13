<?php
/**
 * Created by PhpStorm.
 * User: proseeds
 * Date: 2017/12/25
 * Time: 9:29
 */
namespace app\common\traits;

use app\common\Helper\JmUtils;
use Aws\CloudFront\Exception\Exception;
use proseeds\base\Tenant;
use Yii;
use yii\db\ActiveRecord;
use yii\helpers\Url;
use yii\web\UploadedFile;

/**
 * Class UploadTrait
 * ActiveRecordを継承したクラスでuseすること
 * 1モデル1画像である前提の設計なので、
 * 複数を扱う必要がある場合は調整するなり拡張するなり別traitにするなりすること
 * @package app\common\traits
 */
trait UploadTrait
{
    use \proseeds\models\traits\UploadTrait;

    /** @var string ファイルの親ディレクトリのtenantCode以下のパス */
    public $addPath;
    /**
     * 登録済みファイルの削除フラグ
     * このフラグが立っていると新規ファイルの有無にかかわらず
     * load時にはファイル名がクリアされ、
     * save時にはファイル実体が削除される
     * @var string
     */
    public $deleteFileFlg = false;
    /**
     * ファイルがpostされるattributeと
     * ファイル名が入るattributeを
     * ['file' => ***, 'name' => ###]
     * の形式で設定する
     * @var array
     */
    protected $fileAttributes;
    /** @var string 古いファイル名を保持しておく */
    protected $oldFileName;

    /**
     * アップローダーメソッドの初期化（init()で、呼び出し必須）
     * JM用にオーバーライド
     * @param string $addPath 追加するパス(機能名)
     * @param bool $isPublic pubicな領域(/web/uploads/)にアップロードするか、否か(/uploads/)
     */
    public function uploadInit($addPath = '', $isPublic = false)
    {
        /** @var Tenant $tenant */
        $tenant = Yii::$app->tenant ?? null;
        if ($tenant) {
            $this->isPublic = $isPublic;
            $this->tempFilePath = "{$tenant->tenantCode}/tmp/{$addPath}";
            $this->filePath = "{$tenant->tenantCode}/{$addPath}";
            $this->addPath = $addPath;
        }
    }

    /**
     * モデルに設定されたfileAttributesを元にファイルがあれば
     * ファイルをアップロードして旧ファイルを削除する。
     * 無ければfalseを返す。
     * @return bool|object
     */
    public function saveFiles()
    {
        if ($this->validateFileAttributes()) {
            $result = $this->uploadFile($this->fileAttributes['file'], $this->filePath, $this->{$this->fileAttributes['name']});
            if ($result) {
                $this->deleteOldFile();
            }
            return $result;
        }
        throw new Exception('fileAttribute property is invalid');
    }

    /**
     * モデルに紐づいたファイルを削除する
     * @return bool
     */
    public function deleteFile():bool
    {
        if ($this->validateFileAttributes()) {
            return $this->fileSystem->delete($this->filePath . '/' . $this->{$this->fileAttributes['name']});
        }
        throw new Exception('fileAttribute property is invalid');
    }

    /**
     * モデルに紐づいたファイルにアクセスできるurlを返す
     * 主にsrc属性にセットされる
     * @return string
     */
    public function srcUrl():string
    {
        if ($this->validateFileAttributes()) {
            return Url::to([JmUtils::fileUrl($this->addPath . '/' . $this->{$this->fileAttributes['name']}), 'public' => $this->isPublic]);
        }
        throw new Exception('fileAttribute property is invalid');
    }

    /**
     * 古い画像ファイルがあればそれを削除し、
     * ファイル名のキャッシュも削除する
     * @return bool
     */
    public function deleteOldFile():bool
    {
        if ($this->oldFileName && $this->fileSystem->has($this->filePath . '/' . $this->oldFileName)) {
            $result = $this->fileSystem->delete($this->filePath . '/' . $this->oldFileName);
            $this->oldFileName = null;
            return $result;
        }
        return false;
    }

    /**
     * 旧ファイル名をpropertyにキャッシュしている
     * afterFindはActiveRecordのメソッド
     */
    public function afterFind()
    {
        /** @noinspection PhpUndefinedMethodInspection */
        /** @noinspection PhpUndefinedClassInspection */
        parent::afterFind();
        $this->cacheOldFileName();
    }

    /**
     * 画像情報も一緒に読み込む
     * loadはActiveRecordのメソッド
     * @param $data
     * @param null $formName
     * @return bool
     */
    public function load($data, $formName = null)
    {
        /** @noinspection PhpUndefinedMethodInspection */
        /** @noinspection PhpUndefinedClassInspection */
        return parent::load($data, $formName) && $this->loadFileInfo();
    }

    /**
     * postされたファイルを元にattributeに値をloadする
     * ファイルが存在すればアップロード者IDを上書きする
     * タグのみの更新の時はアップロード者IDは更新しない
     * 新規作成の時のみ、ファイル名とアップロード者に応じた掲載企業IDを代入する
     * ActiveRecord::loadに合わせる意味と、
     * 本メソッドをオーバーライドしたメソッドでfalseを返すケースがあるので
     * 戻り値を設定しているが、今後の実装次第では要件等
     * @return bool
     */
    public function loadFileInfo():bool
    {
        /** @var ActiveRecord $this */
        if ($file = UploadedFile::getInstance($this, $this->fileAttributes['file'])) {
            // ファイルがあればファイル名のattributeにファイル名を代入
            $this->{$this->fileAttributes['name']} = $this->getRandomFileName($file->extension);
        } elseif ($this->deleteFileFlg) {
            // 画像削除フラグがあればファイル名をクリア
            $this->{$this->fileAttributes['name']} = '';
        } else {
            // その他の場合は旧ファイル名を保持(ファイルが無い新規作成時もこの処理で問題ない)
            $this->{$this->fileAttributes['name']} = $this->getOldAttribute($this->fileAttributes['name']);
        }
        return true;
    }

    /**
     * save_file_nameをpropertyにキャッシュする
     * 主にafterFindで使う
     */
    protected function cacheOldFileName()
    {
        if (!$this->validateFileAttributes()) {
            throw new Exception('fileAttribute property is invalid');
        }
        $this->oldFileName = $this->{$this->fileAttributes['name']};
    }

    /**
     * fileAttributesが正常かを確認する
     * @return boolean
     */
    protected function validateFileAttributes():bool
    {
        return isset($this->fileAttributes['file']) && isset($this->fileAttributes['name']);
    }
}
