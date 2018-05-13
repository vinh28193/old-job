<?php

namespace app\models;

use app\common\SearchKey;
use app\models\manage\searchkey\JobDist;
use app\models\manage\searchkey\JobPref;
use app\models\manage\SearchkeyMaster;
use app\models\queries\JobDispQuery;
use Yii;
use yii\db\ActiveRecord;
use yii\elasticsearch\ActiveQuery;
use yii\helpers\ArrayHelper;
use app\models\manage\JobMaster;
use yii\helpers\Url;
use yii\base\Model;
use app\models\manage\searchkey\PrefDistMaster;
use app\models\manage\searchkey\PrefDist;
use app\models\manage\searchkey\Area;
use app\models\manage\searchkey\Pref;
use app\models\manage\searchkey\Dist;
/**
 * 求職者画面側で使用する仕事に関するモデル
 *
 * @property string             $clientName
 * @property string             $stationText
 * @property string             $prefNames
 * @property string             $distNames
 * @property string             $imagePath
 * @property SearchkeyMaster    $searchCds
 * @property string             $prefSearchUrl
 * @property string             $distSearchUrl
 */
class JobMasterDisp extends JobMaster
{
    /**
     * 画像パス
     * %clientId%=掲載企業ID
     * %imageName%=画像名
     */
    const IMAGE_PATH = '%imageName%';

    /**
     * 仕事メール転送入力メールアドレス
     * @var string
     */
    public $mailAddress;

    /**
     * 仕事メール転送入力メッセージ
     * @var string
     */
    public $message;

    /**
     * プレビュー機能で使用するか
     * @var bool
     */
    public $isPreview = false;

    /**
     * パンくずリスト用 エリア情報
     * @var array
     */
    private $_areaInfo = [
        'areaDir' => null,
        'prefName' => null,
        'prefNo' => null,
        'distName' => null,
        'distCd' => null,
    ];

    /**
     * 検索コード
     * @var bool
     */
    private $_searchCds;

    /**
     * 継承元のjobMasterモデルでloadAuthParamの処理が走るので、オーバーライドしている
     * @return bool
     */
    public function beforeValidate()
    {
        // 継承元でbeforeValidate()が定義されているので、大本を参照するようにしている
        return Model::beforeValidate();
    }

    /**
     * ルール設定
     * @return array
     */
    public function rules()
    {
        $jobMasterDispRules =
            [
                ['mailAddress', 'required'],
                ['mailAddress', 'email'],
                [['message', 'isPreview'], 'safe'],
            ];
        // プレビュー機能で呼び出される場合は、load()を行うのでjobMasterのルールを取り込む
        return $this->isPreview ? ArrayHelper::merge(parent::rules(), $jobMasterDispRules) : $jobMasterDispRules;
    }

    public function attributeLabels()
    {
        $parentAttributeLabels = parent::attributeLabels();
        return ArrayHelper::merge($parentAttributeLabels, [
            'mailAddress' => Yii::t('app', 'メールアドレス'),
            'message'     => Yii::t('app', 'メッセージ'),
            'stationText' => ArrayHelper::getValue($parentAttributeLabels, 'station'),
            //stationは一覧表示する時に、stationTextを参照するのでstationに定義されているラベルを割り当てる。
            'clientName'  => ArrayHelper::getValue($parentAttributeLabels, 'client_master_id'),
            //上記同様
            'updated_at'  => Yii::t('app', '更新日'),
        ]);
    }

    /**
     * loadメソッド拡張
     * JobMasterのloadではなく祖先のloadを実施。
     * @param array $data
     * @param string $formName
     * @return bool
     */
    public function load($data, $formName = null)
    {
        return ActiveRecord::load($data, $formName);
    }

    /**
     * @inheritdoc
     * @return JobDispQuery the active query used by this AR class.
     */
    public static function find()
    {
        $query = new JobDispQuery(get_called_class());

        return $query->innerJoinWith([
            'clientMaster.corpMaster',
            'clientChargePlan.dispType',
        ]);
    }

    /**
     * 掲載企業名の取得
     * @return string
     */
    public function getClientName()
    {
        return $this->clientMaster ? $this->clientMaster->client_name : null;
    }

    /**
     * 代理店名の取得
     * @return string
     */
    public function getCorpLabel()
    {
        return $this->clientMaster->corpMaster ? $this->clientMaster->corpMaster->corp_name : null;
    }

