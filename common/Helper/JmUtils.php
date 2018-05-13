<?php
/**
 * Created by PhpStorm.
 * User: proseeds
 * Date: 2016/06/23
 * Time: 10:51
 */

namespace app\common\Helper;

use proseeds\base\Tenant;
use Yii;
use yii\helpers\Html;
use yii\helpers\Url;

/**
 * Class JmUtils
 *
 * @package app\common\Helper
 */
class JmUtils
{

    /* 保存する実ファイル名に挿入するランダム文字列の長さ（） */
    const RANDOM_LENGTH = 10;

    /**
     * Returns a value indicating whether the give value is "empty".
     *
     * The value is considered "empty", if one of the following conditions is satisfied:
     *
     * - it is `null`,
     * - an empty string (`''`),
     * - a string containing only whitespace characters,
     * - or an empty array.
     *
     * @param mixed $value
     * @return boolean if the value is empty
     */
    public static function isEmpty($value)
    {
        return $value === '' || $value === [] || $value === null || (is_string($value) && trim($value) === '');
    }

    /**
     * 文字列からスペースを除去する
     *
     * @param $string
     * @return mixed
     */
    public static function removeWhitespace($string)
    {
        return str_replace([' ', '　'], '', $string);
    }

    /**
     * リンク付きのCSV入力規則の文面に整形する返す。
     *
     * @param string $label
     * @param array $url
     * @param string $text
     * @param string $code
     * @param string $keyName
     * @return string
     */
    public static function rulesText($label, $url, $text, $code, $keyName = '')
    {
        return Yii::t(
            'app',
            $text,
            [
                'LABEL' => Html::encode($label),
                'LINK' => Html::a(
                    Yii::t('app', 'こちら'),
                    '#rulesText-' . $code,
                    [
                        'onclick' => "javascript:window.open('" . Url::to($url) . "', '_blank')",
                        'id' => 'rulesText-' . $code,
                    ]
                ),
                'KEY_NAME' => $keyName ? Html::encode($keyName) : Yii::t('app', '検索キーコード'),
            ]
        );
    }

    /**
     * 元のファイル名をランダムなファイル名に変換する。
     *
     * @param string $fileName
     * @return mixed
     */
    public static function randomFileName($fileName)
    {
        return date('Ymd') . '_' . Yii::$app->security->generateRandomString(self::RANDOM_LENGTH) . '.' . pathinfo($fileName, PATHINFO_EXTENSION);
    }

    /**
     * ファイルパスから拡張子を取得する
     * 拡張子の無いファイルは想定していないので注意
     * @param $filePath
     * @return string|null
     */
    public static function extension(string $filePath): string
    {
        $parsedFileName = explode('.', $filePath);
        return array_pop($parsedFileName);
    }

    /**
     * ファイルパスを入れればurlのpathを返す
     * @param $filePath
     * @return string
     */
    public static function fileUrl(string $filePath):string
    {
        return '/systemdata/' . $filePath;
    }
}
