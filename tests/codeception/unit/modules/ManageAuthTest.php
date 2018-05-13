<?php
use app\modules\manage\models\Manager;
use app\models\manage\ManagerSession;
use app\modules\manage\models\ManageAuth;
use tests\codeception\unit\JmTestCase;

/**
 * Created by PhpStorm.
 * User: n.katayama
 * Date: 2016/11/03
 * Time: 18:33
 */
class ManageAuthTest extends JmTestCase
{
    public function testRules()
    {
        $this->specify('必須項目検証', function () {
            $model = new ManageAuth();
            $model->validate();
            verify($model->hasErrors('loginId'))->true();
            verify($model->hasErrors('password'))->true();
        });

        $this->specify('validatePassword', function () {
            $model = new ManageAuth();
            $admin = $this->getManager('owner_admin');
            $model->load([
                'loginId' => $admin->login_id,
                'password' => 'aaaaaaa',
            ]);
            $model->validate();
            verify('パスワードが不正なレコード', $model->hasErrors('password'))->true();

            $model = new ManageAuth();
            $model->load([
                'loginId' => 'aaaaaaa',
                'password' => $admin->password,
            ]);
            $model->validate();
            verify('ログインIDが不正なレコード', $model->hasErrors('password'))->true();
        });

        $this->specify('boolean項目検証', function () {
            $model = new ManageAuth();
            $model->rememberMe = 'aaaa';
            $model->validate();
            verify($model->hasErrors('rememberMe'))->true();
            $model = new ManageAuth();
            $model->rememberMe = 999;
            $model->validate();
            verify($model->hasErrors('rememberMe'))->true();
        });

        $this->specify('正しいデータ', function () {
            $admin = $this->getManager('owner_admin');
            $model = new ManageAuth();
            $model->load([
                'loginId' => $admin->login_id,
                'password' => $admin->password,
                'rememberMe' => true,
            ]);
            verify($model->validate())->true();
        });
    }

    // 不要
//    public function testAttributeLabels(){}
//    public function testFormName(){}

    // rulesで検証
//    public function testValidatePassword(){}

    public function testLogin()
    {
        /** @var \yii\web\DbSession $session */
        $session = Yii::$app->session;
        $model = new ManageAuth();
        $admin = $this->getManager('owner_admin');

        // sessionテーブルにこれからログインするidのレコードを書き込む(元々誰かがログインしている状況の再現)
        $session->sessionTable = 'manager_session';
        $session->writeCallback = function () use ($admin) {
            return ['admin_id' => $admin->id];
        };
        $session->writeSession('test', "__flash|a:0:{}__id|i:$admin->id;");

        $model->load([
            'loginId' => $admin->login_id,
            'password' => $admin->password . 'a',
        ]);
        verify('ログイン失敗', $model->login())->false();
        verify('元々ログインしていたユーザーのセッションが残っている', ManagerSession::findOne(['id' => 'test', 'admin_id' => $admin->id]))->notEmpty();

        // ログインする
        $model->load([
            'loginId' => $admin->login_id,
            'password' => $admin->password,
        ]);
        verify('ログイン成功', $model->login())->true();
        verify('正しい管理者でログインされている', Yii::$app->user->id)->equals($admin->id);
        verify('元々ログインしていたユーザーのセッションは消える', ManagerSession::findOne(['id' => 'test', 'admin_id' => $admin->id]))->isEmpty();


    }

    public function testGetManager()
    {
        $model = new ManageAuth();
        $admin = $this->getManager('owner_admin');
        $model->load([
            'loginId' => $admin->login_id,
        ]);

        verify($model->manager)->equals(Manager::findByLoginId($admin->login_id));
    }

    public function testGetRoleNameByUserId()
    {
        /** @var \yii\rbac\DbManager $authManager */
        $authManager = Yii::$app->authManager;

        $this->specify('運営元管理者権限が取得できるかのチェック', function () use($authManager) {
            $admin = self::getManager('owner_admin');
            $name = ManageAuth::getRoleNameByUserId($admin->id);
            verify($name)->equals('owner_admin');
        });

        $this->specify('代理店管理者権限が取得できるかのチェック', function () use($authManager) {
            $admin = self::getManager('corp_admin');
            $name = ManageAuth::getRoleNameByUserId($admin->id);
            verify($name)->equals('corp_admin');
        });

        $this->specify('掲載企業管理者権限が取得できるかのチェック', function () use($authManager) {
            $admin = self::getManager('client_admin');
            $name = ManageAuth::getRoleNameByUserId($admin->id);
            verify($name)->equals('client_admin');
        });
    }
}
