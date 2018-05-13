<?php
/**
 * Created by PhpStorm.
 * User: n.katayama
 * Date: 2016/11/03
 * Time: 18:16
 */

namespace models\manage;


use app\models\MailSend;
use tests\codeception\unit\JmTestCase;
use Yii;

class MailSendTest extends JmTestCase
{
    public function testRules()
    {
        $this->setIdentity('client_admin');
        $this->specify('required', function () {
            $model = new MailSend();
            $model->validate();
            verify($model->hasErrors('mail_title'))->true();
            verify($model->hasErrors('from_mail_address'))->true();
        });

        $this->specify('default', function () {
            // 何も値が入っていなければdefaultとしてadminIdが入る
            $model = new MailSend();
            $model->validate();
            verify($model->user_id)->equals($this->getIdentity()->id);
            // 入っていれば何もしない
            $model->user_id = 9999;
            $model->validate();
            verify($model->user_id)->equals(9999);
        });
    }
}
