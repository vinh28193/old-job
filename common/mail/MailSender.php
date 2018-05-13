<?php

namespace app\common\mail;

use app\models\manage\SendMailSet;
use proseeds\models\MailSend;
use yii;
use proseeds\mail\BaseMailSender;
use app\common\constants\MailConst;
use yii\console\Exception;

/**
 * メール送信クラス
 * Class MailSender
 * @package app\common
 */
class MailSender extends BaseMailSender
{
    /**
     * @var array
     * Job_Makerで使用する置換クラス群
     */
    public $replaceClass = MailConst::REPLACER_SET;
    
    private $_mail;
    private $_users;
    private $_fromName;

    /**
     * @param $mail
     * @return $this
     */
    public function mail($mail)
    {
        $this->_mail = array_merge([
            'user_id' => Yii::$app->user->id ?: 0,
            'send_pc_chk' => MailConst::VALID,
            'send_mobile_chk' => MailConst::INVALID,
        ], $mail);
        return $this;
    }

    /**
     * @param $users
     * @return $this
     */
    public function users($users)
    {
        $this->_users = [];
        foreach ($users as $user) {
            $this->_users[] = array_merge([
                'mobile_mail_address' => '',
                'send_pc_chk' => MailConst::VALID,
                'send_mobile_chk' => MailConst::INVALID,
            ], $user);
        }
        return $this;
    }


    /**
     * @param mixed $fromName
     * @return $this
     */
    public function fromName($fromName)
    {
        $this->_fromName = (object)$fromName;
        return $this;
    }

    /**
     * 即時メールの送信
     * @return MailSend
     * @throws Exception
     */
    public function preparedInstantSend()
    {
        try {
            $mailSend = $this->prepareMailSend(Yii::$app->tenant->id, $this->_mail, $this->_users, MailSend::STATUS_INSTANT_READY, $this->_fromName);
            $this->send($mailSend, MailSend::STATUS_INSTANT_READY);
        } catch (Exception $ex) {
            throw $ex;
        }
        return $mailSend;
    }

    /**
     * @param $tenantId
     * @return \app\models\manage\SiteMaster|array|null
     */
    public function getFromMail($tenantId)
    {}

    /**
     * @param SendMailSet $sendMailSet
     * @throws Exception
     */
    public function sendAutoMail($sendMailSet)
    {
        $this->mail([
            'mail_title' => $sendMailSet->replacedTitle,
            'mail_body' => $sendMailSet->replacedBody,
            'mail_type_id' => $sendMailSet->mail_type_id,
            'entity_id' => $sendMailSet->entityId,
        ])->users($sendMailSet->sendUserProperties)->fromName([
            'from_mail_address' => $sendMailSet->from_address,
            'from_mail_name' => $sendMailSet->from_name,
        ])->preparedInstantSend();
    }
}