<?php
namespace app\modules\manager;

use app\modules\manage\models\Manager;
use Yii;
use yii\base\Component;
use yii\base\InvalidParamException;

/**
 * manage module の独自認定マネージャー
 */
class AuthManager extends Component
{
    /** @var Manager $user */
    protected $_manager;

    /**
     * ロールのアクセスチェック
     * @param integer $userId
     * @param string $permissionName
     * @param array $params
     * @return boolean
     * @throws \yii\base\InvalidParamException
     */
    public function checkAccess($userId, $permissionName, $params = [])
    {
        if (!$userId) {
            return false;
        }
        if (!in_array($permissionName, Manager::getRoles())) {
            throw new InvalidParamException("Unknown role '{$permissionName}'.");
        }

        if (!isset($this->_manager)) {
            $this->_manager = Yii::$app->user->identity;
            if (empty($this->_manager)) {
                return false;
            }
        }

        $enableRoles = [];
        switch ($permissionName) {
            // 運営元指定
            case Manager::OWNER_ADMIN:
                $enableRoles = [
                    Manager::OWNER_ADMIN,
                ];
                break;
            // 代理店指定
            case Manager::CORP_ADMIN:
                $enableRoles = [
                    Manager::OWNER_ADMIN,
                    Manager::CORP_ADMIN,
                ];
                break;
            // 求人掲載管理者指定
            case Manager::CLIENT_ADMIN:
                $enableRoles = [
                    Manager::OWNER_ADMIN,
                    Manager::CORP_ADMIN,
                    Manager::CLIENT_ADMIN,
                ];
                break;
        }

        return in_array($permissionName, $enableRoles);
    }
}
