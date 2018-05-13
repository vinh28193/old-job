<?php

namespace app\common\constants;

//TODO:他の機能の定数値もいずれちゃんと一つの定数値クラスにまとめる。
/**
 * Class MailConst
 * @package app\common\constants
 * メールに関するConstの定義
 */
class MailConst
{
    // メール種別：共通
    const MAIL_COMMON = 0;
    // メール種別：応募者
    const MAIL_TYPE_APPLICATION_DETAIL = 1;
    const MAIL_TYPE_ADMIN_REGISTER = 2;

    // メール送信対象
    const VALID = 1;
    const INVALID = 0;

    // 連携用IDが必要ないときの値
    const ENTITY_ZERO = 0;

    // 送信対象者：システム
    const SENDER_SYSTEM = 0;

    // 置換文字列に関するクラスの紐付け
    const REPLACER_SET = [
        self::MAIL_COMMON => '\app\common\mail\MailReplacer',
    ];
}