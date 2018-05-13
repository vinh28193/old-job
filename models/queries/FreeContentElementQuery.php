<?php
/**
 * Created by PhpStorm.
 * User: proseeds
 * Date: 2018/01/29
 * Time: 18:45
 */

namespace app\models\queries;

use app\common\Helper\JmUtils;
use yii\db\ActiveQuery;

/**
 * Class FreeContentElementQuery
 * @package app\models\queries
 * @see \app\models\FreeContentElement
 */
class FreeContentElementQuery extends ActiveQuery
{
    /**
     * イメージファイルの名前の配列を返す
     * @return array
     */
    public function imageFileNames()
    {
        $names = $this->select('image_file_name')->distinct()->column();
        return array_filter($names, function ($v) {
            return !JmUtils::isEmpty($v);
        });
    }
}
