<?php
namespace models\manage;

use app\models\manage\ClientMaster;
use yii;
use app\modules\manage\models\Manager;
use app\models\manage\MediaUpload;
use tests\codeception\unit\JmTestCase;

/**
 * Class MediaUploadTest
 * @package models\manage
 */
class MediaUploadTest extends JmTestCase
{
    // initは非常に単純なメソッドなので省略

    /**
     * ファイル名重複validation以外のrulesのテスト
     */
    public function testRules()
    {
        $this->setIdentity('owner_admin');
        $this->specify('stringチェック', function () {
            $model = new MediaUpload();
            $model->load([
                $model->formName() => [
                    'disp_file_name' => 123,
                    'tag' => 123,
                ],
            ]);
            $model->validate();
            verify($model->hasErrors('disp_file_name'))->true();
            verify($model->hasErrors('tag'))->true();
        });

        $this->specify('最大文字数チェック', function () {
            $model = new MediaUpload();
            $model->load([
                $model->formName() => [
                    'disp_file_name' => str_repeat('a', 201),
                    'tag' => str_repeat('a', 51),
                ],
            ]);
            $model->validate();
            verify($model->hasErrors('disp_file_name'))->true();
            verify($model->hasErrors('tag'))->true();
        });

        $this->specify('tagチェック', function () {
            $model = new MediaUpload();
            $model->load([
                $model->formName() => [
                    'tag' => '0',
                ],
            ]);
            $model->validate();
            verify($model->hasErrors('tag'))->true();
        });

        $this->specify('新規登録時必須チェック', function () {
            $model = new MediaUpload();
            $model->validate();
            verify($model->hasErrors('imageFile'))->true();
        });

        $this->specify('更新時必須チェック', function () {
            $model = MediaUpload::find()->one();
            $model->validate();
            verify($model->hasErrors('imageFile'))->false();
        });
    }

    /**
     * disp_file_nameの重複validationテスト
     */
    public function testRulesOfUniqueName()
    {
        $client1 = ClientMaster::findOne(['corp_master_id' => $this->id(1, 'corp_master')]);
        $client2 = ClientMaster::findOne(['corp_master_id' => $this->id(2, 'corp_master')]);
        $owner = 'owner';

        $this->loadFilePost('MediaUpload', 'imageFile');

        // 同一掲載企業や運営元同士はユニークチェックがかかる
        $model = $this->prepareUniqueTest($client1, $client1);
        verify($model->hasErrors('disp_file_name'))->true();
        self::getFixtureInstance('media_upload')->initTable();

        $model = $this->prepareUniqueTest($client2, $client2);
        verify($model->hasErrors('disp_file_name'))->true();
        self::getFixtureInstance('media_upload')->initTable();

        $model = $this->prepareUniqueTest($owner, $owner);
        verify($model->hasErrors('disp_file_name'))->true();
        self::getFixtureInstance('media_upload')->initTable();

        // 運営元と掲載企業の重複は許さない
        $model = $this->prepareUniqueTest($owner, $client1);
        verify($model->hasErrors('disp_file_name'))->true();
        self::getFixtureInstance('media_upload')->initTable();

        $model = $this->prepareUniqueTest($client1, $owner);
        verify($model->hasErrors('disp_file_name'))->true();
        self::getFixtureInstance('media_upload')->initTable();

        // 別の掲載企業同士の重複チェックはかからない
        $model = $this->prepareUniqueTest($client1, $client2);
        verify($model->hasErrors('disp_file_name'))->false();
        self::getFixtureInstance('media_upload')->initTable();

        // 同じファイル名でも自分自身の更新はできる
        $this->setManager($client1);
        $model = $this->saveModel();
        $model->validate();
        verify($model->hasErrors('disp_file_name'))->false();
        self::getFixtureInstance('media_upload')->initTable();

        $this->setManager($client1);
        $model = $this->saveModel();
        $model->validate();
        verify($model->hasErrors('disp_file_name'))->false();
        self::getFixtureInstance('media_upload')->initTable();

        // 登録した掲載企業管理者をリセット
        self::getFixtureInstance('admin_master')->initTable();
        self::getFixtureInstance('auth_assignment')->initTable();
    }

