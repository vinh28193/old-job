<?php
/**
 * Created by PhpStorm.
 * User: proseeds
 * Date: 2017/02/13
 * Time: 9:33
 */

namespace app\models\queries;


use app\models\manage\ClientMaster;
use app\models\manage\MediaUpload;
use app\modules\manage\models\Manager;
use Yii;
use yii\base\Exception;
use yii\db\ActiveQuery;

class MediaUploadQuery extends ActiveQuery
{
    /**
     * 自分の画像と運営元の画像を合わせて取得する
     * @param $clientMasterId
     * @return $this
     */
    public function filterByClient($clientMasterId)
    {
        return $this->andWhere([MediaUpload::tableName() . '.client_master_id' => $clientMasterId])
            ->addAuthQuery()
            ->orWhere([MediaUpload::tableName() . '.client_master_id' => null]);
    }

    /**
     * where条件に権限の制限を付与する
     * @return $this
     * @throws Exception
     */
    public function addAuthQuery()
    {
        /** @var Manager $identity */
        $identity = Yii::$app->user->identity;
        switch ($identity->myRole) {
            case Manager::OWNER_ADMIN:
                return $this;
                break;
            case Manager::CORP_ADMIN:
                return $this->joinWith('clientMaster')
                    ->andWhere([ClientMaster::tableName() . '.corp_master_id' => $identity->corp_master_id]);
                break;
            case Manager::CLIENT_ADMIN:
                return $this->andWhere([MediaUpload::tableName() . '.client_master_id' => $identity->client_master_id]);
                break;
            default :
                throw new Exception("{$identity->myRole} is not a valid role");
                break;
        }
    }

    /**
     * 一覧画面検索タグ取得用
     * 運営元→全部のタグ
     * 代理店→メディアアップロード機能は使用不可
     * 掲載企業→自分のタグと運営元のタグ
     * @return $this
     * @throws Exception
     */
    public function addTagAuthQuery()
    {
        /** @var Manager $identity */
        $identity = Yii::$app->user->identity;
        switch ($identity->myRole) {
            case Manager::OWNER_ADMIN:
                return $this;
                break;
            case Manager::CORP_ADMIN:
                return $this->where('0=1');
                break;
            case Manager::CLIENT_ADMIN:
                return $this->filterByClient($identity->client_master_id);
                break;
            default :
                throw new Exception("{$identity->myRole} is not a valid role");
                break;
        }
    }
}