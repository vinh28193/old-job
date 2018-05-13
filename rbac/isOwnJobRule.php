<?php
/**
 * Created by PhpStorm.
 * User: proseeds
 */

namespace app\rbac;

use app\models\manage\JobMaster;
use app\modules\manage\models\Manager;
use Yii;
use yii\helpers\ArrayHelper;
use yii\rbac\Rule;

class isOwnJobRule extends Rule
{
    public $name = 'isOwnJob';

    /**
     * Executes the rule.
     *
     * @param string|integer $user the user ID. This should be either an integer or a string representing
     * the unique identifier of a user. See [[\yii\web\User::id]].
     * @param \yii\rbac\Item $item the role or permission that this rule is associated with
     * @param array $params parameters passed to [[ManagerInterface::checkAccess()]].
     * @return boolean a value indicating whether the rule permits the auth item it is associated with.
     */
    public function execute($user, $item, $params)
    {
        /** @var Manager $identity */
        $identity = Yii::$app->user->identity;
        $jobMaster = ArrayHelper::getValue($params, 'jobMaster');
        if (!$jobMaster instanceof JobMaster) {
            return false;
        }

        switch ($identity->myRole) {
           case Manager::OWNER_ADMIN:
               return true;
               break;
            case Manager::CORP_ADMIN:
                return $identity->corp_master_id == $jobMaster->clientMaster->corp_master_id;
                break;
            case Manager::CLIENT_ADMIN:
                return $identity->client_master_id == $jobMaster->client_master_id;
                break;
            default :
                return false;
                break;
        }
    }
}