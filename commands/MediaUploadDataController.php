<?php
namespace app\commands;

use app\models\manage\AdminMaster;
use app\models\manage\MediaUpload;
use proseeds\models\Tenant;
use Yii;
use yii\console\Controller;
use yii\console\Exception;

/**
 * Class DataController
 * @package app\commands
 */
class MediaUploadDataController extends Controller
{
    private $_mediaUploadId = 1;

    private $_rows;

    /**
     * @throws Exception
     */
    public function actionIndex()
    {
        echo "\nmedia_uploadをinsertします\n";
        MediaUpload::deleteAll();
        $tenants = Tenant::find()->all();
        foreach ($tenants as $tenant) {
            /** @var Tenant $tenant */
            // 掲載企業レコード挿入
            $this->_rows=[];
            $clients = AdminMaster::find()
                ->select(['id', 'tenant_id', 'client_master_id'])
                ->where([
                    'and',
                    ['not', ['client_master_id' => null]],
                    ['tenant_id' => $tenant->tenant_id],
                ])->groupBy('client_master_id')->asArray()->all();

            if (!$clients) {
                throw new Exception("tenant{$tenant->tenant_id} has no client admins");
            }

            foreach ($clients as $clientAdmin) {
                for ($i = 1; $i <= 5; $i++) {
                    $this->makeMediaUploadRow($clientAdmin);
                }
            }
            Yii::$app->db->createCommand()->batchInsert(MediaUpload::tableName(), MediaUpload::getTableSchema()->columnNames, $this->_rows)->execute();
            echo "tenant_id={$tenant->tenant_id}のinsert完了\n";
        }

        // 運営元レコード挿入
        $this->_rows = [];
        foreach ($tenants as $tenant) {
            $admin = AdminMaster::find()
                ->select(['id', 'tenant_id', 'client_master_id'])
                ->where(['tenant_id' => $tenant->tenant_id, 'client_master_id' => null])
                ->asArray()->one();
            if (!$admin) {
                throw new Exception("tenant{$tenant->tenant_id} has no owner admins");
            }
            for ($i = 1; $i <= 30; $i++) {
                $this->makeMediaUploadRow($admin);
            }
        }
        Yii::$app->db->createCommand()->batchInsert(MediaUpload::tableName(), MediaUpload::getTableSchema()->columnNames, $this->_rows)->execute();
    }

    /**
     * @param $admin
     */
    private function makeMediaUploadRow($admin)
    {
        $this->_rows[] = [
            $this->_mediaUploadId, // id
            $admin['tenant_id'], // tenant_id
            Yii::$app->security->generateRandomString(10) . $this->_mediaUploadId . '.jpg', // save_file_name
            time(), // updated_at
            $admin['id'], // admin_master_id
            $admin['client_master_id'], // client_master_id
            rand(), // file_size
            time(), // created_at
            Yii::$app->security->generateRandomString(3) . $this->_mediaUploadId . '.jpg', // disp_file_name
        ];
        $this->_mediaUploadId++;
    }
}