    /**
     * 仕事情報表示用モデルの取得
     * @param int  $job_no    仕事ナンバー
     * @param bool $isPreview プレビュー機能用であるか
     * @return array|null|\yii\db\ActiveRecord
     */
    public static function findDispModel($job_no, $isPreview = false)
    {
        $query = self::find()->findOne($job_no);

        /** @var SearchKey $searchKeyComponent */
        $searchKeyComponent = Yii::$app->searchKey;
        foreach ((array)$searchKeyComponent->iconSearchKeys as $tableName => $searchKey){
            switch ($tableName) {
                case 'pref' :
                    break;
                case 'station' :
                    $query->with("{$searchKey->jobRelationName}.station");
                    break;
                case 'wage_category' :
                    $query->with("{$searchKey->jobRelationName}.wageItem");
                    break;
                case 'job_type_category' :
                    $query->with("{$searchKey->jobRelationName}.jobTypeSmall");
                    break;
                default:
                    if (strpos($tableName, 'item') !== false) {
                        $query->with("{$searchKey->jobRelationName}.searchKeyItem");
                    } else {
                        $query->with("{$searchKey->jobRelationName}.searchKeyItem.category");
                    }
                    break;
            }
        }
        // プレビュー機能によらない表示の場合は、有効かどうかを判定する
        if (!$isPreview) {
            $query->active();
        }

        return $query->one();
    }

    /**
     * 仕事情報簡易表示用modelを取得
     * @param $job_no
     * @return null|\yii\db\ActiveRecord
     */
    public static function findShortDispModel($job_no)
    {
        return self::find()->findOne($job_no)->active()->one();
    }

    /**
     * 都道府県名を取得する
     * @return string
     */
    public function getPrefNames()
    {
        return $this->_areaInfo['prefName'];
    }

    /**
     * 市区町村名を取得する
     * @return string
     */
    public function getDistNames()
    {
        return $this->_areaInfo['distName'];
    }

    /**
     * パンくずで使う都道府県検索URL
     * @return string
     */
    public function getPrefSearchUrl()
    {
        return Url::to( '/' . $this->_areaInfo['areaDir'] . '/search-result/' . $this->searchCds->first_hierarchy_cd . $this->_areaInfo['prefNo']);
    }

    /**
     * パンくずで使う市区町村検索URL
     * @return string
     */
    public function getDistSearchUrl()
    {
        return Url::to( '/' . $this->_areaInfo['areaDir'] . '/search-result/' . $this->searchCds->third_hierarchy_cd . $this->_areaInfo['distCd']);
    }

    /**
     * searchで使うコードを取得する
     * 今のところは他は必要ないので地域のみ取得している
     * @return SearchkeyMaster
     */
    public function getSearchCds()
    {
        if (!$this->_searchCds) {
            $this->_searchCds = SearchkeyMaster::find()->select([
                'first_hierarchy_cd',
                'third_hierarchy_cd',
            ])->where(['table_name' => 'pref'])->one();
        }
        return $this->_searchCds;
    }

    /**
     * 仕事ID配列を取得する
     * 条件1: 有効な原稿である
     * 条件2: 指定の都道府県内
     * @param $prefIds array 指定する都道府県ID
     * @return array|int 有効な仕事ID配列
     */
    public static function jobIds($prefIds)
    {
        $jobIds = self::find()->active()->distinct()->select([
            JobMaster::tableName() . '.id',
        ])->joinWith([
            'jobPref',
        ])->andWhere([
            JobPref::tableName() . '.pref_id' => $prefIds,
        ])->column();

        return $jobIds;
    }

    /**
     * 紐付く勤務地の都道府県有効チェック
     * （１つでも有効であればtrue）
     *
     * @return bool
     */
    public function checkPrefArea()
    {
        foreach ($this->jobPref as $jobPref) {
            /** @var $jobPref JobPref */
            if ($jobPref->checkPrefArea()) {
                return true;
            }
        }
        return false;
    }

