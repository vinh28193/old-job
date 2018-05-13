<?php
/**
 * Created by PhpStorm.
 * User: proseeds
 * Date: 2017/12/19
 * Time: 15:29
 */

namespace app\models\bases;

use app\models\manage\AdminMaster;
use app\models\manage\ClientMaster;
use app\models\queries\MediaUploadQuery;
use proseeds\models\BaseModel;
use Yii;
use yii\db\ActiveQuery;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "media_upload".
 * @property integer $id
 * @property integer $tenant_id
 * @property string $save_file_name
 * @property string $disp_file_name
 * @property integer $admin_master_id
 * @property integer $client_master_id
 * @property integer $created_at
 * @property integer $updated_at
 * @property integer $file_size
 * @property string $tag
 *
 * @property ClientMaster $clientMaster
 * @property AdminMaster $adminMaster
 */
class BaseMediaUpload extends BaseModel
{
    /** @webroot以下の、モデルがファイルを保存する際の相対パス */
    const DIR_PATH = 'data/upload';

    /** 最大ファイルサイズ(512KiB) */
    const MAX_SIZE = 512 * 1024;

    /** 保存する実ファイル名に挿入するランダム文字列の長さ */
    const RANDOM_LENGTH = 10;

    /** 一度にアップロードできる最大のファイル数 */
    const MAX_FILES = 30;

    /** モデルが許可しているファイルの拡張子 */
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
        return 'media_upload';
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'tenant_id' => Yii::t('app', 'テナントID'),
            'save_file_name' => Yii::t('app', '保存用ファイル名'),
            'disp_file_name' => Yii::t('app', 'ファイル名'),
            'file_size' => Yii::t('app', 'ファイルサイズ(Byte)'),
            'admin_master_id' => Yii::t('app', '管理者ID'),
            'client_master_id' => Yii::t('app', '掲載企業ID'),
            'created_at' => Yii::t('app', '登録日時'),
            'updated_at' => Yii::t('app', '更新日時'),
            'imageFile' => Yii::t('app', '画像'),
            'adminName' => Yii::t('app', '作成者'),    //一覧画面の表示用
            'clientName' => Yii::t('app', '掲載企業名'),    //一覧画面の表示用
            'tag' => Yii::t('app', '画像検索用タグ'),
        ];
    }

    /**
     * MediaUploadQueryのインスタンスを返す
     * @return MediaUploadQuery
     */
    public static function find():MediaUploadQuery
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return Yii::createObject(MediaUploadQuery::className(), [get_called_class()]);
    }

    /**
     * リレーションgetter
     * @return ActiveQuery
     */
    public function getAdminMaster():ActiveQuery
    {
        return $this->hasOne(AdminMaster::className(), ['id' => 'admin_master_id']);
    }

    /**
     * リレーションgetter
     * @return ActiveQuery
     */
    public function getClientMaster():ActiveQuery
    {
        return $this->hasOne(ClientMaster::className(), ['id' => 'client_master_id']);
    }

    /**
     * ドロップダウン用のtagの配列を並び替えて初期値を付ける
     * @param array $tags
     * @return array
     */
    protected static function orderTags(array $tags):array
    {
        asort($tags);
        return ArrayHelper::merge([
            '' => Yii::t('app', 'すべて'),
            0 => Yii::t('app', 'タグ無し'),
        ], $tags);
    }
}
