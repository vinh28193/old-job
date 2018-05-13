<?php
namespace models\manage;

use app\models\manage\MediaUpload;
use Yii;
use app\models\manage\JobPic;
use app\models\manage\ClientMaster;
use tests\codeception\unit\JmTestCase;

/**
 * Class JobPicTest
 * @package models\manage
 */
class JobPicTest extends JmTestCase
{
    /**
     * rulesのtest
     * validateClientIdもここでテストしている
     */
    public function testRules()
    {
        self::getFixtureInstance('media_upload')->initTable();
        $this->setIdentity('owner_admin');

        $this->specify('必須チェック（全権限共通）', function () {
            $model = new JobPic();
            $model->client_master_id = null;
            $model->validate();
            verify($model->hasErrors('client_master_id'))->true();
        });

        $this->specify('数字チェック（全権限共通）', function () {
            $model = new JobPic();
            $model->client_master_id = '文字列';
            $model->validate();
            verify($model->hasErrors('client_master_id'))->true();
        });

        $this->specify('client_master_id - 運営元権限', function () {
            $model = new JobPic();
            $model->client_master_id = 123456;
            $model->validate();
            verify($model->hasErrors('client_master_id'))->false();
        });

        $this->specify('client_master_id - 代理店権限', function () {
            $this->setIdentity('corp_admin');
            // 権限越え
            $model = new JobPic();
            $model->client_master_id = 123456;
            $model->validate();
            verify($model->hasErrors('client_master_id'))->true();
            // 権限内
            $model = new JobPic();
            $model->client_master_id = $this->getIdentity()->corpMaster->clientMaster[0]->id;
            $model->validate();
            verify($model->hasErrors('client_master_id'))->false();
        });

        $this->specify('client_master_id - 掲載企業権限', function () {
            $this->setIdentity('client_admin');
            // 掲載企業権限（権限越え）
            $model = new JobPic();
            $model->client_master_id = 123456;
            $model->validate();
            verify($model->hasErrors('client_master_id'))->true();
            // 掲載企業権限（権限内）
            $model = new JobPic();
            $model->client_master_id = $this->getIdentity()->client_master_id;
            $model->validate();
            verify($model->hasErrors('client_master_id'))->false();
        });
    }

    /**
     * client_master_idを元にレコードをsaveする
     * @param $clientIds
     */
    private function saveRecords($clientIds)
    {
        foreach ($clientIds as $clientId) {
            $model = new JobPic();
            $model->client_master_id = $clientId;
            $model->save_file_name = 'save' . $clientId;
            $model->disp_file_name = 'disp' . $clientId;
            $model->save(false);
        }
    }

    /**
     * getClientPicsのtest
     */
    public function testGetClientPics()
    {
        self::getFixtureInstance('media_upload')->initTable();
        $this->setIdentity('client_admin');
        $clientId1 = $this->getIdentity()->clientMaster->id;
        $clientId2 = ClientMaster::find()->where([
            'and',
            ['corp_master_id' => $this->getIdentity()->corp_master_id],
            ['not', ['id' => $clientId1]],
        ])->one()->id;
        $clientId3 = ClientMaster::find()->where([
            'not',
            ['corp_master_id' => $this->getIdentity()->corp_master_id],
        ])->one()->id;

        // それぞれの掲載企業に最低一つはレコードがある状態にしておく
        $this->saveRecords([$clientId1, $clientId2, $clientId3]);

        //client1はclient1の画像を取得できる
        $this->checkGetClientPics($clientId1);
        //client1はclient2の画像を取得できない
        $this->checkGetClientPics($clientId2, false);
        //client1はclient3の画像を取得できない
        $this->checkGetClientPics($clientId3, false);

        $this->setIdentity('corp_admin');
        //corp1はclient1の画像を取得できる
        $this->checkGetClientPics($clientId1);
        //corp1はclient2の画像を取得できる
        $this->checkGetClientPics($clientId2);
        //corp1はclient3の画像を取得できない
        $this->checkGetClientPics($clientId3, false);

        //owner_adminは全ての画像を取得できる
        $this->setIdentity('owner_admin');
        $this->checkGetClientPics($clientId1);
        $this->checkGetClientPics($clientId2);
        $this->checkGetClientPics($clientId3);

        self::getFixtureInstance('media_upload')->initTable();
    }

    /**
     * clientの画像が取得できているかどうかを検証する
     * 権限越えしていたら何も取得できないことを検証する
     * @param int $clientId
     * @param bool $equalsEmpty
     */
    private function checkGetClientPics($clientId, $equalsEmpty = true)
    {
        $model = new JobPic(['client_master_id' => $clientId]);
        if ($equalsEmpty) {
            $pics = $model->clientPics;
            verify($pics)->notEmpty();
            $count = MediaUpload::find()->where(['client_master_id' => $clientId])->count();
            verify($pics)->count((int)$count);
            foreach ($pics as $pic) {
                verify($pic->client_master_id)->equals($clientId);
            }
        } else {
            verify($model->clientPics)->isEmpty();
        }
    }

    /**
     * getOwnerPicのtest
     */
    public function testGetOwnerPics()
    {
        $count = MediaUpload::find()->where(['client_master_id' => null])->count();

        foreach (['owner_admin', 'corp_admin', 'client_admin'] as $role) {
            $this->setIdentity($role);
            $model = new JobPic();
            $model->client_master_id = 123456;
            $pics = $model->ownerPics;
            verify($pics)->notEmpty();
            verify($pics)->count((int)$count);
            foreach ($pics as $pic) {
                verify($pic->client_master_id)->null();
            }
        }
    }

    /**
     * makeTagDropDownSelectionsのtest
     */
    public function testMakeTagDropDownSelections()
    {
        $models = [
            new JobPic(['tag' => 'tag4']),
            new JobPic(['tag' => 'tag4']),
            new JobPic(['tag' => '']),
            new JobPic(['tag' => 'tag2']),
            new JobPic(['tag' => 'tag2']),
            new JobPic(['tag' => null]),
            new JobPic(['tag' => 'tag1']),
            new JobPic(['tag' => 'tag1']),
            new JobPic(['tag' => 0]),
            new JobPic(['tag' => 'tag3']),
            new JobPic(['tag' => 'tag3']),
        ];
        $array = [];
        foreach ($models as $model) {
            if ($model->tag != '') {
                $array[$model->tag] = $model->tag;
            }
        }
        ksort($array);
        $array = array_merge([
            '' => Yii::t('app', 'すべて'),
            0 => Yii::t('app', 'タグ無し'),
        ], $array);
        verify(JobPic::makeTagDropDownSelections($models))->equals($array);
    }

    /**
     * loadFileInfoの拡張のtest
     */
    public function testLoadFileInfo()
    {
        $this->setIdentity('owner_admin');
        $model = new JobPic();
        $this->loadFilePost($model->formName(), 'imageFile');
        $model->loadFileInfo();
        verify($model->disp_file_name)->notEquals('test.png');
        verify($model->disp_file_name)->equals($model->save_file_name);
    }
}
