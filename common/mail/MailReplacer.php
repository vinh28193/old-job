<?php

namespace app\common\mail;

use app\models\MailSend;
use yii\base\Object;

class MailReplacer extends Object
{

    const CONST_REPLACE_USER_NAME = "{USER_NAME}";
    const CONST_REPLACE_LOGIN_ID = "{LOGIN_ID}";
    const CONST_REPLACE_PASSWORD = "{PASSWORD}";

    /**
     * メール文面を置換
     * @param $mailSend
     * @param $entityId
     * @return MailSend
     */
    public function tenantReplace($mailSend, $entityId)
    {
        return $mailSend;
    }

    /**
     * ユーザー固有情報の置換
     * @param $mailSend
     * @param $receiver
     * @return MailSend
     */
    public function replaceForUser($mailSend, $receiver)
    {
        return $mailSend;
    }
}