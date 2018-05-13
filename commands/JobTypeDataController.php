<?php
namespace app\commands;

use app\models\manage\searchkey\JobTypeBig;
use app\models\manage\searchkey\JobTypeCategory;
use app\models\manage\searchkey\JobTypeSmall;
use Yii;
use yii\console\Controller;

/**
 * Class DataController
 * @package app\commands
 */
class JobTypeDataController extends Controller
{
    /**
     * @param $tenant
     * @return mixed|void
     */
    public function actionIndex($tenant)
    {
        JobTypeCategory::deleteAll();
        JobTypeBig::deleteAll();
        JobTypeSmall::deleteAll();

        $cateValues = [];
        $bigValues = [];
        $itemValues = [];
        $cateId = 1;
        $bigId = 1;
        $itemId = 1;
        for ($tenantId = 1; $tenantId <= $tenant; $tenantId++) {
            $bigNo = 1;
            $itemNo = 1;
            for ($cateNo = 1; $cateNo <= 3; $cateNo++) {
                $cateValues[] = [
                    $cateId,                              // id
                    $tenantId,                            // tenant_id
                    $cateNo,                              // job_type_category_cd
                    "{$tenantId}職種大カテゴリ{$cateNo}", // name
                    $cateNo,                              // sort
                    1,                                    // valid_chk
                ];
                for ($i = 1; $i <= 10; $i++) {
                    $bigValues[] = [
                        $bigId,                              // id
                        $tenantId,                           // tenant_id
                        "{$tenantId}職種小カテゴリ{$bigNo}", // job_type_big_name
                        1,                                   // valid_chk
                        $i,                                  // sort
                        $cateId,                             // job_type_category_id
                        $bigNo,                              // job_type_big_no
                    ];
                    for ($ii = 1; $ii <= 10; $ii++) {
                        $itemValues[] = [
                            $itemId,                        // id
                            $tenantId,                      // tenant_id
                            "{$tenantId}職種項目{$itemNo}", // job_type_small_name
                            $bigId,                         // job_type_big_id
                            1,                              // valid_chk
                            $ii,                            // sort
                            $itemNo,                        // job_type_big_no
                        ];
                        $itemId++;
                        $itemNo++;
                    }
                    $bigId++;
                    $bigNo++;
                }
                $cateId++;
            }
        }

        Yii::$app->db->createCommand()->batchInsert(JobTypeCategory::tableName(), JobTypeCategory::getTableSchema()->columnNames, $cateValues)->execute();
        Yii::$app->db->createCommand()->batchInsert(JobTypeBig::tableName(), JobTypeBig::getTableSchema()->columnNames, $bigValues)->execute();
        Yii::$app->db->createCommand()->batchInsert(JobTypeSmall::tableName(), JobTypeSmall::getTableSchema()->columnNames, $itemValues)->execute();
    }
}