    /**
     * パンくず用のエリア情報をセット
     *
     * @param array
     */
    public function prepareBreadCrumbAreaInfo($areaInfo)
    {

        // 地域グループコードしかセットされていない場合
        if (empty($areaInfo['prefNos']) && !empty($areaInfo['prefDistMasterNos']) && empty($areaInfo['distCds'])) {
            // 地域グループから都道府県コードと市町村コードを取得
            $prefDistMaster = PrefDistMaster::find()->with([
                'prefDist',
                'pref',
            ])->where([
                'pref_dist_master_no' => $areaInfo['prefDistMasterNos'],
                'valid_chk' => 1,
            ])->orderBy(['sort' => SORT_ASC])->all();

            /** @var PrefDistMaster $prefDist */
            foreach ((array)$prefDistMaster as $prefDist) {
                $dists = ArrayHelper::getColumn($prefDist->prefDist, function (PrefDist $model) {
                    return $model->dist_id;
                });
                $prefNo = $prefDist->pref->pref_no;

                $areaInfo['distCds'] = is_array($areaInfo['distCds']) ? ArrayHelper::merge($areaInfo['distCds'], $dists) : $dists;
                $areaInfo['prefNos'][] = $prefNo;
            }

            $areaInfo['prefNos'] = array_unique($areaInfo['prefNos']);
        }

        // エリアディレクトリ
        $areaList = [];
        foreach ((array)$this->jobPref as $jobPref) {
            /** @var JobPref $jobPref */
            if ($jobPref->checkPrefArea()) {
                $areaList[] = $jobPref->pref->area->area_dir;
                if ($areaInfo['areaDir'] === $jobPref->pref->area->area_dir) {
                    $this->_areaInfo['areaDir'] = $jobPref->pref->area->area_dir;
                    break;
                }
            }
        }
        $this->_areaInfo['areaDir'] = $this->_areaInfo['areaDir'] ?? array_shift($areaList);

        // 都道府県名、都道府県コード
        $prefNameList = [];
        $prefNoList = [];
        /** @var JobPref $jobPref */
        foreach ((array)$this->jobPref as $jobPref) {
            // 都道府県が取得できない、都道府県が無効ならば次へ
            if (!isset($jobPref->pref) || !$jobPref->checkPrefArea()) {
                continue;
            }

            $prefNameList[] = $jobPref->pref->pref_name;
            $prefNoList[] = $jobPref->pref->pref_no;

            if (!empty($areaInfo['prefNos'])) {
                // 都道府県コードの指定がある場合
                $exp = in_array($jobPref->pref->pref_no, $areaInfo['prefNos']);
            } else {
                // 都道府県コードの指定がない場合
                $exp = $jobPref->pref->area->area_dir === $this->_areaInfo['areaDir'];
            }

            if ($exp) {
                $this->_areaInfo['prefName'] = $jobPref->pref->pref_name;
                $this->_areaInfo['prefNo'] = $jobPref->pref->pref_no;
                break;
            }
        }
        $this->_areaInfo['prefName'] = $this->_areaInfo['prefName'] ?? array_shift($prefNameList);
        $this->_areaInfo['prefNo'] = $this->_areaInfo['prefNo'] ?? array_shift($prefNoList);

        // 市町村名、市町村コード
        $distNameList = [];
        $distCdList = [];
        /** @var JobDist $jobDist */
        foreach ((array)$this->jobDist as $jobDist) {
            // 市町村が取得できなければ次へ
            if (!isset($jobDist->dist)) {
                continue;
            }

            $distNameList[] = $jobDist->dist->dist_name;
            $distCdList[] = $jobDist->dist->dist_cd;

            if (!empty($areaInfo['distCds'])) {
                // 市町村コードの指定がある場合、指定された市町村コードとの一致をチェック
                // ※都道府県と市町村の整合性は見ない
                $exp = in_array($jobDist->dist->dist_cd, $areaInfo['distCds']);
            } else {
                // 市町村コードの指定がない場合、都道府県コードとの一致をチェック
                $exp = $jobDist->dist->pref_no === $this->_areaInfo['prefNo'];
            }

            if ($exp) {
                $this->_areaInfo['distName'] = $jobDist->dist->dist_name;
                $this->_areaInfo['distCd'] = $jobDist->dist->dist_cd;
                break;
            }
        }
        $this->_areaInfo['distName'] = $this->_areaInfo['distName'] ?? array_shift($distNameList);
        $this->_areaInfo['distCd'] = $this->_areaInfo['distCd'] ?? array_shift($distCdList);
    }
}
