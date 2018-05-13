<?php

namespace models\manage;

use yii;
use tests\codeception\unit\JmTestCase;
use app\models\manage\AdminMaster;
use tests\codeception\unit\fixtures\AuthAssignmentFixture;
use tests\codeception\unit\fixtures\AuthItemChildFixture;
use tests\codeception\unit\fixtures\AuthItemFixture;
use tests\codeception\unit\fixtures\AuthRuleFixture;
use tests\codeception\unit\fixtures\AdminMasterFixture;
use tests\codeception\unit\fixtures\CorpMasterFixture;
use tests\codeception\unit\fixtures\ClientMasterFixture;
use app\modules\manage\models\Manager;
use yii\helpers\ArrayHelper;

/**
 * @property AuthItemChildFixture $auth_item
 * @property AuthItemFixture $auth_item_child
 * @property AuthAssignmentFixture $auth_assignment
 * @property AuthRuleFixture $auth_rule
 * @property AdminMasterFixture $admin_master
 * @property CorpMasterFixture $corp_master
 */
class AdminMasterTest extends JmTestCase
{
    public function testAttributeLabels()
    {
        $model = new AdminMaster();
        verify($model->attributeLabels())->notEmpty();
    }

    /**
     * rules test
     */
    public function testRules()
    {
        $params = [
            'tenant_id' => '1',
            'admin_no' => '1',
            'corp_master_id' => '1',
            'login_id' => 'aaaaa',
            'password' => 'aaaaa',
            'created_at' => '1452092400',
            'valid_chk' => '1',
            'name_sei' => 'test_sei',
            'name_mei' => 'test_mei',
            'tel_no' => '000-0000-0000',
            'client_master_id' => '1',
            'mail_address' => 'test@gmail.com',
            'option100' => 'aaaa',
            'role' => 'owner_admin',
        ];

        $this->specify('通常時の検証', function () use ($params) {
            $model = new AdminMaster();
            $model->load(['AdminMaster' => $params]);
            verify($model->validate())->true();
        });

        $this->specify('ログインID空時の検証', function () use ($params) {
            $model = new AdminMaster();
            $params['login_id'] = '';
            $model->load(['AdminMaster' => $params]);
            verify($model->validate())->false();
        });
        $this->specify('ログインID3文字以下の検証', function () use ($params) {
            $model = new AdminMaster();
            $params['login_id'] = 'aaa';
            $model->load(['AdminMaster' => $params]);
            verify($model->validate())->false();
        });
        $this->specify('ログインID半角英数字以外の文字の検証', function () use ($params) {
            $model = new AdminMaster();
            $params['login_id'] = 'aaaa#';
            $model->load(['AdminMaster' => $params]);
            verify($model->validate())->false();
        });
        $this->specify('すでに登録してあるログインIDを入力した時の検証', function () use ($params) {
            $model = new AdminMaster();
            $data = self::getFixtureInstance('admin_master')->data();
            $fixtureLoginId = $data[0]['login_id'];
            $params['login_id'] = $fixtureLoginId;
            $model->load(['AdminMaster' => $params]);
            verify($model->validate())->false();
        });

        $this->specify('パスワード空時の検証', function () use ($params) {
            $model = new AdminMaster();
            $params['password'] = '';
            $model->load(['AdminMaster' => $params]);
            verify($model->validate())->false();
        });
        $this->specify('パスワード3文字以下の検証', function () use ($params) {
            $model = new AdminMaster();
            $params['password'] = 'aaa';
            $model->load(['AdminMaster' => $params]);
            verify($model->validate())->false();
        });
        $this->specify('パスワード半角英数字以外の文字の検証', function () use ($params) {
            $model = new AdminMaster();
            $params['password'] = 'aaaa#';
            $model->load(['AdminMaster' => $params]);
            verify($model->validate())->false();
        });

        $this->specify('メールアドレスでない時の検証', function () use ($params) {
            $model = new AdminMaster();
            $params['mail_address'] = 'test';
            $model->load(['AdminMaster' => $params]);
            verify($model->validate())->false();
        });
        $this->specify('メールアドレス空時の検証', function () use ($params) {
            $model = new AdminMaster();
            $params['mail_address'] = '';
            $model->load(['AdminMaster' => $params]);
            verify($model->validate())->false();
        });
        $this->specify('すでに登録してあるメールを入力した時の検証', function () use ($params) {
            $model = new AdminMaster();
            $data = self::getFixtureInstance('admin_master')->data();
            $params['mail_address'] = $data[0]['mail_address'];
            $model->load(['AdminMaster' => $params]);
            verify($model->validate())->false();
        });

        $this->specify('部署名空時の検証', function () use ($params) {
            $model = new AdminMaster();
            $params['name_sei'] = '';
            $model->load(['AdminMaster' => $params]);
            verify($model->validate())->false();
        });
        $this->specify('担当者名空時の検証', function () use ($params) {
            $model = new AdminMaster();
            $params['name_mei'] = '';
            $model->load(['AdminMaster' => $params]);
            verify($model->validate())->false();
        });
        $this->specify('状態空時の検証', function () use ($params) {
            $model = new AdminMaster();
            $params['valid_chk'] = '';
            $model->load(['AdminMaster' => $params]);
            verify($model->validate())->false();
        });
    }

