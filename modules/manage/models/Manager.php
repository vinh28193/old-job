<?php
/**
 * Created by IntelliJ IDEA.
 * User: ueda
 * Date: 15/10/06
 * Time: 21:13
 */

namespace app\modules\manage\models;


use app\common\Helper\JmUtils;
use app\models\manage\AdminMaster;
use app\models\manage\ClientMaster;
use app\models\manage\CorpMaster;
use Yii;
use yii\base\NotSupportedException;
use yii\web\IdentityInterface;

/**
 * Class Manager
 * @package app\modules\manage\models
 *
 * @property string $username
 * @property string $myRole
 * @property MenuCategory[] $myMenu
 */
class Manager extends AdminMaster implements IdentityInterface
{
    const VALID_FLAG = 1;

    const OWNER_ADMIN = 'owner_admin';
    const CORP_ADMIN = 'corp_admin';
    const CLIENT_ADMIN = 'client_admin';

    /**
     * @var string $_roleName roleの名前
     */
    private $_roleName;

    /**
     * @var array $_cachedMyMenu getMyMenuのキャッシュ
     */
    private $_cachedMyMenu = [];

    /**
     * @inheritdoc
     */
    public static function findIdentity($id)
    {
        return static::findOne([
            'id' => $id,
        ]);
    }

    /**
     * @inheritdoc
     */
    public function getAuthKey()
    {
        // とりあえず適当な文字列
        return 'jm2';
    }

    /**
     * Generates "remember me" authentication key
     */
    public function generateAuthKey()
    {
        // とりあえず何もしない
//        throw new NotSupportedException('"generateAuthKey" is not implemented.');
    }

    /**
     * @inheritdoc
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        throw new NotSupportedException('"findIdentityByAccessToken" is not implemented.');
    }

    /**
     * アクセストークンを新規に作成する
     * @return string
     */
    public static function generateAccessToken()
    {
        return Yii::$app->security->generateRandomString(64);
    }

    /**
     * @param $password
     * @return bool
     */
    public function validatePassword($password)
    {
        return $password === $this->password;
//        return Yii::$app->security->validatePassword($password, $this->password_hash);
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * ログインIDから管理者情報を取得する
     * @param $loginId
     * @return null|Manager
     */
    public static function findByLoginId($loginId)
    {
        $manager = self::findOne([
            'login_id' => $loginId,
            'valid_chk' => self::VALID_FLAG,
        ]);

        if (!$manager) {
            return null;
        }

        $myRole = $manager->myRole;

        if (
            $myRole == self::CLIENT_ADMIN &&
            (
                !CorpMaster::find()->where([
                    'id' => $manager->corp_master_id,
                ])->count() ||
                !ClientMaster::find()->where([
                    'id' => $manager->client_master_id,
                ])->count()
            )
        ) {
            return null;
        }

        if (
            $myRole == self::CORP_ADMIN &&
            !CorpMaster::find()->where([
                'id' => $manager->corp_master_id,
            ])->count()
        ) {
            return null;
        }

        return $manager;
    }

    /**
     * @inheritdoc
     */
    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    /**
     * フルネームを返す
     * @return string
     */
    public function getUsername()
    {
        return $this->name_sei . ' ' . $this->name_mei;
    }

    /**
     * 所属 Role を判定し、返す
     * @return string
     */
    public function getMyRole()
    {
        if(!$this->_roleName) {
            $this->_roleName = ManageAuth::getRoleNameByUserId($this->id);
        }
        return $this->_roleName;

        /*if (Yii::$app->user->can('client')) {
            // corp_id client_id 両方がSETされていれば掲載企業管理者
            return self::CLIENT_ADMIN;
        }

        if (Yii::$app->user->can('corp')) {
            // corp_id のみSETされていれば代理店管理者
            return self::CORP_ADMIN;
        }

        // 両方無ければ運営元管理者
        return self::OWNER_ADMIN;*/
    }

    /**
     * 有効な Role を配列で返す
     * @return array
     */
    public static function getRoles()
    {
        return [
            self::OWNER_ADMIN,
            self::CORP_ADMIN,
            self::CLIENT_ADMIN,
        ];
    }

    /**
     * 表示可能な Menu を取得する
     * @return \app\modules\manage\models\MenuCategory[]
     */
    public function getMyMenu()
    {
        if (empty($this->_cachedMyMenu)) {
            $this->_cachedMyMenu = MenuCategory::getMyMenu($this->id);
        }
        return $this->_cachedMyMenu;
    }

    /**
     * 掲載企業管理者であるかどうかを返す
     * @return bool
     */
    public function isClient():bool
    {
        return !JmUtils::isEmpty($this->client_master_id) && $this->myRole == self::CLIENT_ADMIN;
    }

    /**
     * 代理店管理者であるかどうかを返す
     * @return bool
     */
    public function isCorp():bool
    {
        return JmUtils::isEmpty($this->client_master_id) && !JmUtils::isEmpty($this->corp_master_id) && $this->myRole == self::CORP_ADMIN;
    }

    /**
     * 運営元管理者であるかどうかを返す
     * @return bool
     */
    public function isOwner():bool
    {
        return JmUtils::isEmpty($this->client_master_id) && JmUtils::isEmpty($this->corp_master_id) && $this->myRole == self::OWNER_ADMIN;
    }
}
