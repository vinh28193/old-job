<?php

namespace app\common;

use yii;
use yii\base\Component;

class ErrorMail extends Component
{
    public $fromAddress;
    public $toAddress;

    public function __construct($config = [])
    {
        parent::__construct($config);
    }

    public function send($mailTitle, $mailBody)
    {
        $mailFrom = [$this->fromAddress => "JobMaker2"];
        $mailTo = $this->toAddress;

        // エラーメールを送信
        $message = Yii::$app->mailer->compose()
            ->setFrom($mailFrom)
            ->setSubject($mailTitle)
            ->setTextBody($mailBody);

        $message->setTo($mailTo)
            ->send();
    }
}