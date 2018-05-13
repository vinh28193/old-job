<?php

namespace app\modules\manage\models;

use app\models\manage\ManagerSession;
use Yii;
use yii\base\Model;
use yii\db\Query;
use yii\helpers\ArrayHelper;
use yii\rbac\DbManager;
use yii\rbac\Item;

/**
 * Class ManageAuth
 * @package app\modules\manage\models
 *
 * @property bool|Manager $manager
 */
class ManageAuth extends Model
{
    /**
     * ログインID
     * @var string
     */
    public $loginId;
    /**
     * パスワード
     * @var string
     */
    public $password;

    public $rememberMe = true;

    /**
     * @var bool|Manager
     */
    private $manageUser = false;

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [
                ['loginId', 'password'],
                'required',
                'message' => '{attribute} は必須です.'
            ],
            ['password', 'validatePassword'],
            ['rememberMe', 'boolean'],
        ];
    }


    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'loginId' => 'ログインID',
            'password' => 'パスワード',
            'rememberMe' => 'ログイン状態を保存する'
        ];
    }

    /**
     * @return string
     */
    public function formName()
    {
        return '';
    }

    /**
     * パスワードが正しいかを判定する
     * @param $attribute
     */
    public function validatePassword($attribute)
    {
        if (!$this->hasErrors()) {
            $manager = $this->getManager();

            if (!$manager || !$manager->validatePassword($this->password)) {
                $this->addError($attribute, 'ユーザー名またはパスワードが間違っています。');
            }
        }
    }

    /**
     * 管理者ログインする
     *
     * @return Manager|bool
     */
    public function login()
    {
        if (!$this->validate()) {
            return false;
        }

        // 同ユーザーの別のSESSIONを強制ログアウトする
        $manager = $this->getManager();
        ManagerSession::deleteAll([
            'admin_id' => $manager->id,
        ]);

        return Yii::$app->user->login($manager, false ? 3600 * 24 * 30 : 0);
    }

    /**
     * 管理ユーザーをloginIdから取得する
     * @return bool|null|Manager
     */
    public function getManager()
    {
        if ($this->manageUser === false) {
            $this->manageUser = Manager::findByLoginId($this->loginId);
        }

        return $this->manageUser;
    }

    /**
     * userIdを元にroleの名前を単独で取得する
     * @param $userId
     * @return mixed
     * todo メソッドを書く場所本当にここで良いのか？
     */
    public static function getRoleNameByUserId($userId){
        /** @var DbManager $authManager */
        $authManager = Yii::$app->authManager;

        $query = (new Query)->select('b.name')
            ->from(['a' => $authManager->assignmentTable, 'b' => $authManager->itemTable])
            ->where('{{a}}.[[item_name]]={{b}}.[[name]]')
            ->andWhere(['a.user_id' => (string) $userId])
            ->andWhere(['b.type' => Item::TYPE_ROLE])->one($authManager->db);

        return ArrayHelper::getValue($query, 'name');
    }
}