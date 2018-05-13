<?php
/**
 * Created by PhpStorm.
 * User: proseeds
 * Date: 2016/07/28
 * Time: 13:47
 */

namespace app\common;

use app\models\manage\SiteMaster;
use app\models\ToolMaster;
use app\models\manage\JobMaster;
use Yii;
use yii\base\Component;

/**
 * Class Site
 * todo tenantテーブルの役割との兼ね合いも含めて再設計
 * todo viewのレンダリングの際に各ページ1レコードしか使われないものを$appで持っておく意味も無い
 * @package app\common
 *
 * @property SiteMaster $info
 * @property ToolMaster $toolMaster
 * @property int        $toolNo
 * @property string     $searchname
 * @property JobMaster  $jobMaster
 */
class Site extends Component
{
    /**
     * @var SiteMaster|null
     */
    private $_siteMaster;

    /**
     * タグ変換変数と該当カラム名の対応
     */
    const TAG_CONVERSION_MAP = [
        '[NO1]'  => 'job_no',
        '[NO2]'  => 'corp_name_disp',
        '[NO3]'  => 'job_pr',
        '[NO4]'  => 'main_copy',
        '[NO5]'  => 'job_type_text',
        '[NO6]'  => 'work_place',
        '[NO7]'  => 'station',
        '[NO8]'  => 'wage_text',
        '[NO9]'  => 'transport',
        '[NO10]' => 'work_period',
        '[NO11]' => 'work_time_text',
        '[NO12]' => 'requirement',
        '[NO13]' => 'conditions',
        '[NO14]' => 'holidays',
        '[NO15]' => 'job_comment',
        '[NO16]' => 'application_place',
        '[NO17]' => 'option100',
        '[NO18]' => 'option101',
        '[NO19]' => 'option102',
        '[NO20]' => 'option103',
        '[NO21]' => 'option104',
        '[NO22]' => 'option105',
        '[NO23]' => 'option106',
        '[NO24]' => 'option107',
        '[NO25]' => 'option108',
        '[NO26]' => 'option109',
    ];

    /**
     * JobMasterモデル
     *
     * @type \app\models\manage\JobMaster
     */
    private $_jobMaster;

    /**
     * tool_master.tool_no
     * todo tdkが設定されていないと無関係なところに問答無用でこのtdkが出るのはマズイのでは？
     * @type int
     */
    private $_toolNo = 1;

    /**
     * job_master.job_no
     *
     * @type int
     */
    private $_jobNo;

    /**
     * ToolMasterモデルの配列
     *
     * @type \app\models\ToolMaster[]
     */
    private $_toolMaster = [];

    /**
     * [SITENAME]置換用の文字列
     *
     * @type string
     */
    private $_sitename;

    /**
     * [AREANAME]置換用の文字列
     *
     * @type string
     */
    private $_areaname;

    /**
     * [SEARCHNAME]置換用の文字列
     *
     * @type string
     */
    private $_searchname;

    /**
     * サイト情報を取得する
     *
     * @return SiteMaster|null
     */
    public function getInfo()
    {
        if (!$this->_siteMaster) {
            $this->_siteMaster = SiteMaster::find()->one();
        }

        return $this->_siteMaster;
    }

    /**
     * tool_noを設定する
     *
     * @param int $no
     */
    public function setToolNo($no)
    {
        $this->_toolNo = $no;
    }

    /**
     * areanameを設定する
     *
     * @param string $name
     */
    public function setAreaname($name)
    {
        $this->_areaname = $name;
    }

    /**
     * job_noを設定する
     *
     * @param int $no
     */
    public function setJobNo($no)
    {
        $this->_jobNo = $no;
    }

    /**
     * JobMasterを設定する
     *
     * @param JobMaster $jobMaster
     */
    public function setJobMaster(JobMaster $jobMaster)
    {
        $this->_jobMaster = $jobMaster;
    }

    /**
     * searchnameを設定する
     *
     * @param array $names
     */
    public function setSearchname($names)
    {
        $this->_searchname = $names;
    }

    /**
     * ToolMasterを取得
     *
     * @return \app\models\ToolMaster
     */
    public function getToolMaster()
    {
        if (is_null($this->_sitename)) {
            $this->_sitename = Yii::$app->tenant->tenant->tenant_name;
        }

        if (!is_null($this->_jobNo) && is_null($this->_jobMaster)) {
            $this->_jobMaster = JobMaster::find()->where(['job_no' => $this->_jobNo])->one();
        }

        $no = $this->_toolNo;
        if (!isset($this->_toolMaster[$no])) {
            $this->_toolMaster[$no] = ToolMaster::find()->where(['tool_no' => $no])->one();
        }

        $toolMaster = clone $this->_toolMaster[$no];
        $this->replaceToolMasterTitle($toolMaster);
        $this->replaceToolMasterDescription($toolMaster);
        $this->replaceToolMasterKeywords($toolMaster);
        $this->replaceToolMasterH1($toolMaster);

        return $toolMaster;
    }

    /**
     * tool_master.titleを置換する
     *
     * @param ToolMaster $toolMaster
     */
    private function replaceToolMasterTitle(ToolMaster $toolMaster)
    {
        $title             = $toolMaster->title;
        $toolMaster->title = $title == ''
            ? $this->_sitename
            : $this->replaceToolMaster($title);
    }

    /**
     * tool_master.descriptionを置換する
     *
     * @param ToolMaster $toolMaster
     */
    private function replaceToolMasterDescription(ToolMaster $toolMaster)
    {
        $description             = $toolMaster->description;
        $toolMaster->description = $description == ''
            ? $this->_sitename
            : $this->replaceToolMaster($description);
    }

    /**
     * tool_master.keywordsを置換する
     *
     * @param ToolMaster $toolMaster
     */
    private function replaceToolMasterKeywords(ToolMaster $toolMaster)
    {
        $keywords             = $toolMaster->keywords;
        $toolMaster->keywords = $keywords == ''
            ? $this->_sitename
            : $this->replaceToolMaster($keywords);
    }

    /**
     * tool_master.h1を置換する
     *
     * @param ToolMaster $toolMaster
     */
    private function replaceToolMasterH1(ToolMaster $toolMaster)
    {
        $h1             = $toolMaster->h1;
        $toolMaster->h1 = $h1 == ''
            ? $this->_sitename
            : $this->replaceToolMaster($h1);
    }

    /**
     * title, description, keywords, h1の文字列の置換
     *
     * @param string $str
     * @return string
     */
    private function replaceToolMaster($str)
    {
        if (strpos($str, '[SITENAME]') !== false) {
            $str = str_replace('[SITENAME]', $this->_sitename, $str);
        }

        if (strpos($str, '[AREANAME]') !== false) {
            $str = str_replace('[AREANAME]', $this->getAreaName(), $str);
        }

        if (strpos($str, '[SEARCHNAME]') !== false) {
            $str = str_replace('[SEARCHNAME]', $this->_searchname, $str);
        }

        foreach (self::TAG_CONVERSION_MAP as $tag => $column) {
            if (strpos($str, $tag) !== false) {
                $str = str_replace($tag, $this->getTagValue($column), $str);
            }
        }

        return $str;
    }

    /**
     * エリア名を取得する
     *
     * @return string
     */
    private function getAreaName()
    {
        return $this->_areaname ?: '';
    }

    /**
     * タグ変換用の文字列を取得する
     *
     * @param string $propName
     * @return string
     */
    private function getTagValue($propName)
    {
        if ($this->_jobMaster) {
            return $this->_jobMaster->{$propName};
        }

        return null;
    }
}
