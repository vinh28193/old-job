<?php
/**
 * Created by PhpStorm.
 * User: proseeds
 * Date: 2016/05/24
 * Time: 19:57
 */

namespace app\common;



use yii\helpers\Url;

class AccessControl extends \yii\filters\AccessControl
{
    protected function denyAccess($user)
    {
        if ($user->getIsGuest()) {
            $user->loginRequired();
        } else {
            \Yii::$app->getResponse()->redirect(Url::to('/manage/default/permission-denied/'));
        }
    }
}