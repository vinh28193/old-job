<?php
namespace app\commands;

use app\models\manage\AdminMaster;
use app\models\manage\ClientMaster;
use app\models\manage\CorpMaster;
use yii;

/**
 * Class DataController
 * @package app\commands
 */
class AdminDataController extends BaseDataController
{
    protected $tableName = 'admin_master';
    private $_clientArray;

    /**
     * 掲載企業を登録する
     * 半分は有効状態で有効な代理店に紐づけられる
     * もう半分は無効状態で無効な代理店に紐づけられる
     * @param $count
     * @return mixed|void
     */
    protected function insert($count)
    {
        //カラム情報取得
        $adminColumnNames = AdminMaster::getTableSchema()->columnNames;

        // 作業id取得
        $this->currentId['admin_master'] = AdminMaster::find()->max('id') + 1;
        // これから登録するclientに紐づいている中間テーブルレコードを削除
        Yii::$app->db->createCommand()->delete('auth_assignment', ['>=', 'user_id', $this->currentId['admin_master']])->execute();

        // 有効データ作成
        $this->makeValues($count);
        // 有効データ挿入
        Yii::$app->db->createCommand()->batchInsert(AdminMaster::tableName(), $adminColumnNames, $this->rows['admin_master'])->execute();
        Yii::$app->db->createCommand()->batchInsert('auth_assignment', ['item_name', 'user_id', 'created_at'], $this->rows['auth_assignment'])->execute();
    }

    /**
     * 有効もしくは無効な掲載企業データを作成する
     * @param $count
     * @return array
     */
    private function makeValues($count)
    {
        // rowsを初期化
        $this->rows = [];
        // データ生成で使うidをキャッシュ
        $this->_clientArray = ClientMaster::find()->select(['id', 'corp_master_id'])->where(['tenant_id' => $this->tenantId])->asArray()->all();
        $this->ids['corp_master'] = CorpMaster::find()->select('id')->where(['tenant_id' => $this->tenantId])->column();

        // データを生成してセット
        for ($i = 1; $i <= $count; $i++) {
            $this->makeAdminMasterRow('client_admin');
            $this->makeAuthAssignmentRow('client_admin');
            $this->currentId['admin_master']++;
        }

        for ($i = 1; $i <= 40; $i++) {
            $this->makeAdminMasterRow('corp_admin');
            $this->makeAuthAssignmentRow('corp_admin');
            $this->currentId['admin_master']++;
        }

        for ($i = 1; $i <= 20; $i++) {
            $this->makeAdminMasterRow('owner_admin');
            $this->makeAuthAssignmentRow('owner_admin');
            $this->currentId['admin_master']++;
        }
    }

    /**
     * 有効もしくは無効なclient_masterのrowを作成してセットする
     * @param $authName
     * @return array
     * @throws yii\console\Exception
     */
    private function makeAdminMasterRow($authName)
    {
        $this->currentTable = 'admin_master';
        switch ($authName) {
            case 'owner_admin':
                $clientId = null;
                $corpId = null;
                break;
            case 'corp_admin':
                $clientId = null;
                $corpId = $this->incId('corp_master');
                break;
            case 'client_admin':
                $client = $this->inc($this->_clientArray, $this->id());
                $clientId = $client['id'];
                $corpId = $client['corp_master_id'];
                break;
            default:
                throw new yii\console\Exception("auth name {$authName} is invalid");
                break;
        }

        $this->rows['admin_master'][] = [
            $this->id(), // id
            $this->tenantId, // tenant_id
            $this->id(), // admin_no
            $corpId, // corp_master_id
            Yii::$app->security->generateRandomString(10), // login_id
            Yii::$app->security->generateRandomString(10), // password
            $this->timeStamp(), // created_at
            1, // valid_chk
            $this->data('name_sei'),
            $this->data('name_mei'),
            $this->telNo(), // tel_no
            $clientId, // client_master_id
            $this->mail(), // mail_address
            // 殆ど使用されていないため、空文字を追加する。
            '', // option100
            '', // option101
            '', // option102
            '', // option103
            '', // option104
            '', // option105
            '', // option106
            '', // option107
            '', // option108
            '', // option109
        ];
    }

    private function makeAuthAssignmentRow($itemName)
    {
        $this->currentTable = 'auth_assignment';
        $this->rows['auth_assignment'][] = [
            $itemName,
            $this->currentId['admin_master'],
            $this->timeStamp(),
        ];
    }
}
