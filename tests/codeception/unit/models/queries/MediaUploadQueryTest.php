<?php
namespace models\queries;

use app\models\manage\ClientMaster;
use app\models\manage\MediaUpload;
use app\models\queries\MediaUploadQuery;
use tests\codeception\unit\JmTestCase;
use yii\helpers\ArrayHelper;

class MediaUploadQueryTest extends JmTestCase
{
    public $roles = [
        'owner_admin',
        'corp_admin',
        'client_admin'
    ];

    /**
     * @return MediaUploadQuery
     */
    private function getQuery()
    {
        return new MediaUploadQuery(MediaUpload::className());
    }

    /**
     * client_master_idを元にレコードをsaveする
     * @param $clientIds
     */
    private function saveRecords($clientIds)
    {
        foreach ($clientIds as $clientId) {
            $model = new MediaUpload();
            $model->client_master_id = $clientId;
            $model->save_file_name = 'save' . $clientId;
            $model->disp_file_name = 'disp' . $clientId;
            $model->save(false);
        }
    }

    /**
     * filterByClientのtest
     * setIdentity用のcorpとclientが結びついていることが前提
     */
    public function testFilterByClient()
    {
        $this->setIdentity('client_admin');
        $client1Id = $this->getIdentity()->clientMaster->id;
        $client2Id = ClientMaster::find()->where(['and',
            ['corp_master_id' => $this->getIdentity()->corp_master_id],
            ['not', ['id' => $this->getIdentity()->client_master_id]],
        ])->one()->id;
        $client3Id = ClientMaster::find()->where(['not', ['corp_master_id' => $this->getIdentity()->corp_master_id]])->one()->id;

        $this->saveRecords([$client1Id, $client2Id, $client3Id]);

        // 掲載企業権限
        $this->checkClientMasterId($client1Id, false);
        $this->checkClientMasterId($client2Id, true);
        $this->checkClientMasterId($client3Id, true);

        // 代理店権限
        $this->setIdentity('corp_admin');
        $this->checkClientMasterId($client1Id, false);
        $this->checkClientMasterId($client2Id, false);
        $this->checkClientMasterId($client3Id, true);

        // 運営元権限
        $this->setIdentity('owner_admin');
        $this->checkClientMasterId($client1Id, false);
        $this->checkClientMasterId($client2Id, false);
        $this->checkClientMasterId($client3Id, false);

        self::getFixtureInstance('media_upload')->load();
    }

    /**
     * 権限越えしていたら、運営元画像だけ取得されているか確認する
     * 権限越えしていなければ、運営元画像と指定された画像のみ取得されているか確認する
     * @param int  $clientId 掲載企業ID
     * @param bool $overAuth 権限越えしている→true　していない→false
     */
    private function checkClientMasterId($clientId, $overAuth)
    {
        /** @var MediaUpload[] $models */
        $models = $this->getQuery()->filterByClient($clientId)->all();
        if ($overAuth) {
            verify($models)->notEmpty();
            $count = MediaUpload::find()->where(['client_master_id' => null])->count();
            verify($models)->count((int)$count);
            foreach ($models as $pic) {
                verify($pic->client_master_id)->null();
            }
        } else {
            $indexedModels = ArrayHelper::index($models, null, 'client_master_id');
            verify($indexedModels[null])->notEmpty();
            $count = MediaUpload::find()->where(['client_master_id' => null])->count();
            verify($indexedModels[null])->count((int)$count);
            verify($indexedModels[$clientId])->notEmpty();
            $count = MediaUpload::find()->where(['client_master_id' => $clientId])->count();
            verify($indexedModels[$clientId])->count((int)$count);
            verify($indexedModels)->count(2);
        }
    }

    /**
     * addAuthQueryのtest
     * 権限用の各管理者の画像レコードが最低一つあることが前提
     */
    public function testAddAuthQuery()
    {
        /** @var MediaUpload[] $models */
        $this->setIdentity('owner_admin');
        $models = $this->getQuery()->addAuthQuery()->all();
        verify($models)->notEmpty();
        $count = MediaUpload::find()->count();
        verify($models)->count((int)$count);

        $this->setIdentity('corp_admin');
        $models = $this->getQuery()->addAuthQuery()->all();
        verify($models)->notEmpty();
        foreach ($models as $model) {
            verify($model->clientMaster->corp_master_id)->equals($this->getIdentity()->corp_master_id);
        }

        $this->setIdentity('client_admin');
        $models = $this->getQuery()->addAuthQuery()->all();
        verify($models)->notEmpty();
        foreach ($models as $model) {
            verify($model->client_master_id)->equals($this->getIdentity()->client_master_id);
        }
    }

    /**
     * addTagAuthQueryのtest
     */
    public function testAddTagAuthQuery()
    {
        /** @var MediaUpload[] $models */
        $this->setIdentity('owner_admin');
        $models = $this->getQuery()->addTagAuthQuery()->all();
        verify($models)->notEmpty();
        $count = MediaUpload::find()->count();
        verify($models)->count((int)$count);

        $this->setIdentity('corp_admin');
        $models = $this->getQuery()->addTagAuthQuery()->all();
        verify($models)->isEmpty();

        $this->setIdentity('client_admin');
        $models = $this->getQuery()->addTagAuthQuery()->all();
        verify($models)->notEmpty();
        foreach ($models as $model) {
            $result = ($model->client_master_id == null || $model->client_master_id == $this->getIdentity()->client_master_id);
            verify($result)->true();
        }
    }
}