<?php
/**
 * Created by PhpStorm.
 * User: proseeds
 * Date: 2015/11/24
 * Time: 21:22
 */

namespace app\rbac;

use yii\rbac\Rule;

class isOwnRule extends Rule
{
    public $name = 'isOwn';

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
        return isset($params['id']) ? $params['id'] == $user : false;
    }
}