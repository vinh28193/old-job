<?php

namespace app\models\queries;

use yii\db\ActiveQuery;

/**
 * Class FreeContentQuery
 * 今のところ内容は無いが、1テーブル1ActiveQueryあった方が
 * 取り回ししやすいので、本クラスも枠のみ作っておく
 * @package app\models\queries
 * @see \app\models\FreeContent
 */
class FreeContentQuery extends ActiveQuery
{
}
