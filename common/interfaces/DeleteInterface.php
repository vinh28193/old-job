<?php
namespace app\common\interfaces;

/**
 * Created by PhpStorm.
 * User: proseeds
 * Date: 2018/01/29
 * Time: 18:33
 */

/**
 * Interface SearchModelInterface
 * 削除仕様のカプセル化の為に作りました。
 * 適用ヵ所はまだ少ないです。
 * @package app\common\interfaces
 */
interface DeleteInterface
{
    /**
     * 削除するidを返す
     * @param $params
     * @return array|bool
     */
    public function deleteSearch($params);

    /**
     * idを元にrelationレコードやファイル実体も含めて削除する
     * @param array $ids
     * @param array $params
     * @return int
     */
    public static function deleteAllData(array $ids, $params = []):int;
}
