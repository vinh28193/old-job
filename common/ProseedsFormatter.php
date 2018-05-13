<?php
/**
 * Created by PhpStorm.
 * User: Noboru Sakamoto
 * Date: 2015/10/16
 * Time: 18:09
 */

namespace app\common;

use app\models\manage\SearchkeyMaster;
use app\models\manage\SendMailSet;
use Yii;
use yii\helpers\ArrayHelper;
use yii\i18n\Formatter;
use yii\helpers\Html;
use app\models\forms\JobSearchForm;

class ProseedsFormatter extends Formatter
{
    public $onOff;
    public $validChk;
    public $isMustItem;
    public $isSearchMenuItem;
    public $isPublished;
    public $isUsed;
    public $isMailTo;
    public $carrierType;
    public $mapUrl;
    public $nullDisplay = '';
    public $dateFormat = 'yyyy/MM/dd';
    public $datetimeFormat = 'yyyy/MM/dd HH:mm:ss';
    public $sex;
    public $tagFileNames;
    public $isCategoryLabel;
    public $isAndSearch;
    public $searchInputTool;
    public $isMoreSearch;
    public $isOnTop;
    public $isIconFlg;

    /**
     * 最初に実行されるメソッド
     */
    public function init()
    {
        parent::init();
        $this->onOff = [0 => Yii::t('app', '－'), 1 => Yii::t('app', '○')];
        $this->validChk = [0 => Yii::t('app', '無効'), 1 => Yii::t('app', '有効')];
        $this->isMustItem = [0 => Yii::t('app', '任意'), 1 => Yii::t('app', '必須')];
        $this->isSearchMenuItem = [0 => Yii::t('app', '非表示'), 1 => Yii::t('app', '表示')];
        $this->isPublished = [0 => Yii::t('app', '非公開'), 1 => Yii::t('app', '公開')];
        $this->isUsed = [0 => Yii::t('app', '使用しない'), 1 => Yii::t('app', '使用する')];
        $this->isMailTo = SendMailSet::getMailToLabel();
        $this->carrierType = [0 => Yii::t('app', 'PC'), 1 => Yii::t('app', 'スマートフォン')];
        $this->sex = [0 => Yii::t('app', '男性'), 1 => Yii::t('app', '女性')];
        $this->isCategoryLabel = SearchkeyMaster::getIsCategoryLabel();
        $this->isAndSearch = SearchkeyMaster::getIsAndSearch();
        $this->searchInputTool = SearchkeyMaster::getSearchInputTool();
        $this->isMoreSearch = SearchkeyMaster::getIsMoreSearch();
        $this->isOnTop = SearchkeyMaster::getIsOnTop();
        $this->isIconFlg = SearchkeyMaster::getIconFlg();
    }

    /**
     * ON/OFF系フラグ（○or－）
     * @param null|int $value
     * @return string
     */
    public function asOnOff($value)
    {
        if ($value === null) {
            return $this->nullDisplay;
        }
        return $this->onOff[$value];
    }

    /**
     * valid_chk（有効or無効）
     * @param null|int $value
     * @return string
     */
    public function asValidChk($value)
    {
        if ($value === null) {
            return $this->nullDisplay;
        }
        return $this->validChk[$value];
    }

    /**
     * is_must（任意or必須）
     * @param null|int $value
     * @return string
     */
    public function asIsMustItem($value)
    {
        if ($value === null) {
            return Yii::t('app', '必須（固定）');
        }
        return $this->isMustItem[$value];
    }

    /**
     * is_in_list（非表示or表示）
     * @param null|int $value
     * @return string
     */
    public function asIsListMenuItem($value)
    {
        if ($value === null) {
            return Yii::t('app', '非表示（固定）');
        }
        return $this->isSearchMenuItem[$value];
    }

    /**
     * is_in_search（非表示or表示）
     * @param null|int $value
     * @return string
     */
    public function asIsSearchMenuItem($value)
    {
        if ($value === null) {
            return Yii::t('app', '表示（固定）');
        }
        return $this->isSearchMenuItem[$value];
    }

    /**
     * valid_chk（非公開or公開）
     * @param null|int $value
     * @return string
     */
    public function asIsPublished($value)
    {
        if ($value === null) {
            return $this->nullDisplay;
        }
        return $this->isPublished[$value];
    }

    /**
     * valid_chk（使用しないor使用する）
     * @param null|int $value
     * @return string
     */
    public function asIsUsed($value)
    {
        if ($value === null) {
            return $this->nullDisplay;
        }
        return $this->isUsed[$value];
    }

    /**
     * mail_to
     * @param null|int $value
     * @return string
     */
    public function asIsMailTo($value)
    {
        if ($value === null) {
            return $this->nullDisplay;
        }
        return ArrayHelper::getValue($this->isMailTo, $value);
    }

    /**
     * carrier_type
     * @param null|int $value
     * @return string
     */
    public function asCarrierType($value)
    {
        if ($value === null) {
            return $this->nullDisplay;
        }
        return ArrayHelper::getValue($this->carrierType, $value);
    }

    /**
     * マップURLのリンク化
     * @param string $value URL
     * @return string
     */
    public function asMapUrl($value)
    {
        if ($value === null) {
            return $this->nullDisplay;
        }
        return Html::a(Yii::t('app', '>>アクセスする'), Html::encode($value), ['target' => '_blank']);
    }

    /**
     * sex
     * @param null|int $value
     * @return string
     */
    public function asSex($value)
    {
        if ($value === null) {
            return $this->nullDisplay;
        }
        return ArrayHelper::getValue($this->sex, $value);
    }

    /**
     * マップURLのリンク化
     * @param string $value URL
     * @param array $options
     * @return string
     */
    public function asNewWindowUrl($value, $options = [])
    {
        return $this->asUrl($value, ['target' => '_blank'] + $options);
    }

    /**
     * htmlエンコードして改行だけを反映させる
     * @param $value
     * @return string
     */
    public function asJobView($value)
    {
        return nl2br(Html::encode($value));
    }

    /**
     * @param $value
     * @return string
     */
    public function asIsAndSearch($value)
    {
        if ($value === null) {
            return Yii::t('app', 'or（固定）');
        }
        return $this->isAndSearch[$value];
    }

    /**
     * @param $value
     * @return string
     */
    public function asSearchInputTool($value)
    {
        if ($value === null) {
            return Yii::t('app', 'モーダル（固定）');
        }
        return $this->searchInputTool[$value];
    }

    /**
     * @param $value
     * @return string
     */
    public function asIsMoreSearch($value)
    {
        if ($value === null) {
            return $this->nullDisplay;
        }
        return $this->isMoreSearch[$value];
    }

    /**
     * @param $value
     * @return string
     */
    public function asIsOnTop($value)
    {
        if ($value === null) {
            return $this->nullDisplay;
        }
        return $this->isOnTop[$value];
    }

    /**
     * @param $value
     * @return string
     */
    public function asIsIconFlg($value)
    {
        if ($value === null) {
            return Yii::t('app', '表示しない（固定）');
        }
        return $this->isIconFlg[$value];
    }

    /**
     * @param $value
     * @return string
     */
    public function asDateFormatter($value)
    {
        if ($value === null) {
            return $this->nullDisplay;
        }
        return Yii::$app->formatter->asDate($value, Yii::t('app', 'yyyy年MM月dd日'));
    }
}
