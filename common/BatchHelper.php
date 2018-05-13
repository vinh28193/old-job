<?php

namespace app\common;


use Exception;
use Yii;

/**
 * Class batchHelper
 * @package app\common
 */
class BatchHelper
{
    /**
     * エラーメールを送信
     * @param $ex
     * @param $tenant_id
     * @param $type
     */
    public static function sendErrorMail($ex, $tenant_id, $type, $servertype)
    {
        try {
            $mailTitle = 'サイトマップ自動生成で処理の失敗が発生しました';

            $mailBody = <<<BODY
エラーが発生しました。
以下がエラー内容になります。

サーバータイプ：  {$servertype}
テナントID　　：  {$tenant_id}
バッチタイプ　：  {$type}
エラー内容　　：
{$ex}
BODY;
            Yii::$app->errorMail->send($mailTitle, $mailBody);

        } catch (Exception $ex) {
            // todo メール送信失敗時の処理
        }
    }
}