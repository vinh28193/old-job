<?php

namespace app\models\manage;

use app\modules\manage\models\ManageAuth;
use yii\helpers\ArrayHelper;
use app\modules\manage\models\Manager;
use proseeds\models\BaseModel;
use Yii;
use app\models\queries\AdminQuery;

/**
 * This is the model class for table "admin_master".
 *
 * @property integer $id
 * @property integer $tenant_id
 * @property integer $admin_no
 * @property integer $corp_master_id
 * @property string $login_id
 * @property string $password
 * @property string $created_at
 * @property integer $valid_chk
 * @property string $name_sei
 * @property string $name_mei
 * @property string $tel_no
 * @property integer $client_master_id
 * @property string $mail_address
 * @property bool $sendPass
 * @property string $role
 * @property array $exceptions
 * @property integer $job_input_type
 *
 * @property string $fullName
 * @property ClientMaster $clientMaster
 * @property CorpMaster $corpMaster
 * @property ClientMaster $clientModel
 * @property CorpMaster $corpModel
 */
class AdminMaster extends BaseModel
{
    /** 状態 - 有効or無効 */
    const VALID_FLAG = 1;
    const INVALID = 0;
    /** ログインID最低値 */
    const MIN_LOGIN_ID = 4;
    /** パスワード最低値 */
    const MIN_PASSWORD = 4;
    /** 権限別書き込みシナリオ */
    const REGISTER_OWNER = 'owner';
    const REGISTER_CORP = 'corp';

    const INPUT_TYPE_CLASSIC = 1;
    const INPUT_TYPE_PREVIEW = 0;

    /** @var string 管理者種別 */
    private $_role;
    /** @var array 除外権限 */
    private $_exceptions;
    /** @var int パスワード送信確認 */
    public $sendPass;

    /**
     * テーブル名
     * @return string テーブル名
     */
    public static function tableName()
    {
        return 'admin_master';
    }

    public function beforeValidate()
    {
        if (parent::beforeValidate()) {
            switch ($this->role) {
                case Manager::OWNER_ADMIN:
                    $this->scenario = self::REGISTER_OWNER;
                    break;
                case Manager::CORP_ADMIN:
                    $this->scenario = self::REGISTER_CORP;
                    break;
                default:
                    break;
            }
            return true;
        }
        return false;
    }

