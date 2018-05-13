<?php
namespace models\manage;

use yii;
use tests\codeception\unit\JmTestCase;
use app\models\manage\MediaUpload;
use app\models\manage\MediaUploadSearch;
use yii\helpers\ArrayHelper;

/**
 * Class MediaUploadSearchTest
 * @package models\manage
 */
class MediaUploadSearchTest extends JmTestCase
{
    // todo rulesのtest追加

    public function testBeforeValidate()
    {
        $this->setIdentity('owner_admin');
        $model = new MediaUploadSearch();
        $model->beforeValidate();
        verify($model->role)->equals('owner_admin');

        $this->setIdentity('corp_admin');
        $model = new MediaUploadSearch();
        $model->beforeValidate();
        verify($model->role)->equals('corp_admin');

        $this->setIdentity('client_admin');
        $model = new MediaUploadSearch();
        $model->beforeValidate();
        verify($model->role)->equals('client_admin');
    }

    /**
     * @param $searchParam
     * @return MediaUpload[]
     */
    private function getMediaUpload($searchParam)
    {
        $model = new MediaUploadSearch();
        $dataProvider = $model->search([$model->formName() => $searchParam]);

        return $dataProvider->query->all();
    }

    /**
     * 検索テスト（運営元及び全権限共通）
     */
    public function testSearchByOwner()
    {
        self::getFixtureInstance('media_upload')->initTable();
        $this->setIdentity('owner_admin');

        /** @var MediaUpload $model */
        $model = MediaUpload::find()->where([
            'or',
            ['not', ['tag' => null]],
            ['not', ['tag' => '']],
        ])->one();

        $tag = $model->tag;

        $this->specify('運営元管理者を選択して検索', function () {
            $models = $this->getMediaUpload([
                'role' => 'owner_admin',
                'disp_file_name' => '',
                'adminMasterName' => '',
                'tag' => '',
            ]);
            verify($models)->notEmpty();
            foreach ($models as $model) {
                verify((string)ArrayHelper::getValue($model, 'client_master_id'))->equals(null);
            }
        });

        $this->specify('掲載企業管理者を選択して検索', function () {
            $models = $this->getMediaUpload([
                'role' => 'client_admin',
                'disp_file_name' => '',
                'adminMasterName' => '',
                'tag' => '',
            ]);
            verify($models)->notEmpty();
            foreach ($models as $model) {
                verify((string)ArrayHelper::getValue($model, 'client_master_id'))->notEquals(null);
            }
        });

        $this->specify('運営元管理者を選択して、ファイル名を指定して検索', function () {
            $models = $this->getMediaUpload([
                'role' => 'owner_admin',
                'disp_file_name' => 's',
                'adminMasterName' => '',
            ]);

            verify($models)->notEmpty();
            foreach ($models as $model) {
                verify((string)ArrayHelper::getValue($model, 'client_master_id'))->equals(null);
                verify((string)ArrayHelper::getValue($model, 'disp_file_name'))->contains('s');
            }
        });

        $this->specify('運営元管理者を選択して、ファイル名・管理者を指定して検索', function () {
            // todo 通らないので通るように修正
            $models = $this->getMediaUpload([
                'role' => 'owner_admin',
                'disp_file_name' => 's',
                'adminMasterName' => '管',
            ]);

            $admins = self::getFixtureInstance('admin_master')->data();
            $admins = ArrayHelper::index($admins, 'id');
            verify($models)->notEmpty();
            foreach ($models as $model) {
                verify((string)ArrayHelper::getValue($model, 'client_master_id'))->equals(null);
                verify((string)ArrayHelper::getValue($model, 'disp_file_name'))->contains('s');
                $adminId = ArrayHelper::getValue($model, 'admin_master_id');
                verify($admins[$adminId]['name_sei'] . $admins[$adminId]['name_mei'])->contains('管');
            }
        });

        $this->specify('掲載企業管理者を選択して、ファイル名・管理者を指定して検索', function () {
            // todo 通らないので通るように修正
            $models = $this->getMediaUpload([
                'role' => 'client_admin',
                'disp_file_name' => 't',
                'adminMasterName' => '管',
            ]);

            $admins = self::getFixtureInstance('admin_master')->data();
            $admins = ArrayHelper::index($admins, 'id');
            verify($models)->notEmpty();
            foreach ($models as $model) {
                verify((string)ArrayHelper::getValue($model, 'client_master_id'))->notEquals(null);
                verify((string)ArrayHelper::getValue($model, 'disp_file_name'))->contains('t');
                $adminId = ArrayHelper::getValue($model, 'admin_master_id');
                verify($admins[$adminId]['name_sei'] . $admins[$adminId]['name_mei'])->contains('管');
            }
        });

        $this->specify('掲載企業画像かつタグ無しで検索する', function () {
            $models = $this->getMediaUpload([
                'role' => 'client_admin',
                'tag' => '0',
            ]);

            verify($models)->notEmpty();
            foreach ($models as $model) {
                verify((string)ArrayHelper::getValue($model, 'client_master_id'))->notEquals(null);
                verify((string)ArrayHelper::getValue($model, 'tag'))->equals(null);
            }
        });

        $this->specify('運営元画像かつタグ無しで検索する', function () {
            $models = $this->getMediaUpload([
                'role' => 'owner_admin',
                'tag' => '0',
            ]);

            verify($models)->notEmpty();
            foreach ($models as $model) {
                verify((string)ArrayHelper::getValue($model, 'client_master_id'))->equals(null);
                verify((string)ArrayHelper::getValue($model, 'tag'))->equals(null);
            }
        });

        $this->specify('掲載企業画像かつ特定のタグで検索する', function () use ($tag) {
            $models = $this->getMediaUpload([
                'role' => 'client_admin',
                'tag' => $tag,
            ]);

            verify($models)->notEmpty();
            foreach ($models as $model) {
                verify((string)ArrayHelper::getValue($model, 'client_master_id'))->notEquals(null);
                verify((string)ArrayHelper::getValue($model, 'tag'))->equals($tag);
            }
        });

        $this->specify('運営元画像かつ特定のタグで検索する', function () use ($tag) {
            $models = $this->getMediaUpload([
                'role' => 'owner_admin',
                'tag' => $tag,
            ]);

            verify($models)->notEmpty();
            foreach ($models as $model) {
                verify((string)ArrayHelper::getValue($model, 'client_master_id'))->equals(null);
                verify((string)ArrayHelper::getValue($model, 'tag'))->equals($tag);
            }
        });
    }

