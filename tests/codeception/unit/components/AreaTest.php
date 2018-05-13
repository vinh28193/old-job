<?php
/**
 * Created by PhpStorm.
 * User: n.katayama
 * Date: 2016/11/03
 * Time: 19:45
 */

namespace app\components;

use tests\codeception\unit\JmTestCase;
use Yii;
use app\components\Area as AreaComp;
use app\models\manage\searchkey\Area as AreaModel;
use yii\db\Query;

class AreaTest extends JmTestCase
{
    /* @var $areaComp AreaComp */
    public $areaComp;

    public function setUp()
    {
        parent::setUp();
        $this->areaComp = Yii::$app->area;
    }

    public function testGetModels()
    {
        $sort = 0;
        foreach ($this->areaComp->models as $area) {
            /* @var $area AreaModel */
            verify($area->valid_chk)->equals(AreaModel::FLAG_VALID);
            verify($area->sort)->greaterOrEquals($sort);
            $sort = $area->sort;
        }
        $count = (new Query())
                ->from(AreaModel::tableName())
                ->where(['valid_chk' => AreaModel::FLAG_VALID])
                ->count();
        verify(count($this->areaComp->models))->equals($count);
    }

    /**
     * isOneArea()のtest
     * areaのfixtureが、複数エリア有効である必要があります
     */
    public function testIsOneArea()
    {
        //1エリアでない場合、falseを返すことを確認。
        $areaComp = new AreaComp();
        verify($areaComp->isOneArea())->equals(false);

        // ワンエリア状態に変更
        $this->changeToOneArea();

        //1エリアの場合、trueを返すことを確認。
        $areaComp = new AreaComp();
        verify($areaComp->isOneArea())->equals(true);

        // 書き換えたのを元に戻す
        self::getFixtureInstance('area')->load();
    }

    /**
     * getTenantArea()のtest
     */
    public function testGetTenantArea()
    {
        $areaComp = new AreaComp();
        // 複数エリアが有効な場合
        foreach ($areaComp->tenantArea as $key => $area) {
            /* @var $area AreaModel */
            // 最初に全国エリアが来る
            if ($key === 0) {
                verify($area->id)->equals(AreaModel::NATIONWIDE_ID);
                verify($area->area_name)->equals('全国');
                continue;
            }
            // 残りは有効なエリア
            verify($area->valid_chk)->equals(AreaModel::FLAG_VALID);
        }
        // カウントで漏れの無いことを確認
        verify(count($this->areaComp->tenantArea))->equals(count($this->areaComp->models) + 1);

        // ワンエリア状態に変更
        $areaModel = $this->changeToOneArea();
        $areaComp = new AreaComp();
        verify($areaComp->tenantArea[0]->attributes)->equals($areaModel->attributes);

        // 書き換えたのを元に戻す
        self::getFixtureInstance('area')->load();
    }

    /**
     * getListArray()のtest
     */
    public function testGetListArray()
    {
        $listArray = $this->areaComp->listArray;
        foreach ($this->areaComp->tenantArea as $area) {
            verify($listArray[$area->id])->equals($area->area_name);
        }
    }

    /**
     * getNationwideArea()のtest
     */
    public function testGetNationwideArea()
    {
        $nationwideArea = $this->areaComp->nationwideArea;
        verify($nationwideArea)->isInstanceOf(AreaModel::className());
        verify($nationwideArea->id)->equals(AreaModel::NATIONWIDE_ID);
        verify($nationwideArea->area_name)->equals(Yii::t('app', '全国'));
    }

    /**
     * getFirstArea()のtest
     */
    public function testGetFirstArea()
    {
        $area = AreaModel::find()->where(['valid_chk' => AreaModel::FLAG_VALID])->orderBy('sort')->one();
        verify($this->areaComp->firstArea->attributes)->equals($area->attributes);
    }

    /**
     * fetchAreaByDir()のtest
     */
    public function testFetchAreaByDir()
    {
        /* @var $area AreaModel */
        $area = AreaModel::find()->where(['valid_chk' => AreaModel::FLAG_VALID])->one();

        verify($this->areaComp->fetchAreaByDir($area->area_dir)->attributes)->equals($area->attributes);
    }

    /**
     * ワンエリア状態に変える
     * @return AreaModel
     */
    private function changeToOneArea()
    {
        AreaModel::updateAll(['valid_chk' => AreaModel::FLAG_INVALID]);
        /* @var $areaModel AreaModel */
        $areaModel = AreaModel::find()->one();
        $areaModel->valid_chk = 1;
        $areaModel->save(false);
        return $areaModel;
    }
}