    /**
     * 保存前処理
     * 新規登録時、管理者番号をMAX+1して挿入しています。
     * @param boolean $insert INSERT判別
     * @return boolean
     */
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            if ($insert && $this->isNewRecord) {
                //strvalはstringルール対策
                $this->admin_no = self::find()->max('admin_no') + 1;
            }
            return true;
        } else {
            return false;
        }
    }

    /**
     * ルール設定。
     * @return array ルール設定
     */
    public function rules()
    {
        //管理者の検証ルールに加えて、追加設定
        return ArrayHelper::merge([
            [['login_id', 'password'], 'match', 'pattern' => '/^[a-z0-9]*$/i', 'message' => Yii::t('app', '{attribute}は半角英数字にしてください。')],
            [['login_id', 'password'], 'string', 'min' => self::MIN_LOGIN_ID],
            [['login_id', 'mail_address'], 'unique'],
            [['fullName', 'name_sei', 'name_mei', 'login_id', 'password', 'mail_address', 'valid_chk', 'role'], 'required'],
            [['name_sei', 'name_mei'], 'match', 'pattern' => '/^[\S]*$/i', 'message' => Yii::t('app', '{attribute}にスペースは使えません')],
            ['corp_master_id', 'required', 'except' => self::REGISTER_OWNER],
            ['client_master_id', 'required', 'except' => [self::REGISTER_CORP, self::REGISTER_OWNER]],
            [['valid_chk', 'job_input_type'], 'boolean'],
            [['sendPass'], 'safe'],
        ], Yii::$app->functionItemSet->admin->rules);
    }

    /**
     * 要素のラベル名を設定。
     * function_item_setテーブルから名前を取得し、カラムごとに適用しています。
     * @return array ラベル設定
     */
    public function attributeLabels()
    {
        return ArrayHelper::merge(Yii::$app->functionItemSet->admin->attributeLabels, [
            'name_sei' => Yii::t('app', '部署名'),
            'name_mei' => Yii::t('app', '担当者名'),
            'role' => Yii::t('app', '種別'),
            'valid_chk' => Yii::t('app', '状態'),
            'sendPass' => Yii::t('app', '登録通知メールを送信しますか？'),
        ]);
    }

    /**
     * データのロード
     * 読み込み後、種別の整理をしています。
     * todo リファクタ
     * @param array $data 送信データ
     * @param string $formName フォーム名
     * @return bool|void
     */
    public function load($data, $formName = null)
    {
        if (parent::load($data, $formName)) {
            //初回登録時のrequired対策
            $this->admin_no = isset($this->admin_no) ? $this->admin_no : '0';
            $this->setIdsByRole();
            return true;
        }
        return false;
    }

    /**
     * AdminQueryのインスタンスを返す
     * @return AdminQuery
     */
    public static function find():AdminQuery
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return Yii::createObject(AdminQuery::className(), [get_called_class()]);
    }

    /**
     * 関連する除外する管理権限を取得する。
     * @return array 除外する管理権限
     */
    public function getExceptions()
    {
        return ArrayHelper::getColumn(Yii::$app->authManager->getAssignments($this->id), 'roleName');
    }

    /**
     * 除外権限setter
     * @param $value
     */
    public function setExceptions($value)
    {
        $this->_exceptions = $value;
    }

    /**
     * 代理店IDに一致する代理店情報の取得
     * @return \yii\db\ActiveQuery
     */
    public function getCorpMaster()
    {
        return $this->hasOne(CorpMaster::className(), ['id' => 'corp_master_id']);
    }

    public function getClientMaster()
    {
        return $this->hasOne(ClientMaster::className(), ['id' => 'client_master_id']);
    }

    /**
     * 代理店のセッター
     * Viewでの使用にはcorpMasterを使うが実際に登録する値はcorp_master_idとなるので
     * セッターを利用して格納します。
     * @param int $value 代理店ID
     */
    public function setCorpMaster($value)
    {
        $this->corp_master_id = $value;
    }

    /**
     * 掲載企業のセッター
     * Viewでの使用にはclientMasterを使うが実際に登録する値はclient_master_idとなるので
     * セッターを利用して格納します。
     * @param int $value 掲載企業
     */
    public function setClientMaster($value)
    {
        $this->client_master_id = $value;
    }

    /**
     * リレーショナルモデルもしくは新しいモデルインスタンスを返す
     * @return ClientMaster
     */
    public function getClientModel()
    {
        return $this->clientMaster ?: new ClientMaster();
    }

    /**
     * リレーショナルモデルもしくは新しいモデルインスタンスを返す
     * @return CorpMaster
     */
    public function getCorpModel()
    {
        return $this->corpMaster ?: new CorpMaster();
    }

    /**
     * 状態リストを取得する。
     * @return array 状態リスト
     */
    public static function getValidChkList()
    {
        return [
            self::VALID_FLAG => Yii::t('app', '有効'),
            self::INVALID => Yii::t('app', '無効'),
        ];
    }

    /**
     * roleのゲッター
     * @return string role名
     */
    public function getRole()
    {
        if (!isset($this->_role)) {
            $this->_role = $this->id ? ManageAuth::getRoleNameByUserId($this->id) : null;
        }
        return $this->_role;
    }

    /**
     * roleのセッター
     * @param string $value role名
     */
    public function setRole($value)
    {
        $this->_role = $value;
    }

    /**
     * 種別リストを取得する。
     * @return array 種別リスト
     */
    public static function getRoleList()
    {
        return [
            Manager::OWNER_ADMIN => Yii::t('app', '運営元管理者'),
            Manager::CORP_ADMIN => Yii::t('app', '代理店管理者'),
            Manager::CLIENT_ADMIN => Yii::t('app', '掲載企業管理者'),
        ];
    }

    /**
     * 種別による各管理IDのセット
     * 運営元管理者であればcorp_master_id, client_master_idはnullにする。
     * 代理店管理者であればclient_master_idはnullにする。
     */
    private function setIdsByRole()
    {
        if ($this->_role == Manager::OWNER_ADMIN) {
            //運営元管理者
            $this->corp_master_id = null;
            $this->client_master_id = null;
        } elseif ($this->_role == Manager::CORP_ADMIN) {
            //代理店管理者
            $this->client_master_id = null;
        }
    }

    /* -------- Role関係ここまで ---------- */

    /**
     * 状態の名前を取得します。(セットされていない場合null)
     * @return string 状態名
     */
    public function getValidChkName()
    {
        if (!isset($this->attributes['valid_chk'])) {
            return null;
        }
        return $this->attributes['valid_chk'] == 0 ? Yii::t('app', '無効') : Yii::t('app', '有効');
    }

    /**
     * 権限の保存処理
     * @param array $post postデータ
     */
    public function saveAuthExceptions($post)
    {
        $auth = Yii::$app->authManager;

        //権限削除処理
        if (!$this->isNewRecord) {
            $auth->revokeAll($this->id);
        }

        //----------------------
        // roleを保存
        //----------------------
        $authRole = $auth->getRole($this->_role);
        $auth->assign($authRole, $this->id);
        //----------------------
        // permissionを保存
        //----------------------
        $exceptions = $this->_exceptions;
        foreach ((array)$exceptions as $exception) {
            $permission = $auth->getPermission($exception);
            //登録されていない許可がvalueにはめ込まれていた場合の対応
            if (isset($permission)) {
                $auth->assign($permission, $this->id);
            }
        }
    }

    /**
     * admin masterのnameを取得
     * @return string
     */
    public function getFullName()
    {
        return $this->name_sei . ' ' . $this->name_mei;
    }
}
