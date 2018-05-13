<?php
/**
 * Created by PhpStorm.
 * User: KNakamoto
 * Date: 2016/02/15
 * Time: 20:47
 */

namespace app\commands;

use yii;
use yii\base\Exception;
use yii\helpers\ArrayHelper;
use yii\console\Controller;
use yii\swiftmailer;
use app\models\MailSend;
use app\models\MailSendUserLog;

class MailController extends Controller
{
    const replace_start_string = '<<';

    const replace_end_string = '>>';
    
    public function actionIndex()
    {
        echo "Start process..." . PHP_EOL;

        try {

            $mailSend = MailSend::find()
                ->filterWhere(['send_status' => MailSend::CONST_STATUS_READY])
                ->all();

            foreach($mailSend as $mail) {
                // 処理開始前にもう一度実行されていないか確認
                if ($mail->send_status == MailSend::CONST_STATUS_READY) {
                    $mail->send_status = MailSend::CONST_STATUS_START;
                    $mail->send_start_time = time();
                    $mail->save();

                    foreach ($mail->mailSendUsers as $receiver) {
                        // レコードを再読み込みし、強制停止されていないかを確認
                        $status = MailSend::find()->where(['id' => $mail->id])->one();
                        if ($status->send_status == MailSend::CONST_STATUS_STOP) break;

                        // 送信先ユーザーに紐づく置換文字列に関して、送信直前で置き換える
                        $mail_body = $mail->mail_body;
                        foreach (yii::$app->params['replacement_strings'] as $param) {
                            $replacement = (array)json_decode($receiver->replacement_strings);
                            if (ArrayHelper::keyExists($param, $replacement)) {
                                $mail_body = str_replace(self::replace_start_string . $param . self::replace_end_string, $replacement[$param], $mail_body);
                            }
                        }

                        $message = Yii::$app->mailer->compose()
                            ->setFrom($mail->from_mail_address)
                            ->setSubject($mail->mail_title)
                            ->setTextBody($mail_body);

                        $sendLog = new MailSendUserLog();

                        if ($message->setTo($receiver->to_mail_address)->send()) {
                            $sendLog->send_status = MailSendUserLog::CONST_STATUS_SEND;
                            $mail->success_count++;
                        } else {
                            $sendLog->send_status = MailSendUserLog::CONST_STATUS_NO_SEND;
                            $mail->failure_count++;
                        }

                        $sendLog->tenant_id = $mail->tenant_id;
                        $sendLog->mail_send_id = $mail->id;
                        $sendLog->to_user_name = $receiver->to_user_name;
                        $sendLog->to_mail_address = $receiver->to_mail_address;
                        $sendLog->replacement_strings = $receiver->replacement_strings;
                        $sendLog->to_id = $receiver->to_id;

                        $sendLog->beforeSave(true);
                        $sendLog->save();
                    }

                    $mail->send_status = MailSend::CONST_STATUS_FINISH;
                    $mail->send_end_time = time();
                    $mail->save();
                }
            }

            echo "Process complete successfully." . PHP_EOL;

        } catch (Exception $e) {
            echo $e . PHP_EOL . PHP_EOL;
            echo "Process false." . PHP_EOL;
        }
    }

//    public function actionIndex()
//    {
//        echo "plz write command" . PHP_EOL;
//        try {
//
//            $a = Yii::$app->mailer->compose()
//                ->setFrom('thosaka@pro-seeds.co.jp')
//                ->setSubject('send_test_proseeds_thosaka-subject')
//                ->setTextBody('send_test_proseeds_thosaka-body')
//                ->setTo('thosaka@pro-seeds.co.jp')
//            ;
//            $a->send();
//
//        } catch (Exception $e) {
//            echo $e;
//        }
//    }

    public function actionSend()
    {
    }
}