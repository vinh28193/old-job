<?php
namespace app\commands;

use app\models\manage\ClientCharge;
use app\models\manage\ClientChargePlan;
use app\models\manage\ClientMaster;
use app\models\manage\CorpMaster;
use yii;

/**
 * Class DataController
 * @package app\commands
 */
class ClientDataController extends BaseDataController
{
    protected $tableName = 'client_master';
    /**
     * 掲載企業を登録する
     * 半分は有効状態で有効な代理店に紐づけられる
     * もう半分は無効状態で有効もしくは無効な代理店に紐づけられる
     * @param $count
     * @return mixed|void
     */
    protected function insert($count)
    {
        //カラム情報取得
        $clientColumnNames = ClientMaster::getTableSchema()->columnNames;
        $clientChargeColumnNames = ClientCharge::getTableSchema()->columnNames;

        // 作業id取得
        $this->currentId['client_master'] = ClientMaster::find()->max('id') + 1;
        $this->currentId['client_charge'] = ClientCharge::find()->max('id') + 1;
        // これから登録するclientに紐づいている中間テーブルレコードを削除
        ClientCharge::deleteAll(['>=', 'client_master_id', $this->currentId['client_master']]);

        // 有効と無効半分ずつに分ける
        $halfCount = $count/2;

        // 有効データ作成
        $this->valid = 1;
        $this->makeValues($halfCount);
        // 有効データ挿入
        Yii::$app->db->createCommand()->batchInsert(ClientMaster::tableName(), $clientColumnNames, $this->rows['client_master'])->execute();
        Yii::$app->db->createCommand()->batchInsert(ClientCharge::tableName(), $clientChargeColumnNames, $this->rows['client_charge'])->execute();

        // 無効データ作成
        $this->valid = 0;
        $this->makeValues($halfCount);
        // 無効データ挿入
        Yii::$app->db->createCommand()->batchInsert(ClientMaster::tableName(), $clientColumnNames, $this->rows['client_master'])->execute();
        Yii::$app->db->createCommand()->batchInsert(ClientCharge::tableName(), $clientChargeColumnNames, $this->rows['client_charge'])->execute();
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
        $condition = ['tenant_id' => $this->tenantId];
        if ($this->valid) {
            $condition = array_merge($condition, ['valid_chk' => $this->valid]);
        }
        $this->ids['corp_master'] = CorpMaster::find()->select('id')->where($condition)->column();
        $this->ids['client_charge_plan'] = ClientChargePlan::find()->where(['tenant_id' => $this->tenantId])->select('id')->column();

        // データを生成してセット
        for ($i = 1; $i <= $count; $i++) {
            $this->makeClientMasterRow();
            $this->makeClientChargeRow();
            $this->currentId['client_master']++;
        }
    }

    /**
     * 有効もしくは無効なclient_masterのrowを作成してセットする
     * @return array
     */
    private function makeClientMasterRow()
    {
        $this->currentTable = 'client_master';
        $this->rows['client_master'][] = [
            $this->id(), // id
            $this->tenantId, // tenant_id
            $this->id(), // client_no
            $this->incId('corp_master'), // corp_master_id
            $this->data('client_name'),
            $this->data('client_name_kana'),
            $this->telNo(), // tell_no
            $this->data('address'),
            $this->data('tanto_name'),
            $this->timeStamp(), // created_at
            $this->valid, // valid_chk
            $this->data('client_business_outline'),
            $this->data('client_corporate_url'),
            '', // admin_memo 実績がないため空にしている。
            $this->data('option100'),
            $this->data('option101'),
            $this->data('option102'),
            $this->data('option103'),
            $this->data('option104'),
            $this->data('option105'),
            $this->data('option106'),
            $this->data('option107'),
            $this->data('option108'),
            $this->data('option109'),
        ];
    }

    /**
     * プランと掲載企業の紐づけを行う
     * @return array
     */
    private function makeClientChargeRow()
    {
        $this->currentTable = 'client_charge';
        $this->rows['client_charge'][] = [
            $this->id, // id
            $this->tenantId, // tenant_id
            $this->incId('client_charge_plan'), // client_charge_plan_id
            $this->currentId['client_master'], // client_master_id
            rand(1, 255),
        ];
        $this->currentId['client_charge']++;
    }

}
