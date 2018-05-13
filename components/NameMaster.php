<?php
/**
 * Created by PhpStorm.
 * User: proseeds
 * Date: 2016/08/31
 * Time: 11:20
 */

namespace app\components;

use yii\base\Component;

/**
 * Class NameMaseter
 * @package app\components
 *
 * @property \app\models\manage\searchkey\NameMaseter[] $models
 */
class NameMaster extends Component
{
    /** @string jobページの文言取得 */
    private $_jobName;

    /** @string applicationページの文言取得 */
    private $_applicationName;

    /**
     * jobページの文言を取得する
     */
    public function getJobName()
    {
        if ($this->_jobName === null) {
            $nameMaster = \app\models\manage\NameMaster::findOne(['name_id' => '2']);
            $this->_jobName = $nameMaster->change_name ?: Yii::t('app', '求人');
        }
        return $this->_jobName;
    }

    /**
     * jobページの文言を取得する
     */
    public function getApplicationName()
    {
        if ($this->_applicationName === null) {
            $nameMaster = \app\models\manage\NameMaster::findOne(['name_id' => '3']);
            $this->_applicationName = $nameMaster->change_name ?: Yii::t('app', '応募');
        }
        return $this->_applicationName;
    }
}
