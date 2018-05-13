<?php

namespace app\models\queries;

use yii\db\ActiveQuery;
use app\models\manage\AdminMaster;

/**
 * 管理者系クエリ
 *
 */
class AdminQuery extends ActiveQuery
{
    /** 状態 - 有効or無効 */
    const VALID = 1;
    const INVALID = 0;

    /**
     * 指定された代理店IDの管理者を取得する
     * @param integer $corpMasterId
     * @return $this
     */
    public function addCorpAdminQuery($corpMasterId)
    {
        $conditon = [
            AdminMaster::tableName() . '.valid_chk' => self::VALID,
            AdminMaster::tableName() . '.corp_master_id' => $corpMasterId,
            AdminMaster::tableName() . '.client_master_id' => null,
        ];

        return $this->andWhere($conditon);
    }
}