    /**
     * 重複validation用の状況を準備し、検証用モデルを返す
     * @param $exist
     * @param $new
     * @return MediaUpload
     */
    private function prepareUniqueTest($exist, $new)
    {
        // $existの権限でテスト用レコードをsave
        $this->setManager($exist);
        $this->saveModel();
        // $newの権限で上と同じ内容をloadしてvalidate
        $this->setManager($new);
        $model = new MediaUpload();
        $model->loadFileInfo();
        $model->validate();
        return $model;
    }

    /**
     * ClientMasterを元に掲載企業管理者を作ってidentityにセットする
     * もしくは、運営元管理者をセットする
     * @param $clientMaster
     */
    private function setManager($clientMaster)
    {
        if ($clientMaster instanceof ClientMaster) {
            $manager = new Manager(['role' => 'client_admin']);
            $manager->client_master_id = $clientMaster->id;
            $manager->corp_master_id = $clientMaster->corp_master_id;
            $manager->login_id = 'unitTest';
            $manager->password = 'unitTest';
            $manager->tel_no = '123';
            $manager->save(false);
            $manager->saveAuthExceptions([]);
            Yii::$app->user->identity = $manager;
        } else {
            $this->setIdentity('owner_admin');
        }
    }

    /**
     * 検証用モデルをsaveする
     * @return MediaUpload
     */
    private function saveModel()
    {
        $model = new MediaUpload();
        $model->loadFileInfo();
        $model->save(false);
        return $model;
    }

    /**
     * loadFileInfoのtest
     */
    public function testLoadFileInfo()
    {
        $this->setIdentity('owner_admin');

        $this->specify('uploadで画像データが無い時', function () {
            /** @var MediaUpload $model */
            $model = MediaUpload::find()->one();
            $model->loadFileInfo();
            foreach ($model->attributes as $name => $value) {
                verify($value)->equals($model->getOldAttribute($name));
            }
        });

        $this->specify('uploadで画像データがある時', function () {
            /** @var MediaUpload $model */
            $model = MediaUpload::find()->where(['not', ['admin_master_id' => $this->getIdentity()->id]])->one();
            $this->loadFilePost($model->formName(), 'imageFile');
            $model->loadFileInfo();
            verify($model->imageFile)->true();
            verify($model->admin_master_id)->equals($this->getIdentity()->id);
            verify($model->file_size)->equals(512);
            verify($model->save_file_name)->notEmpty();
            verify($model->save_file_name)->notEquals($model->getOldAttribute('save_file_name'));
            verify($model->disp_file_name)->equals('test.png');
        });

        $this->specify('owner権限で新規登録した場合', function () {
            $model = new MediaUpload();
            $this->loadFilePost($model->formName(), 'imageFile');
            $model->loadFileInfo();
            verify($model->imageFile)->true();
            verify($model->admin_master_id)->equals($this->getIdentity()->id);
            verify($model->file_size)->equals(512);
            verify($model->save_file_name)->notEmpty();
            verify($model->save_file_name)->notEquals($model->getOldAttribute('save_file_name'));
            verify($model->disp_file_name)->equals('test.png');
            verify($model->client_master_id)->null();
        });

        $this->specify('client権限で新規登録した場合', function () {
            $this->setIdentity('client_admin');
            $model = new MediaUpload();
            $this->loadFilePost($model->formName(), 'imageFile');
            $model->loadFileInfo();
            verify($model->client_master_id)->notNull();
            verify($model->client_master_id)->equals($this->getIdentity()->client_master_id);
        });
    }

    /**
     * errorMessagesのテスト
     */
    public function testErrorMessages()
    {
        $model = new MediaUpload();
        $model->addErrors([
            'imageFile' => 'ファイルが無いです',
            'file_size' => 'ファイルが大きすぎます',
            'tag' => 'このタグは使えません',
        ]);
        verify($model->errorMessages())->equals([
            'ファイルが無いです',
            'ファイルが大きすぎます',
            'このタグは使えません',
        ]);
    }
}
