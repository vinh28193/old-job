<?php

namespace app\models;

use app\models\manage\WidgetLayout;
use app\models\manage\Widget;
use app\models\manage\WidgetData;
use app\models\queries\WidgetQuery;

/**
 * 求職者画面側で使用するモデル。
 * @author Yukinori Nakamura<y_nakamura@id-frontier.jp>
 */
class WidgetLayoutDisp extends WidgetLayout
{
    /**
     * トップ画面表示用のウィジェットレイアウトモデルをすべて取得する。
     * @param int $areaId エリアID
     * @return static[]
     */
    public static function getShowWidgetLayouts($areaId)
    {
        $mainQuery = self::find()->innerJoinWith([
            // widget検索
            'widget' => function (WidgetQuery $q) use ($areaId) {
                $q->orderBy([
                    Widget::tableName() . '.sort' => SORT_ASC,
                    Widget::tableName() . '.id'   => SORT_ASC,
                ])->innerJoinWithData($areaId);
            },
        ]);
        //エリアIDが0でなければ（＝全国トップでなければ）各エリア共通レイアウトを読み込み
        if ($areaId != 0) {
            $mainQuery->andWhere(['area_flg' => self::AREA_COMMON,]);
        }
        return $mainQuery->all();
    }
}
