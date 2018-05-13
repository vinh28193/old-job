<?php
namespace app\commands;

use app\models\manage\searchkey\Dist;
use app\models\manage\searchkey\Pref;
use app\models\manage\searchkey\PrefDist;
use app\models\manage\searchkey\PrefDistMaster;
use tests\codeception\fixtures\AreaFixture;
use tests\codeception\fixtures\PrefFixture;
use yii;
use yii\console\Controller;

/**
 * Class DataController
 * @package app\commands
 */
class PrefDistDataController extends Controller
{
    /**
     * @return mixed|void
     */
    public function actionIndex()
    {
        $prefFixture = new PrefFixture();
        $prefFixture->load();
        $areaFixture = new AreaFixture();
        $areaFixture->load();

        PrefDist::deleteAll();
        PrefDistMaster::deleteAll();
        $prefectures = yii\helpers\ArrayHelper::index(Pref::find()->with('dist')->all(), null, 'tenant_id');
        $prefDistMasterId = 1;
        foreach ($prefectures as $tenantId => $tenantPrefectures) {
            $prefDistMasterRows = [];
            $prefDistRows = [];
            $no = 1;
            foreach ($tenantPrefectures as $pref) {
                $sort = 1;
                foreach ($pref->dist as $dist) {
                    /** @var Dist $dist */
                    $prefDistMasterRows[] = [
                        $prefDistMasterId, // id
                        $tenantId,         // tenant_id
                        $pref->id,         // pref_id
                        $dist->dist_name,  // pref_dist_name
                        1,                 // valid_chk
                        $sort,             // sort
                        $no,               // pref_dist_master_no
                    ];
                    $prefDistRows[] = [
                        $prefDistMasterId, // id
                        $tenantId,         // tenant_id
                        $prefDistMasterId, // pref_dist_master_id
                        $dist->dist_cd,    // dist_id
                    ];
                    $prefDistMasterId++;
                    $sort++;
                    $no++;
                }
            }
            Yii::$app->db->createCommand()->batchInsert(PrefDistMaster::tableName(), PrefDistMaster::getTableSchema()->columnNames, $prefDistMasterRows)->execute();
            Yii::$app->db->createCommand()->batchInsert(PrefDist::tableName(), PrefDist::getTableSchema()->columnNames, $prefDistRows)->execute();
            echo "tenant{$tenantId}の地域グループを設定しました\n";
        }
    }
}