    /**
     * 掲載企業権限での検索テスト
     */
    public function testSearchByClient()
    {
        $this->setIdentity('client_admin');

        $this->specify('運営元管理者を選択して検索', function () {
            $models = $this->getMediaUpload([
                'role' => 'owner_admin',
                'disp_file_name' => '',
                'adminMasterName' => '',
                'tag' => '',
            ]);
            verify($models)->notEmpty();
            foreach ($models as $model) {
                verify((string)ArrayHelper::getValue($model, 'client_master_id'))->equals(null);
            }
        });

        $this->specify('掲載企業管理者を選択して検索', function () {
            $models = $this->getMediaUpload([
                'role' => 'client_admin',
                'disp_file_name' => '',
                'adminMasterName' => '',
                'tag' => '',
            ]);
            verify($models)->notEmpty();
            foreach ($models as $model) {
                verify((string)ArrayHelper::getValue($model, 'client_master_id'))->equals($this->getIdentity()->client_master_id);
            }
        });
    }

    // todo deleteSearchのtest追加

    /**
     * tagDropDownSelectionsのtest
     */
    public function testTagDropDownSelections()
    {
        $this->setIdentity('client_admin');
        $tags = [
            ['tag' => 'owner4', 'client_master_id' => null],
            ['tag' => '', 'client_master_id' => null],
            ['tag' => 'owner2', 'client_master_id' => null],
            ['tag' => 'owner1', 'client_master_id' => null],
            ['tag' => 'owner1', 'client_master_id' => null],
            ['tag' => 0, 'client_master_id' => null],
            ['tag' => 'owner3', 'client_master_id' => null],
            ['tag' => 'client1', 'client_master_id' => $this->getIdentity()->client_master_id],
            ['tag' => 'client2', 'client_master_id' => $this->getIdentity()->client_master_id + 1],
        ];

        $models = MediaUpload::find()->all();
        foreach ($models as $k => $model) {
            /** @var MediaUpload $model */
            if (isset($tags[$k])) {
                $model->tag = $tags[$k]['tag'];
                $model->client_master_id = $tags[$k]['client_master_id'];
            } else {
                $model->tag = null;
                $model->client_master_id = null;
            }
            $model->save(false);
        }

        $this->specify('掲載企業権限とテスト', function () {
            $tagValidate = [
                '' => 'すべて',
                0 => 'タグ無し',
                1 => '0',
                'client1' => 'client1',
                'owner1' => 'owner1',
                'owner2' => 'owner2',
                'owner3' => 'owner3',
                'owner4' => 'owner4',
            ];
            verify(MediaUploadSearch::tagDropDownSelections())->equals($tagValidate);
        });

        $this->specify('運営元権限とテスト', function () {
            $this->setIdentity('owner_admin');
            $tagValidate = [
                '' => 'すべて',
                0 => 'タグ無し',
                1 => '0',
                'client1' => 'client1',
                'client2' => 'client2',
                'owner1' => 'owner1',
                'owner2' => 'owner2',
                'owner3' => 'owner3',
                'owner4' => 'owner4',
            ];
            verify(MediaUploadSearch::tagDropDownSelections())->equals($tagValidate);
        });

        self::getFixtureInstance('media_upload')->initTable();
    }

    /**
     * getTotalFileSizeのtest
     */
    public function testGetTotalFileSize()
    {
        $this->setIdentity('client_admin');
        $datas = [
            ['client_master_id' => null, 'file_size' => 1024],
            ['client_master_id' => null, 'file_size' => 512],
            ['client_master_id' => $this->getIdentity()->client_master_id, 'file_size' => 2048],
            ['client_master_id' => $this->getIdentity()->client_master_id, 'file_size' => 128],
            ['client_master_id' => $this->getIdentity()->client_master_id + 1, 'file_size' => 1024],
        ];
        Yii::$app->db->createCommand()->truncateTable(MediaUploadSearch::tableName())->execute();
        foreach ($datas as $data) {
            (new MediaUploadSearch([
                'disp_file_name' => 'UnitDispFileName',
                'save_file_name' => 'UnitSaveFileName',
                'client_master_id' => $data['client_master_id'],
                'file_size' => $data['file_size'],
            ]))->save(false);
        }
        $recurData = ArrayHelper::index($datas, null, 'client_master_id');

        $clientFileSize = 0;
        foreach ($recurData[$this->getIdentity()->client_master_id] as $fileSize) {
            $clientFileSize += $fileSize['file_size'];
        }
        verify((new MediaUploadSearch())->getTotalFileSize())->equals($clientFileSize);

        $this->setIdentity('owner_admin');
        $ownerFileSize = 0;
        foreach ($recurData as $records) {
            foreach ($records as $record) {
                $ownerFileSize += $record['file_size'];
            }
        }
        verify((new MediaUploadSearch())->getTotalFileSize())->equals($ownerFileSize);

        self::getFixtureInstance('media_upload')->initTable();
    }
}
