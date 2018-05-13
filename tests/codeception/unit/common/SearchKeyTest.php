<?php
namespace tests\codeception\unit\common;

use app\common\SearchKey;
use app\models\JobMasterDisp;
use app\models\manage\JobMasterSearch;
use app\models\manage\searchkey\Dist;
use app\models\manage\searchkey\JobTypeSmall;
use app\models\manage\searchkey\JobWage;
use app\models\manage\searchkey\SearchkeyItem;
use app\models\manage\searchkey\SearchkeyItem11;
use app\models\manage\searchkey\Station;
use app\models\manage\searchkey\WageCategory;
use app\models\manage\searchkey\WageItem;
use yii;
use app\models\manage\SearchkeyMaster;
use tests\codeception\unit\JmTestCase;
use tests\codeception\fixtures\SearchkeyMasterFixture;
use yii\helpers\ArrayHelper;
use app\models\manage\searchkey\JobSearchkeyItem1;
use app\models\manage\searchkey\SearchkeyItem1;

class SearchKeyTest extends JmTestCase
{
    /**
     * @var \UnitTester
     */
    protected $tester;

    /**
     * fixture設定
     * @return array
     */
    public function fixtures()
    {
        return [
            'searchkey_master' => SearchkeyMasterFixture::className(),
        ];
    }

    /**
     * getSearchKeysのtest
     */
    public function testGetSearchKeys()
    {
        verify(Yii::$app->searchKey->searchKeys)->equals(SearchkeyMaster::findSearchKeys());
    }

    /**
     * getSearchKeyLabelsのtest
     */
    public function testGetSearchKeyLabels()
    {
        $searchKeyLabels = Yii::$app->searchKey->searchKeyLabels;

        /** @var SearchkeyMaster $searchKey */
        foreach (Yii::$app->searchKey->searchKeys AS $searchKey) {
            verify($searchKeyLabels[$searchKey->job_relation_table])->equals($searchKey->searchkey_name);
        }
    }

    /**
     * labelsのtest
     */
    public function testLabel()
    {
        /** @var SearchkeyMaster $searchKey */
        foreach (Yii::$app->searchKey->searchKeys AS $searchKey) {
            verify(Yii::$app->searchKey->label($searchKey->job_relation_table))->equals($searchKey->searchkey_name);
        }
    }

    /**
     * getIconSearchKeysのtest
     */
    public function testGetIconSearchKeys()
    {
        /** @var SearchKey $searchKeyComponent */
        $searchKeyComponent = Yii::$app->searchKey;
        foreach ($searchKeyComponent->iconSearchKeys as $searchKey) {
            verify($searchKey->icon_flg)->equals(SearchkeyMaster::ICON_FLG_VALID);
            verify(ArrayHelper::isIn($searchKey->table_name, SearchkeyMaster::ICON_STATIC_KEYS))->false();
        }
    }