    public function testRulesByOwner()
    {
        $params = [
            'tenant_id' => '1',
            'admin_no' => '1',
            'corp_master_id' => '1',
            'login_id' => 'aaaaa',
            'password' => 'aaaaa',
            'created_at' => '1452092400',
            'valid_chk' => '1',
            'name_sei' => 'test_sei',
            'name_mei' => 'test_mei',
            'tel_no' => '000-0000-0000',
            'client_master_id' => '1',
            'mail_address' => 'test@gmail.com',
            'option100' => 'aaaa',
            'role' => 'owner',
        ];

        $this->specify('運営管理者権限での、代理店ID・掲載企業IDの検証', function () use ($params) {
            $model = new AdminMaster();
            $params['corpMaster'] = null;
            $params['clientMaster'] = null;
            $model->load(['AdminMaster' => $params]);
            verify($model->validate())->true();
        });
    }

    public function testRulesByCorp()
    {
        $params = [
            'tenant_id' => '1',
            'admin_no' => '1',
            'corp_master_id' => '1',
            'login_id' => 'aaaaa',
            'password' => 'aaaaa',
            'created_at' => '1452092400',
            'valid_chk' => '1',
            'name_sei' => 'test_sei',
            'name_mei' => 'test_mei',
            'tel_no' => '000-0000-0000',
            'client_master_id' => '1',
            'mail_address' => 'test@gmail.com',
            'option100' => 'aaaa',
            'role' => 'corp_admin',
        ];

        $this->specify('代理店管理者権限での、代理店IDの検証', function () use ($params) {
            $model = new AdminMaster();
            $params['corp_master_id'] = null;
            $model->load(['AdminMaster' => $params]);
            verify($model->validate())->false();
        });

        $this->specify('代理店管理者権限での、掲載企業IDの検証', function () use ($params) {
            $model = new AdminMaster();
            $params['client_master_id'] = null;
            $model->load(['AdminMaster' => $params]);
            verify($model->validate())->true();
        });
    }

    /**
     * ロードテスト
     */
    public function testLoad()
    {
        $params = [
            'AdminMaster' => [
                'corp_master_id' => 1,
                'client_master_id' => 1,
            ]
        ];

        $this->specify('ロード時の種別セットテスト（管理者No.がない場合）', function () use ($params) {
            $adminMaster = new AdminMaster();
            $adminMaster->load($params);
            $adminMaster->admin_no = 0;
        });

        $this->specify('ロード時の種別セットテスト（管理者No.がない場合）', function () use ($params) {
            $params['admin_no'] = 100;
            $adminMaster = new AdminMaster();
            $adminMaster->load($params);
            $adminMaster->admin_no = 100;
        });
    }

    /**
     * ロード時の運営管理者権限の種別セットテスト
     */
    public function testLoadByOwner()
    {
        $params = [
            'AdminMaster' => [
                'role' => 'owner_admin',
                'corp_master_id' => 1,
                'client_master_id' => 1,
            ]
        ];

        $this->specify('ロード時の運営管理者権限の種別セットテスト', function () use ($params) {
            $adminMaster = new AdminMaster();
            $adminMaster->load($params);
            verify($adminMaster->corp_master_id)->null();
            verify($adminMaster->client_master_id)->null();
        });
    }

    /**
     * ロード時の代理店管理者権限の種別セットテスト
     */
    public function testLoadByCorp()
    {
        $params = [
            'AdminMaster' => [
                'role' => 'corp_admin',
                'corp_master_id' => 1,
                'client_master_id' => 1,
            ]
        ];

        $this->specify('ロード時の代理店管理者権限の種別セットテスト', function () use ($params) {
            $adminMaster = new AdminMaster();
            $adminMaster->load($params);
            verify($adminMaster->corp_master_id)->notnull();
            verify($adminMaster->client_master_id)->null();
        });
    }

    /**
     * 権限の保存テスト
     */
    public function testExceptions()
    {
        $data = self::getFixtureInstance('auth_assignment')->data();
        $id = $data[$this->id(0, 'auth_assignment')]['user_id'];
        $targetRecords = $data;
        $targetRecords = array_filter($targetRecords, function ($record) use ($id){
            return $record['user_id'] == $id;
        });
        $adminMaster = AdminMaster::findOne(['id' => $id]);
        $this->specify('', function () use ($adminMaster, $targetRecords) {
            verify(array_values($adminMaster->getExceptions()))->equals(array_values(ArrayHelper::getColumn($targetRecords, 'item_name')));
        });
    }

    /**
     * 権限の保存テスト
     */
    public function testSaveAuthExceptions()
    {
        //テストデータ
        $params = [
            'AdminMaster' => [
                'tenant_id' => '1',
                'admin_no' => '1',
                'corp_master_id' => '1',
                'login_id' => 'aaaaa',
                'password' => 'aaaaa',
                'created_at' => '1452092400',
                'valid_chk' => '1',
                'name_sei' => 'test_sei',
                'name_mei' => 'test_mei',
                'tel_no' => '000-0000-0000',
                'client_master_id' => '1',
                'mail_address' => 'test@gmail.com',
                'option100' => 'aaaa',
                'role' => 'owner_admin',
                'exceptions' => [
                    'clientListException',
                    'clientCreateException',
                ],
            ],
        ];

        $adminMaster = new AdminMaster();
        $adminMaster->load($params);

        $this->specify('管理者の保存', function () use ($adminMaster) {
            verify($adminMaster->save())->true();
        });
        $this->specify('除外する管理権限の保存', function () use ($adminMaster, $params) {
            $adminMaster->saveAuthExceptions($params);
            //ロールの保存も含まれるのでrole + permission = 3です。
            verify(count($adminMaster->exceptions))->equals(3);
        });

        // saveしたレコードを元に戻す
        self::getFixtureInstance('admin_master')->load();
    }
}