<?php

namespace app\models\queries;

use app\models\manage\WidgetData;
use app\models\manage\WidgetDataArea;
use yii\db\ActiveQuery;

/**
 * Class WidgetQuery
 * @package app\models\queries
 */
class WidgetQuery extends ActiveQuery
{
    /**
     * @param $areaId
     * @return $this
     */
    public function innerJoinWithData($areaId)
    {
        return $this->innerJoinWith([
            // widget_data検索
            'widgetData' => function (ActiveQuery $q) use ($areaId) {
                $q->where([
                    'and',
                    [WidgetData::tableName() . '.valid_chk' => WidgetData::VALID],
                    ['<=', WidgetData::tableName() . '.disp_start_date', time()],
                    [
                        'or',
                        ['>=', WidgetData::tableName() . '.disp_end_date', strtotime('today')],
                        [WidgetData::tableName() . '.disp_end_date' => null],
                    ],
                ])->orderBy([
                    WidgetData::tableName() . '.sort' => SORT_ASC,
                    WidgetData::tableName() . '.id'   => SORT_ASC,
                ])->innerJoinWith([
                    // widget_data_area検索
                    'widgetDataArea' => function (ActiveQuery $q) use ($areaId) {
                        $q->where([WidgetDataArea::tableName() . '.area_id' => $areaId]);
                    },
                ]);
            },
        ]);
    }
}