    /**
     * searchkey_masterのsortに依存（給与→汎用１→汎用１１の順）
     *

     */
    public function testSearchKeyIconContents()
    {
        $this->setIdentity('owner_admin');
        // 仕事情報の準備
        /** @var JobMasterDisp $jobMasterDisp */
        $jobMasterDisp = JobMasterDisp::find()->one();
        // 元の検索キーを削除
        $jobMasterDisp->unlinkAll('jobDist', true);
        $jobMasterDisp->unlinkAll('jobStation', true);
        $jobMasterDisp->unlinkAll('jobWage', true);
        $jobMasterDisp->unlinkAll('jobPref', true);
        for ($i = 1; $i <= 20; $i++) {
            $jobMasterDisp->unlinkAll('jobSearchkeyItem' . $i, true);
        }
        // 検索キーを紐づけ
        $dist = Dist::findOne(1);
        $station = Station::findOne(['station_no' => 20010]);
        // 汎用は3つずつ紐づけ
        foreach (range(1, 20) as $i) {
            /** @var SearchkeyItem $className (実際は文字列) */
            $className = SearchkeyMaster::MODEL_BASE_PATH . "SearchkeyItem{$i}";
            $items = $className::find()->limit(3)->all();
            if ($items) {
                $commonKeys[$i] = $items;
                $post["JobSearchkeyItem{$i}"] = ['itemIds' => ArrayHelper::getColumn($items, 'id')];
            }
        }
        $jobMasterDisp->saveRelationalModels(array_merge([
            'JobDist' => ['itemIds' => [$dist->id]],
            'JobStation' => ['itemIds' => [$station->station_no]],
        ], $post));

        // 検索キーの設定変更
        // 二階層
        // 汎用１はvalid_chk=1,icon_flg=0
        // 汎用２はvalid_chk=1,icon_flg=0
        // 汎用６はvalid_chk=0,icon_flg=1
        // その他はvalid_chk=0,icon_flg=0
        // 一階層
        // 汎用１１はvalid_chk=1,icon_flg=0
        // 汎用１２はvalid_chk=1,icon_flg=0
        // 汎用１６はvalid_chk=0,icon_flg=1
        // その他はvalid_chk=0,icon_flg=0
        $validSearchKeys = [1,2,11,12];
        $iconSearchKeys = [1,6,11,16];
        foreach (range(1, 20) as $i) {
            /** @var SearchkeyMaster $searchKey */
            $searchKey = SearchkeyMaster::find()->where(['job_relation_table' => "job_searchkey_item{$i}"])->one();
            if (in_array($i, $validSearchKeys)) {
                $searchKey->valid_chk = 1;
            } else {
                $searchKey->valid_chk = 0;
            }
            if (in_array($i, $iconSearchKeys)) {
                $searchKey->icon_flg = 1;
            } else {
                $searchKey->icon_flg = 0;
            }
            $searchKey->save(false);
        }

        /** @var SearchkeyItem1[] $searchKeyItems1 */
        /** @var SearchkeyItem11[] $searchKeyItems11 */
        $searchKeyItems1 = ArrayHelper::getColumn($jobMasterDisp->jobSearchkeyItem1, 'searchKeyItem');
        $searchKeyItems11 = ArrayHelper::getColumn($jobMasterDisp->jobSearchkeyItem11, 'searchKeyItem');

        // 全部有効な項目・カテゴリ
        foreach ($searchKeyItems1 as $item) {
            $item->valid_chk = 1;
            $item->category->valid_chk = 1;
        }
        foreach ($searchKeyItems11 as $item) {
            $item->valid_chk = 1;
        }

        $contents = (new SearchKey())->searchKeyIconContents($jobMasterDisp);
        verify($contents)->equals([
            $searchKeyItems1[0]->searchkey_item_name,
            $searchKeyItems1[1]->searchkey_item_name,
            $searchKeyItems1[2]->searchkey_item_name,
            $searchKeyItems11[0]->searchkey_item_name,
            $searchKeyItems11[1]->searchkey_item_name,
            $searchKeyItems11[2]->searchkey_item_name,
        ]);

        // 検索キー項目を無効にした時の検証
        $searchKeyItems1[0]->valid_chk = 0;
        $searchKeyItems11[0]->valid_chk = 0;

        $contents = (new SearchKey())->searchKeyIconContents($jobMasterDisp);
        verify($contents)->equals([
            $searchKeyItems1[1]->searchkey_item_name,
            $searchKeyItems1[2]->searchkey_item_name,
            $searchKeyItems11[1]->searchkey_item_name,
            $searchKeyItems11[2]->searchkey_item_name,
        ]);

        // 検索キーカテゴリを無効にした時の検証
        $searchKeyItems1[1]->category->valid_chk = 0;

       $contents = (new SearchKey())->searchKeyIconContents($jobMasterDisp);
        verify($contents)->equals([
            $searchKeyItems1[2]->searchkey_item_name,
            $searchKeyItems11[1]->searchkey_item_name,
            $searchKeyItems11[2]->searchkey_item_name,
        ]);

        self::getFixtureInstance('searchkey_master')->load();
        self::getFixtureInstance('job_master')->load();
    }
}